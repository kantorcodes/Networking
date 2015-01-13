<?php namespace Drapor\Networking;

/**
 * Created by PhpStorm.
 * User: michaelkantor
 * Date: 12/29/14
 * Time: 2:57 PM
 */
use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Events\Dispatcher;
use Drapor\Networking\Traits\TimeElapsed;

class Networking
{
    use TimeElapsed;
    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @var string
     */
    public $scheme;

    /**
     * @var string
     */
    public $proxy;

    /**
     * @var array
     */
    public $auth;

    /**
     * @var array
     */
    public $headers;


    /**
     * @var array $options
     */
    public $options = [
        'body'            => false,
        'query'           => false,
        'allow_redirects' => false,
        'auth'            => false
    ];

    /** @var $body array * */
    protected $body;

    /** @var $status_code Int * */
    protected $status_code;

    /** @var $response ResponseInterface * */
    protected $response;

    /** @var $request RequestInterface * */
    protected $request;

    /** @var array $cookies * */
    protected $cookies;

    /** @var string $url * */
    protected $url;

    /** @var $events Dispatcher * */
    protected $events;


    function __construct()
    {
        $this->events = app('events');
    }

    /**
     * If you want to encode any body or query parameters, authenticate or set
     * redirect settings then you would call this method to set
     * a new array of options before calling send()
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * Unless $fields['body'] or $fields['query'] is specified, they will not
     * be sent in the http request.
     * @param $fields
     * @param $endpoint
     * @param $type
     * @return array
     */
    public function send(array $fields, $endpoint, $type)
    {
        try {
            $this->createRequest($fields, $endpoint, $type);
        } catch (RequestException $e) {
            $this->setResponse($e->getResponse());
        }

        $body        = $this->getBody();
        $status_code = $this->getStatusCode();
        $cookie      = $this->getCookies();

        $response = [
            'body' => $body,
            'status_code' => $status_code,
            'cookie' => $cookie
        ];

        return $response;
    }

    /**
     * @param array  $fields
     * @param        $endpoint
     * @param string $type
     *
     * @return void
     */
    private function createRequest(array $fields = [], $endpoint, $type = "get")
    {

        $this->setStartedAt();
        $this->setUrl($this->baseUrl . $endpoint);


        $client = $this->getClient();
        $jar    = $this->getCookieJar();
        $url    = $this->getUrl();
        $opts   = $this->configureRequest($fields, $jar);

        $request  = $client->createRequest($type, $url, $opts);

        /** $response RequestInterface * */
        $response = $client->send($request);

        $this->setRequest($request);
        $this->setResponse($response);
        $this->setCookies($jar);
    }

    /**
     * @param $fields
     * @param $endpoint
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function createStreamRequest(array $fields, $endpoint)
    {
        $body = json_encode($fields);

        $guzzle = $this->getClient();

        $req = $guzzle->createRequest('POST', $endpoint);
        $req->setScheme($this->scheme);
        $req->setBody(Stream::factory($body));
        /** $response RequestInterface * */
        $response = $guzzle->send($req);

        return $response;
    }

    /**
     * @param array $fields
     * @param       $jar
     *
     * @return array
     */
    private function configureRequest(array $fields, $jar)
    {

        $opts = [
            'headers' => $this->headers,
            'cookies' => $jar
        ];

        if (!empty($fields)) {
            $config = $this->getOptions();
            if ($config['body']) {
                $opts['body'] = $fields;
            }
            if ($config['query']) {
                $opts['query'] = $fields;
            }
            if($config['allow_redirects']){
                $opts['allow_redirects'] = [
                    'max'       => 10,
                    'strict'    => true,
                    'referer'   => true,
                    'protocols' => [$this->scheme]
                ];
            }
        }
        return $opts;
    }


    /**
     * @return Client
     */
    private function getClient()
    {

        $defaults = array();

        if (!empty($this->proxy)) {
            $defaults['proxy'] = $this->proxy;
        }
        if (!empty($this->auth)) {
            $defaults['auth'] = $this->auth;
        }

        $guzzle = new Client([
            'base_url' => $this->url,
            'defaults' => $defaults
        ]);

        return $guzzle;
    }

    private function getCookieJar()
    {
        return new CookieJar;
    }

    /**
     * @return string
     */
    private function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    private function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    private function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param CookieJar $jar
     */
    private function setCookies($jar)
    {
        $jar->extractCookies($this->getRequest(), $this->getResponse());
        $this->cookies = $jar->toArray();
        $payload = [
            'status_code'  => $this->getStatusCode(),
            'body'         => json_encode($this->getBody()),
            'url'          => $this->getUrl(),
            'headers'      => json_encode($this->headers),
            'cookies'      => json_encode($this->getCookies()),
            'time_elapsed' => $this->getTimeElapsed()
        ];

        $this->events->fire('networking.response.created', [$payload]);

    }

    /**
     * @return array
     */
    private function getOptions()
    {
        return $this->options;
    }

    /**
     * @return RequestInterface
     */
    private function getRequest()
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     */
    private function setRequest($request)
    {
        $this->request = $request;
    }


    /**
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    private function getResponse()
    {
        return $this->response;
    }

    /**
     * Set the response & related info from the response.
     * @param ResponseInterface $response
     */
    private function setResponse($response)
    {
        $this->setEndedAt();
        $this->setBody(json_decode($response->getBody(),true));
        $this->setStatusCode($response->getStatusCode());
        $this->response = $response;

    }

    /**
     * @return Dispatcher
     */
    private function getDispatcher()
    {
        return $this->events;
    }

    /**
     * @return Int
     */
    private function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * @param Int $status_code
     */
    private function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
    }

    /**
     * @return array
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    private function setBody(array $body)
    {
        $this->body = $body;
    }

    /**
     *
     */
    private function setDispatcher()
    {
        $this->events = new Dispatcher();

    }


}
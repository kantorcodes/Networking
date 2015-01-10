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
use Log;

class Networking{

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
	 * @var array
	 */
	public $options  = [
			'body' => false,
			'query' => false
		];

	/** @var $response ResponseInterface **/
	protected $response;

	/** @var $request RequestInterface **/
	protected $request;

	/** @var array $cookies **/
	protected $cookies;

	/** @var string $url **/
	protected $url;

	/**
	 * @param Client $client
     * @param Dispatcher $dispatcher
	 */
	public function __construct(Client $client, Dispatcher $dispatcher)
	{
			$this->guzzle     = $client;
			$this->dispatcher = $dispatcher;
	}

	/**
	 * If you want to encode any body or query parameters
	 * then you call this method to set a new array of options.
     * @param array $options
	 */
	public function setOptions( array $options ){
			$this->options = $options;
	}

	/**
	 * @param array $fields
	 * @param $endpoint
	 * @param string $type
	 *
     * @return void
	 */
	private function createRequest(array $fields = [],$endpoint,$type = "get"){

		\Log::info("Logging requests headers..");
		\Log::info($this->headers);

        $this->setUrl($this->baseUrl.$endpoint);

		$client = $this->getClient();
		$jar    = $this->getCookieJar();
        $url    = $this->getUrl();
		$opts   = $this->configureRequest( $fields, $jar );

		$request  = $client->createRequest($type,$url,$opts);
		$response = $client->send($request);

		$this->setRequest($request);
		$this->setResponse($response);
        $this->setCookies($jar);
	}

     /**
     * Unless $fields['body'] or $fields['query'] is specified, they will not
     * be sent in the http request.
     * @param $fields
     * @param $endpoint
     * @param $type
     * @return array
     */
    public function send( array $fields,$endpoint,$type ) {
        try {

            $this->createRequest( $fields,$endpoint,$type);
            $res = $this->getResponse();

            //Set Status Code + Body For Ok Response
            $body = json_decode($res->getBody());
            $status_code = $res->getStatusCode();

        }catch(RequestException $e){

            $res = $e->getResponse();
            $body = json_decode($res->getBody());
            $status_code = $res->getStatusCode();

        }

        $cookie = $this->getCookies();

        $response =  [
            'body'        => $body,
            'status_code' => $status_code,
            'cookie'      => $cookie
        ];

        return $response;
    }

    /**
     * @return Client
     */
    private function getClient() {

        $defaults = array();

        if(isset($this->proxy)){
            $defaults['proxy'] = $this->proxy;
        }
        if(isset($this->auth)){
            $defaults['auth'] = $this->auth;
        }

        $guzzle = new Client( [
            'base_url' => $this->url,
            'defaults' => $defaults
        ]);

        return $guzzle;
    }

	private function getCookieJar() {
		return  new CookieJar;
	}

	/**
	 * @param array $fields
	 * @param $jar
	 *
     * @return array
	 */
	private function configureRequest( array $fields, $jar ) {

		$opts = [
				'headers' => $this->headers,
				'cookies' => $jar
		];

		if ( ! empty( $fields ) ) {
            $config = $this->getOptions();
			if($config['body']){
				$opts['body'] = $fields;
			}
			if($config['query']){
				$opts['query'] = $fields;
			}
		}
		   return $opts;
	}

	/**
     * @return array
	 */
	private function getOptions() {
		return $this->options;
	}

	/**
	 * @param RequestInterface $request
	 */
	private function setRequest( $request ) {
		$this->request = $request;
	}

	/**
	 * @param ResponseInterface $response
	 */
	private function setResponse( $response ) {
		$this->response = $response;
	}

	/**
	 * @param CookieJar $jar
	 */
	private function setCookies( $jar ) {
		$jar->extractCookies($this->request, $this->response);
		$this->cookies = $jar->toArray();

        $this->dispatcher->fire('response.created',[
            'status_code' => $this->response->getStatusCode(),
            'body'        => $this->response->getBody(),
            'url'         => $this->url,
            'headers'     => $this->headers,
            'cookies'     => $this->cookies
        ]);

	}

	/**
	 * @return \GuzzleHttp\Message\ResponseInterface
	 */
	private function getResponse() {
		return $this->response;
	}

	/**
	 * @return array
	 */
	private function getCookies() {
		Log::info("Logging requests cookies...");
		Log::info($this->cookies);
		return $this->cookies;
	}

	/**
     * @param string $url
	 */
	private function setUrl( $url ) {
		$this->url = $url;
	}

	/**
	 * @param $fields
	 * @param $endpoint
     * @param $proxy
	 * @return \GuzzleHttp\Message\ResponseInterface
	 */
	public function createStreamRequest(array $fields,$endpoint,$proxy = null){
		$body = json_encode($fields);

		$guzzle = $this->getClient();

		$req = $guzzle->createRequest('POST', $endpoint);
		$req->setScheme($this->scheme);
		$req->setBody(Stream::factory($body));

		$response = $guzzle->send($req);

		return $response;
	}

    /**
     * @return RequestInterface
     */
    private function getRequest() {
        return $this->request;
    }

    /**
     * @return string
     */
    private function getUrl() {
        return $this->url;
    }
}
<?php namespace Drapor\Services;
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

	class Service{

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
		public $headers;

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
		private function __construct(Client $client, Dispatcher $dispatcher)
		{
			$this->guzzle     = $client;
			$this->dispatcher = $dispatcher;
		}

		/**
		 * @return \GuzzleHttp\Message\ResponseInterface
		 */
		private function getResponse() {
			return $this->response;
		}

		/**
		 * @param ResponseInterface $response
		 */
		private function setResponse( $response ) {
			$this->response = $response;
			Log::info($this->response->getBody());

			$this->dispatcher->fire('response.created',[
				'status_code' => $response->getStatusCode(),
				'body'        => $response->getBody(),
				'url'         => $this->url,
				'headers'     => $this->headers,
				'cookies'     => $this->cookies
			]);
		}

		/**
		 * @return RequestInterface
		 */
		private function getRequest() {
			return $this->request;
		}

		/**
		 * @param RequestInterface $request
		 */
		private function setRequest( $request ) {
			$this->request = $request;
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
		 * @param CookieJar $jar
		 */
		private function setCookies( $jar ) {
			$jar->extractCookies($this->request, $this->response);
			$this->cookies = $jar->toArray();
		}

		/**
		 * @return string
		 */
		private function getUrl() {
			return $this->url;
		}

		/**
		 * @param string $url
		 */
		private function setUrl( $url ) {
			$this->url = $url;
		}


		/**
		 * Unless $fields['body'] or $fields['query'] is specified, they will not
		 * be sent in the http request.
		 * @param $fields
		 * @param $endpoint
		 * @param $type
		 * @return array
		 */
		public function send( array $fields, $endpoint,$type ) {
			try {
				$this->createRequest( $fields, $endpoint,$type);
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
			$this->setUrl($res->getEffectiveUrl());

			$response =  [
				'body'        => $body,
				'status_code' => $status_code,
				'cookie'      => $cookie
			];

			return $response;
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

			$client = $this->getClient();
			$jar    = $this->getCookieJar();

			$url = $this->baseUrl.$endpoint;
			$opts = $this->getOptions( $fields, $jar );

			$request  = $client->createRequest($type,$url,$opts);
			$response = $client->send($request);

			$this->setRequest($request);
			$this->setResponse($response);
			$this->setCookies($jar);
		}



		/**
		 * @param null $proxy
		 *
		 * @return ResponseInterface|void
		 */
		public function checkIp($proxy = null){
			$this->createRequest(["time" => time(), 'body' => false, 'query' => false],'/getIp',$proxy);

			$res = $this->getRequest();

			return $res->getBody();
		}

		/**
		 * @param $fields
		 * @param $endpoint
		 * @param $proxy
		 * @return \GuzzleHttp\Message\ResponseInterface
		 */
		public function createStreamRequest(array $fields,$endpoint,$proxy = null)
		{
			$body = json_encode($fields);

			$guzzle = new Client([
				'base_url' => $this->url,
				'headers'  => $this->headers,
				'proxy'    => $proxy
			]);

			$req = $guzzle->createRequest('POST', $endpoint);
			$req->setScheme($this->scheme);
			$req->setBody(Stream::factory($body));

			$response = $guzzle->send($req);

			return $response;
		}


		/**
		 * @return Client
		 */
		private function getClient() {
			$guzzle = new Client( [
				'base_url' => $this->url,
				'defaults' => [
					'proxy' => $this->proxy,
				],
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
		private function getOptions( array $fields, $jar ) {

			$opts = [
				'headers' => $this->headers,
				'cookies' => $jar
			];

			if ( ! empty( $fields ) ) {

				if($fields['body']){
					$opts['body'] = $fields;
				}
				if($fields['query']){
					$opts['query'] = $fields;
				}

			}
			return $opts;
		}


	}
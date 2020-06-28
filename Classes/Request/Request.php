<?php


namespace Request;


use DiDom\Document;
use FileSystem\Cookies;
use Resources\Student;

class Request
{
	/** @var string */
	private $url;

	/** @var Cookies */
	private $cookies;

	/** @var array */
	private $post_fields;

	/** @var resource */
	private $channel;

	/** @var Document */
	private $response;

	/**
	 * Request constructor.
	 * @param $url
	 * @param array $post_fields
	 * @throws CurlErrorException
	 */
	public function __construct($url, $post_fields = [])
	{
		$this->url = $url;
		$this->post_fields = $post_fields;
		$this->cookies = Student::getInstance()->cookies();

		$this->make();
	}

	/**
	 * @throws CurlErrorException
	 */
	private function make()
	{
		$this->channel = curl_init();

		if(is_array($this->post_fields) && count($this->post_fields) > 0)
		{
			curl_setopt($this->channel, CURLOPT_POST, 1);
			curl_setopt($this->channel, CURLOPT_POSTFIELDS, $this->post_fields);
		}

		curl_setopt($this->channel, CURLOPT_URL, $this->url);
		curl_setopt($this->channel, CURLOPT_HEADER, 0);
		curl_setopt($this->channel, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->channel, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->channel, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($this->channel, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($this->channel, CURLOPT_COOKIEJAR, $this->cookies->getFullPath());
		curl_setopt($this->channel, CURLOPT_COOKIEFILE, $this->cookies->getFullPath());

		$html = curl_exec($this->channel);

		if(curl_errno($this->channel)) throw new CurlErrorException (curl_error($this->channel));

		curl_close($this->channel);

		$this->response = new Document($html);
	}

	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return Document
	 */
	public function response()
	{
		return $this->response;
	}
}
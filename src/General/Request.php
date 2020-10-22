<?php


namespace MoodleParser\General;


use DiDom\Document;
use MoodleParser\FileSystem\Cookies;

class Request
{
	/** @var string */
	private $url;

	/** @var Cookies */
	private $cookies = false;

	/** @var array */
	private $post_fields;

    /** @var Document */
	private $response;

	/**
	 * Request constructor.
	 * @param $url
	 * @param array $post_fields
	 * @param Cookies $cookies
	 */
	public function __construct($url, $post_fields = [], Cookies $cookies = null)
	{
		$this->url = $url;
		$this->post_fields = $post_fields;

		if( isset($cookies) )
			$this->cookies = $cookies;

		$this->make();
	}

	private function make()
	{
		$channel = curl_init();

		if(is_array($this->post_fields) && count($this->post_fields) > 0)
		{
			curl_setopt($channel, CURLOPT_POST, 1);
			curl_setopt($channel, CURLOPT_POSTFIELDS, $this->post_fields);
		}

		curl_setopt($channel, CURLOPT_URL, $this->url);
		curl_setopt($channel, CURLOPT_HEADER, 0);
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($channel, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);

		if($this->cookies != false)
		{
			curl_setopt($channel, CURLOPT_COOKIEJAR, $this->cookies->getFullPath());
			curl_setopt($channel, CURLOPT_COOKIEFILE, $this->cookies->getFullPath());
		}

		$html = curl_exec($channel);

		//TODO Make Logging function for errors
		/**
		 * if(curl_errno($this->channel))
		*/
		curl_close($channel);

		$this->response = new Document($html);
	}

	/**
	 * @return Document
	 */
	public function response()
	{
		return $this->response;
	}
}
<?php


namespace General;


use DiDom\Document;
use FileSystem\Cookies;
use Resources\Student;
use General\Exceptions\CurlErrorException;

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

	/**
	 * @param $login
	 * @param $password
	 * @return bool|Request
	 */
	public static function Login($login, $password)
	{
		$request = false;
		try {
			$request = new Request(
				Properties::$login_url,
				[
					"username" => $login,
					"password" => $password
				]
			);
		}
		catch (CurlErrorException $e) { Signal::msg($e->getMessage()); }

		return $request;
	}

	/**
	 * @param $session_key
	 * @param $cmid
	 * @param bool $timer_exist
	 * @return bool|Request
	 */
	public static function StartAttempt($session_key, $cmid, $timer_exist = false)
	{
		$post_fields[ "cmid" ] = $cmid;
		$post_fields[ "sesskey" ] = $session_key;

		if( $timer_exist === true)
		{
			$post_fields["_qf__mod_quiz_preflight_check_form"] = true;
			$post_fields["submitbutton"] = "Почати спробу";
		}

		$request = false;

		try {
			$request = new Request(
				Properties::$start_attempt,
				$post_fields
			);
		}
		catch (CurlErrorException $e) { Signal::msg($e->getMessage()); }

		return $request;
	}

	/**
	 * @param array $form_fields
	 * @return bool|Request
	 */
	public static function ProcessAttempt(array $form_fields)
	{
		// TODO Checking required fields
		$request = false;

		try {
			$request = new Request(
				Properties::$process_attempt,
				$form_fields
			);
		}
		catch (CurlErrorException $e) { Signal::msg($e->getMessage()); }

		return $request;
	}
}
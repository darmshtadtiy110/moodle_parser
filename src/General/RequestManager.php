<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Cookies;

class RequestManager
{
	private $cookies;

	public function __construct(Cookies $cookies)
	{
		$this->cookies = $cookies;
	}

	/**
	 * @param $login
	 * @param $password
	 * @param $token
	 * @return bool|Request
	 */
	public function login($login = null, $password = null, $token = null)
	{
		return new Request(
			Properties::login(),
			[
				"anchor" => "",
				"username" => $login,
				"password" => $password,
				"logintoken" => $token
			],
			$this->cookies
		);
	}

	public function homepage()
	{
		return new Request(
			Properties::homepage(),
			[],
			$this->cookies
		);
	}

	public function course($id)
	{
		return new Request(
			Properties::Course().$id,
			[],
			$this->cookies
		);
	}

	public function quiz($id)
	{
		return new Request(
			Properties::Quiz().$id,
			[],
			$this->cookies
		);
	}

	/**
	 * @param $session_key
	 * @param $cmid
	 * @param bool $timer_exist
	 * @return bool|Request
	 */
	public function startAttempt($session_key, $cmid, $timer_exist = false)
	{
		$post_fields[ "cmid" ] = $cmid;
		$post_fields[ "sesskey" ] = $session_key;

		if( $timer_exist === true)
		{
			$post_fields["_qf__mod_quiz_preflight_check_form"] = true;
			$post_fields["submitbutton"] = "Почати спробу";
		}

		return new Request(
			Properties::start_attempt(),
			$post_fields,
			$this->cookies
		);
	}

	/**
	 * @param array $form_fields
	 * @return Request
	 */
	public function processAttempt(array $form_fields)
	{
		return new Request(
			Properties::process_attempt(),
			$form_fields,
			$this->cookies
		);
	}

	public function finishAttempt($attempt_id)
	{
		return new Request(
			Properties::summary_form().$attempt_id,
			[],
			$this->cookies
		);
	}

	public function attemptReview($attempt_id)
	{
		return new Request(
			Properties::attempt_review().$attempt_id,
			[],
			$this->cookies
		);
	}
}
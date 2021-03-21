<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Cookies;

class RequestManager
{
	private static $homepage = "/my";

	private static $user_profile = "/user/profile.php?id=";

	private static $login_url = "/login/index.php";

	private static $course = "/course/view.php?id=";

	private static $quiz = "/mod/quiz/view.php?id=";

	private static $start_attempt = "/mod/quiz/startattempt.php";

	private static $process_attempt = "/mod/quiz/processattempt.php";

	private static $processing_summary_form = "/mod/quiz/summary.php?attempt=";

	private static $attempt_review = "/mod/quiz/review.php?attempt=";

	private static $toggle_completions = "/course/togglecompletion.php";

	private $cookies;

	/** @var String */
	private $session_key;

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
			self::$login_url,
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
			self::$homepage,
			[],
			$this->cookies
		);
	}

	public function course($id)
	{
		return new Request(
			self::$course.$id,
			[],
			$this->cookies
		);
	}

	public function quiz($id)
	{
		return new Request(
			self::$quiz.$id,
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
			self::$start_attempt,
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
			self::$process_attempt,
			$form_fields,
			$this->cookies
		);
	}

	public function finishAttempt($attempt_id)
	{
		return new Request(
			self::$processing_summary_form.$attempt_id,
			[],
			$this->cookies
		);
	}

	public function attemptReview($attempt_id)
	{
		return new Request(
			self::$attempt_review.$attempt_id,
			[],
			$this->cookies
		);
	}

	public function userProfile($user_id)
	{
		return new Request(
			self::$user_profile.$user_id,
			[],
			$this->cookies
		);
	}

	public function toggleCompletion($id, $name)
	{
		return new Request(
			self::$toggle_completions,
			[
				"id" => $id,
				"sesskey" => $this->session_key,
				"modulename" => $name,
				"completionstate" => "1"
			],
			$this->cookies
		);
	}

	/**
	 * @param String $session_key
	 */
	public function setSessionKey($session_key)
	{
		$this->session_key = $session_key;
	}

	/**
	 * @return String
	 */
	public function getSessionKey()
	{
		return $this->session_key;
	}
}
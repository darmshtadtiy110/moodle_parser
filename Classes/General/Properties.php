<?php


namespace General;


class Properties
{
	public static $target_url = "http://nip.tsatu.edu.ua";

	public static $login_url = "/login/index.php";

	public static $course = "/course/view.php?id=";

	public static $quiz = "/mod/quiz/view.php?id=";

	public static $start_attempt = "/mod/quiz/startattempt.php";

	public static $process_attempt = "/mod/quiz/processattempt.php";

	public static function login()
	{
		return self::$target_url.self::$login_url;
	}

	public static function start_attempt()
	{
		return self::$target_url.self::$start_attempt;
	}

	public static function process_attempt()
	{
		return self::$target_url.self::$process_attempt;
	}
}
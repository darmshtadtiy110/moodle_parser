<?php


namespace MoodleParser\General;


class Properties
{
	private static $target_url = "http://op.tsatu.edu.ua";

	private static $homepage = "/my";

	private static $user_profile = "/user/profile.php?id=";

	private static $login_url = "/login/index.php";

	private static $course = "/course/view.php?id=";

	private static $quiz = "/mod/quiz/view.php?id=";

	private static $start_attempt = "/mod/quiz/startattempt.php";

	private static $process_attempt = "/mod/quiz/processattempt.php";

	private static $processing_summary_form = "/mod/quiz/summary.php?attempt=";

	private static $attempt_review = "/mod/quiz/review.php?attempt=";

	public static function login()
	{
		return self::$target_url.self::$login_url;
	}

    /**
     * @return string
     */
    public static function Homepage(): string
    {
        return self::$target_url.self::$homepage;
    }

	public static function Profile()
	{
		return self::$target_url.self::$user_profile;
	}

	public static function start_attempt()
	{
		return self::$target_url.self::$start_attempt;
	}

	public static function process_attempt()
	{
		return self::$target_url.self::$process_attempt;
	}

	public static function summary_form()
	{
		return self::$target_url.self::$processing_summary_form;
	}

	public static function Course()
	{
		return self::$target_url.self::$course;
	}

	public static function Quiz()
	{
		return self::$target_url.self::$quiz;
	}

	public static function attempt_review()
	{
		return self::$target_url.self::$attempt_review;
	}
}
<?php


namespace Request;


class StartAttempt extends Request
{
	public function __construct($cookies, $session_key, $cmid, $timer_exist = false)
	{
		$post_fields[ "cmid" ] = $cmid;
		$post_fields[ "sesskey" ] = $session_key;

		if( $timer_exist === true)
		{
			$post_fields["_qf__mod_quiz_preflight_check_form"] = true;
			$post_fields["submitbutton"] = "Почати спробу";
		}

		parent::__construct(
			Properties::$start_attempt,
			$cookies,
			$post_fields
			);
	}
}
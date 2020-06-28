<?php


namespace Request;


class StartAttempt extends Request
{
	/**
	 * StartAttempt constructor.
	 * @param $session_key
	 * @param $cmid
	 * @param bool $timer_exist
	 * @throws CurlErrorException
	 */
	public function __construct($session_key, $cmid, $timer_exist = false)
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
			$post_fields
		);
	}
}
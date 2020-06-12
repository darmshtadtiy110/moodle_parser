<?php


namespace Factory;

use General\Request;
use Resources\ProcessingAttempt;

class AttemptFactory
{
	/**
	 * @param $id - Is cmid parameter from attempt list page
	 * @param $session_key - Current user session key
	 * @param $timer_exist - If test is time limited then param is true
	 * @return ProcessingAttempt
	 */
	public static function CreateProcessed($id, $session_key, $timer_exist)
	{
		$post_fields[ "cmid" ] = $id;
		$post_fields[ "sesskey" ] = $session_key;

		if( $timer_exist )
		{
			$post_fields["_qf__mod_quiz_preflight_check_form"] = true;
			$post_fields["submitbutton"] = "Почати спробу";
		}

		$new_attempt_page = Request::StartAttempt($post_fields);

		return new ProcessingAttempt($new_attempt_page, $id."_".$session_key);
	}

	public static function CreateFinished($attempt_data_array)
	{

	}
}
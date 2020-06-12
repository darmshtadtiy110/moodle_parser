<?php


namespace Factory;

use General\Request;
use DiDom\Document;
use Exception;
use Resources\Quiz;

class QuizFactory
{
	public static function Create($quiz_data_array)
	{
		$quiz_start_page = Request::Page($quiz_data_array["link"]);

		$session_key = self::ParseSessionKey($quiz_start_page);
		$quiz_id = self::ParseQuizId($quiz_start_page);
		$timer_exist = self::ParseTimer($quiz_start_page);
		$attempt_list = self::ParseAttemptList($quiz_start_page);


	}


}

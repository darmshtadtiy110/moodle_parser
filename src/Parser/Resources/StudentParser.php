<?php


namespace MoodleParser\Parser\Resources;


use DiDom\Exceptions\InvalidSelectorException;
use MoodleParser\General\Signal;
use MoodleParser\Parser\Exceptions\ExpressionNotFound;
use MoodleParser\Parser\Parser;

class StudentParser extends Parser
{
	/**
	 * @return string|bool
	 */
	public function getLoginResults()
	{
		$login_info_nodes = $this->find(".logininfo");

		if ( empty($login_info_nodes) ) return true;
		else return $login_info_nodes[0]->text();
	}

	/**
	 * @return bool|string
	 */
	public function getLoginError()
	{
		$error_nodes = $this->find(".alert.alert-danger");
		return $error_nodes[0]->text();
	}


	/**
	 * @return string
	 * @throws ExpressionNotFound
     */
	public function getUserText()
	{
		$user_text_nodes = $this->find(".usertext");

		if (empty($user_text_nodes))
		{
		    throw new ExpressionNotFound(".usertext");
        }
		else {
            return $user_text_nodes[0]->text();
        }
	}

    /**
     * @return string
     * @throws ExpressionNotFound
     */
	public function checkLoginInfo()
    {
        $login_modal = $this->find("#modal-body");

        if (empty($login_modal))
        {
            throw new ExpressionNotFound("#modal-body");
        }
        else {
            return $login_modal[0]->text();
        }
    }

	/**
	 * @return int
     * @throws ExpressionNotFound
	 */
	public function getUserId()
	{
		$user_profile_link = $this->find("a[data-title=profile,moodle]");
		if( !empty($user_profile_link) )
        {
            $link = $user_profile_link[0]->text();
            return (int) self::parseExpressionFromLink("id", $link);
        }
	    else throw new ExpressionNotFound("a[data-title=profile,moodle]");
	}

	/**
	 * @return array
	 */
	public function getCoursesArray()
	{
		$courses_array = [];

		$course_boxes = $this->find("div.coursebox");

		foreach ($course_boxes as $key => $course_box)
		{
			$course_name = "";
			$course_link = "";

			try {
				$course_name = $course_box->find("div.info>h3>a")[0]->text();
				$course_link = $course_box->find("div.info>h3>a")[0]->attr("href");
			}
			catch (InvalidSelectorException $e) { Signal::msg($e->getMessage()); }

			$course_id = self::parseExpressionFromLink("id", $course_link);
			
			$courses_array[$course_id] = [
				"id" => $course_id,
				"name" => $course_name
			];
		}

		return $courses_array;
	}

    /**
     * @return \DiDom\Element|string|null
     * @throws ExpressionNotFound
     */
	public function getToken()
    {
        $token_input = $this->find("input[name=logintoken]");
        if( !empty($token_input) )
            return $token_input[0]->attr("value");
        else throw new ExpressionNotFound("input[name=logintoken]");
    }
}
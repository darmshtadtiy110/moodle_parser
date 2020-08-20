<?php


namespace Parser\Resources;


use DiDom\Exceptions\InvalidSelectorException;
use General\Signal;
use Parser\Parser;

class StudentParser extends Parser
{
	/**
	 * @return string|bool
	 */
	public function getLoginResults()
	{
		$login_info_nodes = $this->find(".login");

		if ( empty($login_info_nodes) ) return true;
		else return $login_info_nodes[0]->text();
	}

	/**
	 * @return bool|string
	 */
	public function getLoginError()
	{
		$error_nodes = $this->find("span.error");
		return $error_nodes[0]->text();
	}


	/**
	 * @return string
	 */
	public function getUserText()
	{
		$user_text_nodes = $this->find(".usertext");
		return $user_text_nodes[0]->text();
	}

	/**
	 * @return int
	 */
	public function getUserId()
	{
		$user_profile_link = $this->find("a[data-title=profile,moodle]");
		$link = $user_profile_link[0]->text();
		return (int) self::parseExpressionFromLink("id", $link);
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
}
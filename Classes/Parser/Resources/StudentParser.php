<?php


namespace Parser\Resources;


use Parser\Parser;
use General\Signal;
use Resources\Course;
use DiDom\Exceptions\InvalidSelectorException;

class StudentParser extends Parser
{
	/**
	 * @return bool|string
	 */
	public function getLoginResults()
	{
		$login_nodes = $this->find(".login");
		return $login_nodes[0]->text();
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

			$course = new Course($course_id, $course_name);

			$courses_array[$course_id] = $course;
		}

		return $courses_array;
	}
}
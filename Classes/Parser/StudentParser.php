<?php


namespace Parser;


use General\Signal;
use Resources\Course;
use Exception;
use DiDom\Exceptions\InvalidSelectorException;


// TODO add id parsing
class StudentParser extends Parser
{
	/**
	 * @return bool|string
	 */
	public function getLoginResults()
	{
		$login_nodes = [];

		try {
			$login_nodes = $this->parse_page->find(".login");
		}
		catch (InvalidSelectorException $e) {
			echo "Wrong selector ". $e->getMessage();
		}

		if(empty($login_nodes))
			return true;

		return $login_nodes[0]->text();
	}

	/**
	 * @return bool|string
	 */
	public function getLoginError()
	{
		try {
			$error_nodes = $this->parse_page->find("span.error");
		}
		catch (InvalidSelectorException $e) {}

		if(empty($error_nodes))
			return false;

		return $error_nodes[0]->text();
	}


	/**
	 * @return string
	 * @throws Exception
	 */
	public function getUserText()
	{
		try {
			$user_text_nodes = $this->parse_page->find(".usertext");
		}
		catch (InvalidSelectorException $e) {
			echo "Wrong selector ". $e->getMessage();
		}

		if(empty($user_text_nodes)) {
			throw new Exception("Can't find usertext node");
		}

		return $user_text_nodes[0]->text();
	}

	/**
	 * @throws Exception
	 * @return array
	 */
	public function getCoursesArray()
	{
		$course_boxes  = [];
		$courses_array = [];

		try {
			$course_boxes = $this->parse_page->find("div.coursebox");
		}
		catch (InvalidSelectorException $e) {
			Signal::msg("Wrong selector, or boxes not found on page ".$e->getMessage());
		}

		foreach ($course_boxes as $key => $course_box)
		{
			$course_name = "";
			$course_link = "";

			try {
				$course_name = $course_box->find("div.info>h3>a")[0]->text();
				$course_link = $course_box->find("div.info>h3>a")[0]->attr("href");
			}
			catch (InvalidSelectorException $e) { Signal::msg($e->getMessage()); }

			if($course_name != "" && $course_link != "")
			{
				$id = $this->parseIdFromLink($course_link);

				$course = new Course();

				$courses_array[$id] = $course->loadFromArray([
					"id" => $id,
					"name" => $course_name,
					"link" => $course_link
				]);
			}
		}

		return $courses_array;
	}
}
<?php


namespace Parser;


use DiDom\Exceptions\InvalidSelectorException;
use Exception;
use Resources\Course;

class StudentParser extends Parser
{
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
	public function getCourseList()
	{
		$courses_list = [];

		try {
			$course_boxes = $this->parse_page->find("div.coursebox");
		}
		catch (InvalidSelectorException $e) {
			echo "Wrong selector ";
		}

		if(empty($course_boxes)) throw new Exception("Course boxes not found on page");

		foreach ($course_boxes as $key => $course_box)
		{
			$course_name = false;
			$course_link = false;

			try {
				$course_name = $course_box->find("div.info>h3>a")[0]->text();
			}
			catch (InvalidSelectorException $e) { echo $e->getMessage(); }

			try {
				$course_link = $course_box->find("div.info>h3>a")[0]->attr("href");
			}
			catch (InvalidSelectorException $e) { echo $e->getMessage(); }

			if($course_name && $course_link)
			{
				$id = $this->parseIdFromLink($course_link);

				$course = new Course();

				$courses_list[$id] = $course->loadFromArray([
					"id" => $id,
					"name" => $course_name,
					"link" => $course_link
				]);
			}
		}

		return $courses_list;
	}
}
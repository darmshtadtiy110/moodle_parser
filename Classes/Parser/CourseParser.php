<?php


namespace Parser;


use Exception;
use Resources\Quiz;

class CourseParser extends Parser
{
	public function getQuizList()
	{
		$quiz_list = [];

		try {
			$quiz_nodes = $this->parse_page->find("li.activity.quiz.modtype_quiz>div>div>div>div>a");
		}
		catch (Exception $e) {
			echo "GetTestList exception ".$e->getMessage();
		}

		if( empty($quiz_nodes) ) return false;

		foreach($quiz_nodes as $quiz)
		{
			$quiz_name = $quiz->text();
			$quiz_link = $quiz->attr("href");

			if($quiz_name && $quiz_link)
			{
				$id = $this->parseIdFromLink($quiz_link);

				$quiz_list[$id] = [
					"id" => $id,
					"name" => $quiz->text(),
					"link" => $quiz->attr("href")
				];
			}
		}

		return $quiz_list;
	}
}
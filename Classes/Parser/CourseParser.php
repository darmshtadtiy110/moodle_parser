<?php


namespace Parser;


use DiDom\Exceptions\InvalidSelectorException;

class CourseParser extends Parser
{
	/**
	 * @return array
	 */
	public function getQuizList()
	{
		$quiz_list = [];

		try {
			$quiz_nodes = $this->parse_page->find("li.activity.quiz.modtype_quiz>div>div>div>div>a");
		}
		catch (InvalidSelectorException $e) {
			echo "GetTestList exception ".$e->getMessage();
		}

		if( !empty($quiz_nodes) )
		{
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
		}

		return $quiz_list;
	}
}
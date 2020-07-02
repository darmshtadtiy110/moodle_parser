<?php


namespace Parser\Resources;

use Parser\Parser;
use DiDom\Exceptions\InvalidSelectorException;
use Resources\Quiz;

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
			foreach($quiz_nodes as $quiz_node)
			{
				$quiz_name = $quiz_node->text();
				$quiz_link = $quiz_node->attr("href");

				if($quiz_name && $quiz_link)
				{
					$quiz = new Quiz();

					$quiz_list[] = $quiz->loadFromArray([
						"name" => $quiz_node->text(),
						"link" => $quiz_node->attr("href")
					]);
				}
			}
		}

		return $quiz_list;
	}
}
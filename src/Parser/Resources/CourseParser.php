<?php


namespace MoodleParser\Parser\Resources;

use MoodleParser\Parser\Parser;

class CourseParser extends Parser
{
	/**
	 * @return array
	 */
	public function getQuizList()
	{
		$quiz_list = [];

		$quiz_nodes = $this->find("li.activity.quiz.modtype_quiz>div>div>div>div>a");


		if( !empty($quiz_nodes) )
		{
			foreach($quiz_nodes as $quiz_node)
			{
				$quiz_name = $quiz_node->text();
				$quiz_link = $quiz_node->attr("href");
				$quiz_id = Parser::parseExpressionFromLink("id", $quiz_link);

				if($quiz_name && $quiz_link)
				{
					$quiz_list[$quiz_id] = [
						"id" => $quiz_id,
						"name" => $quiz_name
					];
				}
			}
		}

		return $quiz_list;
	}

	public function getCourseName()
	{
		return $this->find("div.page-header-headings>h1")[0]->text();
	}
}
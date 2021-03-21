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

		$quiz_nodes = $this->find("li.activity.quiz.modtype_quiz");


		if( !empty($quiz_nodes) )
		{
			foreach($quiz_nodes as $quiz_node)
			{
				$quiz_name_span = $this->find("span.instancename", $quiz_node)[0];

				if($quiz_name_span->hasChildren() === true && count($quiz_name_span->children()) > 1)
					$quiz_name_span->lastChild()->remove();

				$quiz_name = $quiz_name_span->text();

				$quiz_id = substr($quiz_node->attr("id"), 7);

				if($quiz_id)
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
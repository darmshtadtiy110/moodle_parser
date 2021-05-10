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

				// remove trash from quiz name
				if($quiz_name_span->hasChildren() === true && count($quiz_name_span->children()) > 1)
					$quiz_name_span->lastChild()->remove();

				$id = substr($quiz_node->attr("id"), 7);
				$name = $quiz_name_span->text();

				// check quiz is available
				if(!empty($this->find("div.availabilityinfo", $quiz_node)))
					$available = true;
				else
					$available = false;

				$quiz_list[$id] = [
					"name" => $name,
					"available" => $available
				];
			}
		}

		return $quiz_list;
	}

	public function getCourseName()
	{
		return $this->find("div.page-header-headings>h1")[0]->text();
	}

	public function courseId()
	{
		$dropdown_lang_link = $this->find("a.dropdown-item")[0]->attr("href");
		return parent::parseExpressionFromLink("id", $dropdown_lang_link);
	}
}
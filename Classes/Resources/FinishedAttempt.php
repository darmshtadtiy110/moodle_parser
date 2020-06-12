<?php


namespace Resources;


use Exception;

class FinishedAttempt extends Attempt implements Finished
{
	function __construct($name, $review_link, $page, $question_list, $grade)
	{
		parent::__construct($name, $review_link, $page);
		$this->question_list = $question_list;
		$this->grade = $grade;
	}

	public function getQuestions()
	{
		if(empty($this->question_list))
		{
			try {
				$question_boxes = $this->getPage()->find("div.que");

				foreach ($question_boxes as $box)
				{
					$checked_input_id = $box->find("input[checked=checked]")[0]->attr("id");

					$state = $box->find("div.info>div.state")[0]->text();
					$state = ($state == "Правильно") ? true : false;

					$question_text = $box->find("div.content>div.formulation.clearfix>div.qtext")[0]->text();
					$answer_text = $box->find("label[for=$checked_input_id]")[0]->text();

					// TODO Use question factory
					$this->question_list[] = [
						"question_text" => $question_text,
						"answer_id" => $checked_input_id,
						"answer_text" => substr($answer_text, 3),
						"state" => $state
					];
				}

			}
			catch (Exception $e) {
				echo "ParseAttemptPage exception ".$e->getMessage();
			}
		}

		return $this->question_list;
	}
}
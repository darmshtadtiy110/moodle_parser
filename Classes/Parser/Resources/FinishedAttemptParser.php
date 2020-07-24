<?php


namespace Parser\Resources;

use General\Request;
use Parser\Parser;
use General\Signal;
use Resources\FinishedAttempt;
use DiDom\Exceptions\InvalidSelectorException;
use Exception;

class FinishedAttemptParser extends Parser
{

	/**
	 * @deprecated
	 * This function parsed finished attempt page
	 * @return array
	 */
	public function getQuestions()
	{
		$question_list = [];

		try {
			$question_boxes = $this->parse_page->find("div.que");

			var_dump($question_boxes);

			foreach ($question_boxes as $box)
			{
				$checked_input_id = $box->find("input[checked=checked]")[0]->attr("id");

				$state = $box->find("div.info>div.state")[0]->text();
				$state = ($state == "Правильно") ? true : false;

				$question_text = $box->find("div.content>div.formulation.clearfix>div.qtext")[0]->text();
				$answer_text = $box->find("label[for=$checked_input_id]")[0]->text();

				$question_list[] = [
					"question_text" => $question_text,
					"answer_id" => $checked_input_id,
					"answer_text" => substr($answer_text, 3),
					"state" => $state
				];
			}

		}
		catch (InvalidSelectorException $e) {
			Signal::msg("Attempt parser exception ".$e->getMessage() );
		}

		return $question_list;
	}

	public function findGeneralTable()
	{
		return $this->find("table.generaltable")[0];
	}

	public function getGrade()
	{
		$general_table = $this->findGeneralTable();

		try {
			$rows = $general_table->find("tbody>tr");
			$grade = (int) $rows[4]->find("td>b")[0]->text();
			return $grade;
		}
		catch (InvalidSelectorException $e) {}
		return false;
	}

	public function getName()
	{
		$general_table = $this->findGeneralTable();

		try {
			$rows = $general_table->find("tbody>tr");
			$name = $rows[1]->find("td")[0]->text();
			return $name;
		}
		catch (InvalidSelectorException $e) {}
		return false;
	}

	public static function GetById($id)
	{
		$attempt_review_request = Request::AttemptReview($id);

		$attempt_parser = new FinishedAttemptParser();
		$questions_parser = new QuestionParser();

		$attempt_parser->setParsePage($attempt_review_request->response());
		$questions_parser->setParsePage($attempt_review_request->response());

		$attempt = false;
		try {
			$attempt = new FinishedAttempt(
				$id,
				$attempt_parser->getGrade(),
				$attempt_parser->getName(),
				$questions_parser->parseQuestions()
			);
		}
		catch (Exception $e) { Signal::msg($e->getMessage()); }

		return $attempt;
	}
}
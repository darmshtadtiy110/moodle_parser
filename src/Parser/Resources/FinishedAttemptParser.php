<?php


namespace MoodleParser\Parser\Resources;

use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use DOMElement;
use MoodleParser\General\Signal;
use MoodleParser\Parser\Parser;

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

	/**
	 * @return Element|DOMElement
	 */
	public function findGeneralTable()
	{
		$table_node = $this->find("table.generaltable");
		return $table_node[0];
	}

	public function getGrade()
	{
		$general_table = $this->findGeneralTable();

		try {
			$rows = $general_table->find("tbody>tr");
			$grade = (int) $rows[5]->find("td>b")[0]->text();
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
}
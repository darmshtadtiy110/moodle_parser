<?php


namespace MoodleParser\Parser\Resources;


use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use DOMElement;
use MoodleParser\General\Signal;
use MoodleParser\Parser\Exceptions\NewAttemptBan;
use MoodleParser\Parser\Parser;
use MoodleParser\Resources\Question;

class AttemptParser extends Parser
{
	/**
	 * @return int
	 * @throws NewAttemptBan
	 */
	public function getProcessingAttemptId()
	{
		$summary_link_node = $this->find("a.endtestlink");

		if(empty($summary_link_node)) throw new NewAttemptBan($this);

		$summary_url = $summary_link_node[0]->attr("href");

		return (int) self::parseExpressionFromLink("attempt", $summary_url);
	}

	public function parseForm($form_state = "")
	{
		//TODO Remove switch
		switch ($form_state)
		{
			case "finish_attempt":
				$form_element = $this->find("form")[1];
				break;
			default:
				$form_element = $this->find("form")[0];
				break;
		}

		return $this->parseFormInputs($form_element);
	}

	private function parseFormInputs(Element $form_ob)
	{
		$inputs = [];
		$fields = [];

		try {
			$inputs = $form_ob->find("input");

		} catch (InvalidSelectorException $e)
		{
			Signal::msg("ParseQuizForm exception " . $e->getMessage());
		}

		foreach ($inputs as $input)
		{
			$input_name = $input->attr("name");
			$input_value = $input->attr("value");

			if ($input_name != "" && !array_key_exists($input_name, $fields))
				$fields[$input_name] = $input_value;
		}

		if( empty($fields) ) return false;
		else return $fields;
	}

	/**
	 * @return array
	 */
	public function getQuestionsStatus()
	{
		$total_counter   = 0;
		$saved_counter   = 0;
		$current_counter = 0;

		$question_buttons = $this->find("div.qn_buttons.clearfix.multipages>a");

		foreach ($question_buttons as $button)
		{
			$button_classes = $button->classes();

			if ($button_classes->contains("answersaved"))
				$saved_counter++;

			if ($button_classes->contains("thispage"))
				$current_counter++;

			$total_counter++;
		}

		return [
			"total" => $total_counter,
			"saved" => $saved_counter,
			"current" => $current_counter
		];
	}

	/**
	 * @return Question[]
	 */
	public function getQuestionsArray()
	{
		$question_array = [];

		$question_blocks = $this->find("div.que");

		foreach ($question_blocks as $key => $block)
		{
			$num = $key + 1;

			$question_array[$num] = new Question($block);
		}

		return $question_array;
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

	public function getFinishedAttemptId()
	{
		$form_node = $this->find("form.questionflagsaveform")[0];
		$form_action = $form_node->attr("action");
		return self::parseExpressionFromLink("attempt", $form_action);
	}

	/**
	 * @deprecated
	 * @return array
	 */
	public function getQuestions()
	{
		$question_list = [];

		try {
			$question_boxes = $this->getParsePage()->find("div.que");

			foreach ($question_boxes as $box)
			{
				$checked_input_id = $box->find("input[checked=checked]")[0]->attr("id");

				$state = $box->find("div.info>div.state")[0]->text();
				switch ($state) {
					case "Правильно":
						break;
					case "Частково правильно":
						break;
				}

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
}
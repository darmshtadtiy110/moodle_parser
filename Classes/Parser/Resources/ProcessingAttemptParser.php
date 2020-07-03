<?php


namespace Parser\Resources;


use General\Signal;
use Parser\Parser;
use DiDom\Exceptions\InvalidSelectorException;

class ProcessingAttemptParser extends Parser
{
	public function getAttemptId()
	{
		$summary_link_node = $this->find("a.endtestlink");
		$summary_url = $summary_link_node[0]->attr("href");

		return self::parseExpressionFromLink("attempt", $summary_url);
	}

	public function parseInputs()
	{
		$inputs = [];

		try {
			$form_ob = $this->parse_page->find("form#responseform");

			if(empty($form_ob)) return false;

			$inputs = $form_ob[0]->find("input");
		}
		catch (InvalidSelectorException $e) {
			Signal::msg("ParseQuizForm exception ".$e->getMessage());
		}

		$fields = [];

		foreach ($inputs as $input)
		{
			$input_name = $input->attr("name");
			$input_value = $input->attr("value");
			if($input_name != "" && !array_key_exists($input_name, $fields) )
				$fields[$input_name] = $input_value;
		}

		return $fields;
	}

	/**
	 * @return array ["number" => int, "saved" => bool, "current" => bool]
	 */
	public function getQuestionsStatus()
	{
		$status_array = [];
		$question_buttons = $this->find("div.qn_buttons.clearfix.multipages>a");

		$counter = 1;

		foreach ($question_buttons as $button)
		{

			$question_array = [
				"number" => $counter,
				"saved" => false,
				"current" => false
			];

			$button_classes = $button->classes();

			if ($button_classes->contains("answersaved"))
				$question_array["saved"] = true;

			if ($button_classes->contains("thispage"))
				$question_array["current"] = true;

			$status_array[$counter] = $question_array;
			$counter++;
		}

		return $status_array;
	}
}
<?php


namespace Parser\Resources;


use DiDom\Element;
use General\Signal;
use Parser\Parser;
use DiDom\Exceptions\InvalidSelectorException;

class ProcessingAttemptParser extends Parser
{
	public function getAttemptId()
	{
		$summary_link_node = $this->find("a.endtestlink");
		$summary_url = $summary_link_node[0]->attr("href");

		return (int) self::parseExpressionFromLink("attempt", $summary_url);
	}

	public function parseForm($form_state = "")
	{
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
}
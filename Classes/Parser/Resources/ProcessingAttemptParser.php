<?php


namespace Parser\Resources;


use General\Signal;
use Parser\Parser;
use DiDom\Exceptions\InvalidSelectorException;
use Parser\Resources\Questions\QuestionParser;

class ProcessingAttemptParser extends Parser
{
	/**
	 * @return array
	 */
	public function getQuestions()
	{
		$questions_array = [];

		try {
			// find question boxes on page
			$question_boxes = $this->parse_page->find("div.que");

			if( empty($question_boxes) ) return $questions_array;

			foreach ($question_boxes as $box)
			{
				$questions_array[] = QuestionParser::IdentQuestion($box);
			}
		}
		catch (InvalidSelectorException $e) {}

		return $questions_array;
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
}
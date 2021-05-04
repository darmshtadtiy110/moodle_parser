<?php


namespace MoodleParser\Parser\Resources;

use DiDom\Element;
use Exception;
use MoodleParser\General\Signal;
use MoodleParser\Parser\Parser;
use MoodleParser\Resources\Variant;
use MoodleParser\Resources\Variants\Checkbox;
use MoodleParser\Resources\Variants\Radio;

class QuestionParser extends Parser
{
	/**
	 * QuestionParser constructor.
	 * @param Element $question_block
	 */
	public function __construct(Element $question_block)
	{
		parent::__construct($question_block);
	}

	public function getNumber()
	{
		return (int) $this->find("span.qno")[0]->text();
	}

	public function getText()
	{
		return $this->find("div.qtext")[0]->text();
	}

	/**
	 * @return bool
	 */
	public function getCorrect()
	{
		$correct_str = $this->find("div.info>div.state")[0]->text();
		return ($correct_str == "Правильно") ? true : false;
	}


	/**
	 * @return Variant[]
	 */
	public function getVariants()
	{
		$variants = [];


		$variant_nodes = $this->find("div.answer>div");

		foreach ($variant_nodes as $key => $node)
		{
			try {
				$variant_text = preg_replace("/[a-z]. / ", "", $node->find("label")[0]->text());

				$node_entire_inputs = $node->find("input");
				// select no hidden input
				$answer_input_node = $node_entire_inputs[count($node_entire_inputs) - 1];

				$var_type = $answer_input_node->attr("type");

				$answer_input_name = $answer_input_node->attr("name");
				$answer_input_value = $answer_input_node->attr("value");

				switch ($var_type)
				{
					case "checkbox":
						$variant = new Checkbox(
							$key,
							$variant_text,
							$answer_input_name,
							$answer_input_value
						);
						continue;
					case "radio":
					default:
						$input_is_checked = $answer_input_node->attr("checked");
						$input_is_checked = ($input_is_checked == "checked") ? true : false;
						$variant = new Radio(
							$key,
							$variant_text,
							$input_is_checked,
							$answer_input_name,
							$answer_input_value
						);
						continue;
					}

					$variants[] = $variant;
			}
			catch (Exception $e) { Signal::msg($e->getMessage()); }
		}
		return $variants;
	}

	public function findCorrectAnswerText()
	{
		$correct_answer = $this->find("div.rightanswer");
		if(empty($correct_answer)) return false;
		return str_replace("Правильна відповідь: ", "", $correct_answer[0]->text());
	}
}
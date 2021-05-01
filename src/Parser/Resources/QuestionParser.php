<?php


namespace MoodleParser\Parser\Resources;

use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use Exception;
use MoodleParser\General\Signal;
use MoodleParser\Parser\Parser;
use MoodleParser\Resources\Question;
use MoodleParser\Resources\Variants\Checkbox;
use MoodleParser\Resources\Variants\Radio;

class QuestionParser extends Parser
{
	/** @var Element */
	private $question_block;

	public function setQuestionBlock(Element $block)
	{
		$this->question_block = $block;
	}

	public function getNumber()
	{
		$number = false;
		try {
			$number = (int) $this->question_block->find("span.qno")[0]->text();
		}
		catch (InvalidSelectorException $e) {}
		return $number;
	}

	public function getText()
	{
		$text = "";
		try {
			$text = $this->question_block->find("div.qtext")[0]->text();
		}
		catch (InvalidSelectorException $e) {}

		return $text;
	}

	public function getCorrect()
	{
		$correct = false;
		try {
			$correct = $this->question_block->find("div.info>div.state")[0]->text();
		}
		catch (InvalidSelectorException $e) { Signal::msg($e->getMessage()); }

		$correct = ($correct == "Правильно") ? true : false;
		return $correct;
	}


	public function getVariants()
	{
		$variants = [];

		try {
			$correct_answer = $this->question_block->find("div.rightanswer");

			$variant_nodes = $this->question_block->find("div.answer>div");

			foreach ($variant_nodes as $key => $node)
			{
				try {
					$variant_text = $node->find("label")[0]->text();

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
								substr($variant_text, 3),
								$input_is_checked,
								$answer_input_name,
								$answer_input_value
							);
							continue;
					}

					if(!empty($correct_answer))
					{
						$correct_answer_text = str_replace("Правильна відповідь: ", "", $correct_answer[0]->text());

						if($variant->getValue() == $correct_answer_text)
							$variant->setIsCorrect(true);
					}

					$variants[] = $variant;
				}
				catch (Exception $e) { Signal::msg($e->getMessage()); }


			}
		}
		catch (InvalidSelectorException $e) { Signal::msg("IdentQuestion error: ".$e->getMessage()); }
		return $variants;
	}

	/**
	 * @return Question[]
	 */
	public function parseQuestions()
	{
		$question_array = [];

		$question_blocks = $this->find("div.que");

		foreach ($question_blocks as $key => $block)
		{
			$this->setQuestionBlock($block);
			$num = $this->getNumber();

			try {
				$question_array[$num] = new Question(
					$num,
					$this->getText(),
					$this->getVariants(),
					$this->getCorrect()
				);
			}
			catch (Exception $e) { Signal::msg($e->getMessage()); }
		}

		return $question_array;
	}
}
<?php


namespace Parser\Resources;


use DiDom\Element;
use General\Signal;
use Parser\Parser;
use Resources\Question;
use DiDom\Exceptions\InvalidSelectorException;
use Resources\Variants\TextVariant;

class QuestionParser extends Parser
{
	/** @var Element */
	private $question_block;

	public function setQuestionBlock(Element $block)
	{
		$this->question_block = $block;
	}

	public function getQuestionNumber()
	{
		$number = false;
		try {
			$number = (int) $this->question_block->find("span.qno")[0]->text();
		}
		catch (InvalidSelectorException $e) {}
		return $number;
	}

	public function getQuestionText()
	{
		$text = "";
		try {
			$text = $this->question_block->find("div.qtext")[0]->text();
		}
		catch (InvalidSelectorException $e) {}

		return $text;
	}


	public function getVariants()
	{
		$variants = [];

		try {
			$variant_nodes = $this->question_block->find("div.answer>div");

			foreach ($variant_nodes as $key => $node)
			{
				$var_type = "text";

				$variant_text = $node->find("label")[0]->text();
				/**
				if (strlen($variant_text) == 0)
				{

				}*/

				$answer_input_node = $node->find("input");
				$answer_input_name = $answer_input_node[0]->attr("name");
				$answer_input_value = $answer_input_node[0]->attr("value");

				switch ($var_type)
				{
					case "text":
						$variants[$key] = new TextVariant(
							$key,
							substr($variant_text, 3),
							$answer_input_name,
							$answer_input_value
						);
						continue;
					case "pic":
						continue;
				}
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

			$text = $this->getQuestionText();
			$num = $this->getQuestionNumber();
			$variant_array = $this->getVariants();

			$question_array[$num] = new Question($num, $text, $variant_array);
		}

		return $question_array;
	}
}
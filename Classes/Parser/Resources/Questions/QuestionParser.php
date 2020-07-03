<?php


namespace Parser\Resources\Questions;


use DiDom\Element;
use General\Signal;
use Parser\Parser;
use Resources\Questions\Question;
use Resources\Questions\TextQuestion;
use DiDom\Exceptions\InvalidSelectorException;

class QuestionParser extends Parser
{
	/** @var Element */
	private $question_block;

	public function identityQuestionBlock(Question $question)
	{
		$question_blocks = $this->find("div.que");

		foreach ($question_blocks as $block)
		{
			try {
				$number = $block->find("span.qno")[0]->text();
				if($number == $question->getNumber())
				{
					$this->setQuestionBlock($block);
					break;
				}
			}
			catch (InvalidSelectorException $e) {}
		}

	}

	/**
	 * @param Element $question_block
	 */
	public function setQuestionBlock(Element $question_block)
	{
		$this->question_block = $question_block;
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

	/**
	 * @deprecated
	 * @param Element $question_block
	 * @return Question|TextQuestion
	 */
	public function getVariants(Element $question_block)
	{
		try {
			$variant_nodes = $question_block->find("div.answer>div");

			// as default - text question
			$question = new Question(80);

			//$question->parser->setQuestionBlock($question_block);

			foreach ($variant_nodes as $variant)
			{
				$variant_text = $variant->find("label")[0]->text();
				$variant_text = substr($variant_text, 3);

				$answer_input_node = $variant->find("input");
				$answer_input_name = $answer_input_node[0]->attr("name");
				$answer_input_value = $answer_input_node[0]->attr("value");

				$question->setVariant([
					"value" => $variant_text,
					"input_name" => $answer_input_name,
					"input_value" => $answer_input_value
				]);
			}
		}
		catch (InvalidSelectorException $e) { Signal::msg("IdentQuestion error: ".$e->getMessage()); }

		/** @var Question $question */
		return $question;
	}
}
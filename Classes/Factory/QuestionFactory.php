<?php


namespace Factory;


use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use Resources\Attempt;

class QuestionFactory
{
	public static function CreateFromAttempt(Attempt $attempt)
	{
		$question_text = false;
		$answer_variants = false;

		$attempt_current_page = $attempt->getPage();

		$question_blocks = self::ParseQuestionBlocks($attempt_current_page);

		/**
		 * if have many questions per page, return array of Question's
		 */
		foreach ($question_blocks as $question)
		{
			$question_array = self::ParseQuestionData($question);
		}

	}

	/**
	 * @param Document $attempt_current_page
	 * @return bool|\DiDom\Element[]|\DOMElement[]
	 */
	private static function ParseQuestionBlocks(Document $attempt_current_page)
	{
		$questions = false;

		try {
			$questions = $attempt_current_page->find("div.que");
		}
		catch (InvalidSelectorException $e) {
			echo "Create question from attempt exception: ".$e->getMessage();
		}

		if(!is_array($questions)) return false;
		else return $questions;
	}

	/**
	 * @param Element $question_block
	 * @return array = [ "type", "text", "variants" = null ]
	 */
	private static function ParseQuestionData(Element $question_block)
	{
		$question_text = false;
		$answer_variants = false;
		/**
		 * Finding question text
		 */
		try {
			$question_text = $question_block->find("div.qtext")[0]->text();
		}
		catch (InvalidSelectorException $e) {
			echo "Find question text exception: ".$e->getMessage();
		}

		/**
		 * Finding answer variants
		 */
		try {
			$answer_variants = $question_block->find("div.answer>div>label");
		}
		catch (InvalidSelectorException $e) {
			echo "Find answer variants exception: ".$e->getMessage();
		}

		self::AnswerTypeIdentity($answer_variants);

		//var_dump($answer_variants);

		return [];
	}

	/**
	 * @param $answer_variants
	 * @return array = [ "type", "value"]
	 */
	private static function AnswerTypeIdentity($answer_variants)
	{
		if(
			!is_array($answer_variants) ||
			!( $answer_variants[0] instanceof Element )
		)
			die("[AnswerTypeIdentity] Wrong parameter \n");

		//$answer = "";

		//echo $answer_variants[0]->html();

		/**
		 * instead foreach
		 */
		$variant = $answer_variants[0];

		/**
		 * get the text from label
		 */
		$answer_text = $variant->text();
		$answer_text = substr($answer_text, 3);

		//if($answer_text != "")




	}
	/**
	 * @param $question_data_array = [
	 *              "type",
	 *              "text",
	 *              "variants",
	 * @return
	 */
	public static function CreateFromArray($question_data_array)
	{
		return new TextQuestion;
	}
}
<?php


namespace Parser\Resources;


use Parser\Parser;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use General\Signal;
use Resources\Questions\TextQuestion;

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
				$questions_array[] = $this->identQuestion($box);
			}
		}
		catch (InvalidSelectorException $e) {}

		return $questions_array;
	}

	private function identQuestion(Element $question_box)
	{
		try {
			$question_text = $question_box->find("div.qtext")[0]->text();
			$variant_nodes = $question_box->find("div.answer>div");

			// as default - text question
			$question = new TextQuestion($question_text);

			foreach ($variant_nodes as $variant)
			{
				$variant_text = $variant->find("label")[0]->text();
				$variant_text = substr($variant_text, 3);

				$question->setVariant($variant_text);
			}
		}
		catch (InvalidSelectorException $e) { Signal::msg("IdentQuestion error: ".$e->getMessage()); }

		return $question;
	}
}
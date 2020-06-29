<?php


namespace Parser;


use DiDom\Exceptions\InvalidSelectorException;
use Resources\Question\TextQuestion;

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
				$question_text = $box->find("div.qtext")[0]->text();

				// as default - text question
				$question = new TextQuestion($question_text);

				$variant_nodes = $box->find("div.answer>div");

				foreach ($variant_nodes as $variant)
				{
					$variant_text = $variant->find("label")[0]->text();
					$variant_text = substr($variant_text, 3);

					$question->setVariant($variant_text);
				}

				$questions_array[] = $question;
			}
		}
		catch (InvalidSelectorException $e) {}

		return $questions_array;
	}
}
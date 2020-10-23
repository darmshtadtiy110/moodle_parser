<?php


namespace MoodleParser\AttemptProcessor;


use MoodleParser\Resources\FinishedAttempt;
use MoodleParser\Resources\Question;
use MoodleParser\Resources\Variant;

class TipsProcessor implements Processor
{
	/** @var Question[] */
	protected $tips = [];

	/**
	 * TipsProcessor constructor.
	 * @param FinishedAttempt|FinishedAttempt[] $tips_attempt
	 */
	public function __construct($tips_attempt)
	{
		//TODO Compare from all attempts to one list
		if( $tips_attempt instanceof FinishedAttempt )
			$this->tips = $tips_attempt->getQuestions();
		elseif ( is_array($tips_attempt) && $tips_attempt[0] instanceof FinishedAttempt)
		{
			foreach ($tips_attempt as $attempt)
				$this->tips = array_merge($this->tips, $attempt->getQuestions());

			shuffle($this->tips);

			$this->compareTips();
		}

		return false;
	}

	private function compareTips()
	{
		foreach ($this->tips as $main_key => $question)
		{
			next($this->tips);
			foreach($this->tips as $key => $sec_question)
			{
				if($question->getText() == $sec_question->getText())
				{
					if($question->isCorrect() === true && $sec_question->isCorrect() === false)
						unset($this->tips[$key]);
					elseif($question->isCorrect() === false && $sec_question->isCorrect() === true)
						unset($this->tips[$main_key]);
					elseif($question->isCorrect() === false && $sec_question->isCorrect() === false) {
						// We need go deeper :)

						$first_variants = $question->getVariants();
						foreach ($first_variants as $vars_key => $variant)
						{
							if($variant->isChecked())
								unset($first_variants[$vars_key]);
						}

						$question->setVariants(array_values($first_variants));
						$this->tips[$main_key] = $question;
					}
				}
			}
		}
	}

	public function choiceVariant(Question $question)
	{
		foreach($this->tips as $tip)
		{
			if($tip->getText() == $question->getText())
			{
				if($tip->isCorrect())
				{
					$correct = $tip->getSelectedVariant();

					return $this->searchVariantFromTip($question, $correct);
				}
				else {
					foreach ($tip->getVariants() as $variant)
					{
						if($variant->isCorrect())
							return $this->searchVariantFromTip($question, $variant);
					}
					return $this->randomFromTips($question, $tip);
				}
			}
		}
		// if tips not find use great random
		$great_random = new RandomProcessor();

		return $great_random->choiceVariant($question);
	}

	private function randomFromTips(Question $question, Question $tip)
	{
		$variants = $question->getVariants();
		$wrong_var = $tip->getSelectedVariant();
		if(is_null($wrong_var)) var_dump($tip);

		foreach ($variants as $key => $variant)
		{
			if($variant->getValue() == $wrong_var->getValue())
				unset( $variants[ $key ] );
		}

		$variants = array_values($variants);

		return $variants[mt_rand(0, count($variants) - 1)];
	}

	private function searchVariantFromTip(Question $question, Variant $tip)
	{
		foreach ($question->getVariants() as $variant)
		{
			if($variant->getValue() == $tip->getValue())
				return $variant;
		}
		return false;
	}
}
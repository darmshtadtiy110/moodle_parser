<?php


namespace MoodleParser\Resources;


use DiDom\Element;

class Question extends Resource
{
	/** @var bool */
	protected $correct = false;

	/** @var Variant[] */
	protected $variants = [];

	/** @var int */
	protected $selected_variant;

	/** @var bool */
	protected $current = false;

	protected $saved = false;

	protected $answered = false;

	/**
	 * Question constructor.
	 * @param Element $question_block
	 */
	public function __construct(Element $question_block)
	{
		$this->parser($question_block);

		if(!$question_block->classes()->contains("notyetanswered"))
		{
			$this->isAnswered();
			$variants = $this->parser()->getVariants();
			foreach ($variants as $key => $variant)
			{
				if($variant->getValue() == $this->parser()->findCorrectAnswerText())
				{
					$variant->setIsCorrect(true);
					$variants[$key] = $variant;
				}
			}
			$this->variants = $variants;
		}
		else {
			$this->variants = $this->parser()->getVariants();
		}

		foreach($this->variants as $variant)
		{
			if($variant->isChecked()) $this->selected_variant = $variant->id();
		}
		parent::__construct($this->parser()->getNumber(), $this->parser()->getText());
	}

	/**
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * @return String
	 */
	public function getText()
	{
		return $this->name;
	}

	/**
	 * @return bool
	 */
	public function isCorrect()
	{
		return $this->correct;
	}

	public function setIsCorrect()
	{
		$this->correct = true;
	}

	/**
	 * @return Variant
	 */
	public function getSelectedVariant()
	{
		return $this->variants[$this->selected_variant];
	}

	/**
	 * @return Variant[]
	 */
	public function getVariants()
	{
		return $this->variants;
	}

	/**
	 * @param Variant[] $variants
	 */
	public function setVariants($variants)
	{
		$this->variants = $variants;
	}

	/**
	 * @return int
	 */
	public function getNumber()
	{
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function isCurrent()
	{
		return $this->current;
	}

	/**
	 * @param bool $current
	 */
	public function setCurrent($current)
	{
		$this->current = $current;
	}

	public function getAnswered()
	{
		return $this->answered;
	}

	public function isAnswered()
	{
		$this->answered = true;
	}

	/**
	 * @return bool
	 */
	public function isSaved()
	{
		return $this->saved;
	}

	/**
	 * @param bool $saved
	 */
	public function setSaved($saved)
	{
		$this->saved = $saved;
	}

	public function selectVariant(Variant $variant)
	{
		$this->selected_variant = $variant->id();
		$this->setSaved(true);
	}
}
<?php


namespace Resources;


use Exception;
use General\Resource;

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

	public function __construct($id, $text, $variants, $correct)
	{
		if(!is_bool($correct)) throw new Exception("Correct arg isn't bool");

		$this->variants = $variants;
		$this->correct = $correct;

		foreach($this->variants as $variant)
		{
			if($variant->isChecked()) $this->selected_variant = $variant->getId();
		}

		parent::__construct($id, $text);
	}

	/**
	 * @return int
	 */
	public function getId()
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
		$this->selected_variant = $variant->getId();
		$this->setSaved(true);
	}
}
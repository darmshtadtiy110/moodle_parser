<?php


namespace Resources;


use General\Resource;

class Question extends Resource
{
	protected $answer;

	/** @var bool */
	protected $state;

	/** @var Variant[] */
	protected $variants = [];

	/** @var int */
	protected $selected_variant;

	/** @var int */
	protected $grade;

	/** @var bool */
	protected $current = false;

	protected $saved = false;

	public function __construct($id, $text, $variants)
	{
		$this->variants = $variants;

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
	 * @return String
	 */
	public function getAnswer()
	{
		return $this->answer;
	}

	/**
	 * @return bool
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @return array
	 */
	public function getVariants()
	{
		return $this->variants;
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
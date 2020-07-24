<?php


namespace Resources\Variants;

use Resources\Variant;

class TextVariant extends Variant
{
	public function __construct($id, $text, $input_name, $input_value)
	{
		$this->value = $text;
		parent::__construct($id, $input_name, $input_value);
	}

	/**
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}
}
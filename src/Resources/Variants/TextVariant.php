<?php


namespace Resources\Variants;

use Resources\Variant;

class TextVariant extends Variant
{
	public function __construct($id, $text, $checked, $input_name, $input_value)
	{
		if( !is_string($text) && $text == "") return;

		$this->value = $text;
		parent::__construct($id, $checked, $input_name, $input_value);
	}
}
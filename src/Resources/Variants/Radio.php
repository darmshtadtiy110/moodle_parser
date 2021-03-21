<?php


namespace MoodleParser\Resources\Variants;

use MoodleParser\Resources\Variant;

class Radio extends Variant
{
	public function __construct($id, $text, $checked, $input_name, $input_value)
	{
		if( !is_string($text) && $text == "") return;

		$this->value = $text;
		parent::__construct($id, $checked, $input_name, $input_value);
	}
}
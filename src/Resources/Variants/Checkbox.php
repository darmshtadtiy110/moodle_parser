<?php


namespace MoodleParser\Resources\Variants;


use MoodleParser\Resources\Variant;

class Checkbox extends Variant
{
	public function __construct($id, $text, $input_name, $input_value, $checked = true)
	{
		if( !is_string($text) && $text == "") return;

		$this->value = $text;
		parent::__construct($id, $checked, $input_name, $input_value);
	}
}
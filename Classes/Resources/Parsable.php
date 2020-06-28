<?php


namespace Resources;


use General\Tools;
use Parser\Parser;

trait Parsable
{
	/** @var Parser */
	private $parser;

	/**
	 * @return mixed
	 */
	public function parser()
	{
		return $this->parser;
	}

	private function setParser()
	{
		// get resource name
		$resource_class = Tools::get_class_name($this);

		// find needed parser
		$parser_class = "\\Parser\\".$resource_class."Parser";

		$this->parser = new $parser_class();
	}
}
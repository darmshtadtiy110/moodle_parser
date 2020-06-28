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

	/**
	 * @return Parser
	 */
	private static function getParser()
	{
		// get resource name
		$resource_class = Tools::get_class_name(static());

		// find needed parser
		$parser_class = "\\Parser\\".$resource_class."Parser";

		return new $parser_class();
	}

	abstract function parse();
}
<?php


namespace Resources;

use General\Resource;
use Parser\Resources\DocumentParser;

class Document extends Resource
{
	public function __construct($id, $name = "")
	{
		$this->parser = new DocumentParser;

		parent::__construct($id, $name);
	}

	protected function use_parser()
	{
		// TODO: Implement parserLoader() method.
	}
}
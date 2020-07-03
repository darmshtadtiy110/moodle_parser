<?php


namespace Traits;


use General\Properties;
use General\Signal;
use General\Request;

use General\Exceptions\CurlErrorException;
use General\Tools;
use Parser\Parser;

trait ParserUtilities
{
	/** @var Parser */
	protected $parser;

	/** @var Request */
	protected $last_request;

	public function parser()
	{
		return $this->parser;
	}

	protected function setParser()
	{
		$resource_class = get_class($this);
		$parser_class = "\\Parser\\".$resource_class."Parser";
		$this->parser = new $parser_class();
	}

	protected function getParsablePage()
	{
		$resource_type = Tools::get_object_class_name($this);

		if( is_callable( "Properties::".$resource_type, true ) )
		{
			$link = Properties::$resource_type().$this->getId();

			try {
				$resource_request = new Request($link);
				$this->parser()->setParsePage($resource_request->response());
			}
			catch (CurlErrorException $e) {
				Signal::msg("Curl error in request for ".get_class()." ".$e->getMessage());
			}
		}
	}

	public function parse()
	{
		$this->setParser();
		$this->getParsablePage();
		$this->use_parser();
	}

	abstract protected function use_parser();
}
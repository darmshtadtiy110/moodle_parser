<?php


namespace Resources;


use General\Signal;
use Request\Request;
use Parser\Parser;
use Parser\Resources\FinishedAttemptParser;
use Parser\Resources\CourseParser;
use Parser\Resources\QuizParser;

use Request\CurlErrorException;

trait Parsable
{
	/** @var Parser */
	protected $parser;

	/** @var Request */
	protected $last_request;

	/**
	 * @return Parser|CourseParser|QuizParser|FinishedAttemptParser
	 */
	public function parser()
	{
		return $this->parser;
	}

	protected function setParser()
	{
		// get resource name
		//$resource_class = Tools::get_object_class_name($this);
		$resource_class = get_class($this);
		// find needed parser
		$parser_class = "\\Parser\\".$resource_class."Parser";
		$this->parser = new $parser_class();
	}

	protected function request_resource()
	{
		if( $this->link != "")
		{
			try {
				$this->last_request = new Request($this->link);
			}
			catch (CurlErrorException $e) {
				Signal::msg("Curl error in request for ".get_class()." ".$e->getMessage());
			}
		}
	}

	public function parse()
	{
		$this->request_resource();
		$this->parser->setParsePage($this->last_request->response());
		$this->use_parser();
	}

	abstract protected function use_parser();
}
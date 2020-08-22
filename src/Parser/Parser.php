<?php


namespace MoodleParser\Parser;


use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use DOMElement;
use MoodleParser\General\Signal;

abstract class Parser
{
	/** @var Document */
	protected $parse_page;

	/**
	 * @param Document $parse_page
	 */
	public function setParsePage($parse_page)
	{
		$this->parse_page = $parse_page;
	}

	/**
	 * @return Document
	 */
	public function getParsePage()
	{
		return $this->parse_page;
	}

	public function purgePage()
	{
		$this->parse_page = null;
	}

	/**
	 * @param $expression
	 * @return Element[]|DOMElement[]
	 */
	public function find($expression)
	{
		$element = false;
		try {
			$element = $this->parse_page->find($expression);
		}
		catch (InvalidSelectorException $e) {
			Signal::msg($e->getMessage());
			Signal::msg($e->getTraceAsString());
		}

		return $element;
	}

	public static function parseExpressionFromLink($exp, $link)
	{
		$link_array = parse_url($link);
		if(array_key_exists("query", $link_array))
		{
			parse_str($link_array["query"], $link_query);
			if(array_key_exists($exp, $link_query) ) return $link_query[$exp];
		}
		return false;
	}
}
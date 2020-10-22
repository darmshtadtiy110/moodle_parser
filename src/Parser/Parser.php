<?php


namespace MoodleParser\Parser;


use DiDom\Document;
use DiDom\Element;
use DiDom\Exceptions\InvalidSelectorException;
use DiDom\Node;
use DOMElement;
use MoodleParser\FileSystem\Page;
use MoodleParser\General\Signal;

abstract class Parser
{
	/** @var Document */
	protected $parse_page;

	/**
	 * @param Document $parse_page
	 */
	public function setParsePage(Document $parse_page)
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
	 * @param Element[]|DOMElement[]|Node $element
	 * @return Element[]|DOMElement[]
	 */
	public function find($expression, $element = null)
	{
		$sought = false;

		try {
			if(isset($element))
				$sought = $element->find($expression);
			else
				$sought = $this->parse_page->find($expression);
		}
		catch (InvalidSelectorException $e) {
			Signal::msg($e->getMessage());
			Signal::msg($e->getTraceAsString());
		}

		return $sought;
	}

	public function savePage()
    {
        $page = new Page($this->parse_page->html());
        $page->save();
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
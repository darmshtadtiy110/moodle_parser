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
	/** @var Document|Element */
	protected $document;

	public function __construct($document)
	{
		$this->document = $document;
	}

	/**
	 * @deprecated
	 * @param Document $document
	 */
	public function setParsePage(Document $document)
	{
		$this->document = $document;
	}

	/**
	 * @return Document
	 */
	public function getParsePage()
	{
		return $this->document;
	}

	public function purgePage()
	{
		$this->document = null;
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
				$sought = $this->document->find($expression);
		}
		catch (InvalidSelectorException $e) {
			Signal::msg($e->getMessage());
			Signal::msg($e->getTraceAsString());
		}

		return $sought;
	}

	public function savePage()
    {
        $page = new Page($this->document->html());
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
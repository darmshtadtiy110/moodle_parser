<?php


namespace Parser;


use DiDom\Document;

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

	protected function parseIdFromLink($link)
	{
		$link_array = parse_url($link);
		parse_str($link_array["query"], $link_query);

		if(isset($link_query["id"])) return $link_query["id"];
		return false;
	}
}
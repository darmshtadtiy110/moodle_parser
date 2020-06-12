<?php


namespace Parser;


use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;

abstract class Parser
{
	/** @var Document */
	protected $parse_page;

	/**
	 * Parser constructor.
	 * @param Document $parse_page
	 * @throws LoginNeededException
	 */
	function __construct(Document $parse_page)
	{
		$this->parse_page = $parse_page;

		if($this->getLoginResults() !== true)
			throw new LoginNeededException($this->getLoginError());
	}

	/**
	 * @return bool|string
	 */
	public function getLoginResults()
	{
		$login_nodes = [];

		try {
			$login_nodes = $this->parse_page->find(".login");
		}
		catch (InvalidSelectorException $e) {
			echo "Wrong selector ". $e->getMessage();
		}

		if(empty($login_nodes))
			return true;

		return $login_nodes[0]->text();

	}

	/**
	 * @return bool|string
	 */
	public function getLoginError()
	{
		try {
			$error_nodes = $this->parse_page->find("span.error");
		}
		catch (InvalidSelectorException $e) {}

		if(empty($error_nodes))
			return false;

		return $error_nodes[0]->text();
	}

	protected function parseIdFromLink($link)
	{
		$link_array = parse_url($link);
		parse_str($link_array["query"], $link_query);

		if(isset($link_query["id"])) return $link_query["id"];
		return false;
	}
}
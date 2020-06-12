<?php


namespace General;

use \phpQuery;

class ParserCallbackFunctions
{
	public static function AllCourses($multi, $channels, $result_file)
	{
		foreach ($channels as $url => $channel)
		{
			curl_multi_remove_handle($multi, $channel);

			// load current page as phpQuery instance
			$newPage = phpQuery::newDocument(curl_multi_getcontent($channel));

			// finding coursename and author link
			$courseFullName = $newPage->find("h3.coursename>a")->html();
			$ownerProfileUrl = $newPage->find("ul.teachers>li>a")->attr("href");

			// for prevent RAM overload need to unload document from memory
			$newPage->unloadDocument();

			if(
				$courseFullName != ""
				//$profileFullName != "Користувач"
			)
			{
				fputcsv(
					$result_file,
					[
						$url,
						$courseFullName,
						$ownerProfileUrl
					]
				);
			}
		}
	}

	public static function AllUsers($multi, $channels, $result_file)
	{
		foreach ($channels as $url => $channel)
		{
			curl_multi_remove_handle($multi, $channel);

			// load current page as phpQuery instance
			$newPage = phpQuery::newDocument(curl_multi_getcontent($channel));

			$profileFullName = $newPage->find("div.name>button")->html();

			// for prevent RAM overload need to unload document from memory
			$newPage->unloadDocument();

			if(
				$profileFullName != "" &&
				$profileFullName != "Користувач"
			)
			{
				fputcsv(
					$result_file,
					[
						$url,
						$profileFullName
					]
				);
			}

		}
	}
}
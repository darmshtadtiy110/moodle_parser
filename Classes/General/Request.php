<?php


namespace General;
use \DiDom\Document;
use Exception;

class RequestOLD
{
	private static $auth_url = "/login/index.php";

	private static $start_attempt = "/mod/quiz/startattempt.php";

	private static $process_attempt = "/mod/quiz/processattempt.php";

	/**
	 * @param $full_url
	 * @param array $post_fields
	 * @return Document
	 * @throws Exception
	 */
	private static function MakeRequest($full_url, $post_fields = [])
	{
		$channel = curl_init();

		if(is_array($post_fields) && count($post_fields) > 0)
		{
			curl_setopt($channel, CURLOPT_POST, 1);
			curl_setopt($channel, CURLOPT_POSTFIELDS, $post_fields);
		}

		curl_setopt($channel, CURLOPT_URL, $full_url);
		curl_setopt($channel, CURLOPT_HEADER, 0);
		curl_setopt($channel, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($channel, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($channel, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($channel, CURLOPT_COOKIEJAR, FileSystem::cookies());
		curl_setopt($channel, CURLOPT_COOKIEFILE, FileSystem::cookies());

		$html = curl_exec($channel);

		if(curl_errno($channel)) throw new Exception (curl_error($channel));

		curl_close($channel);

		return new Document($html);
	}

	public static function Multi($url_list, $callback_func, $result_file_handle)
	{
		$curl_multi_instance = curl_multi_init();

		$multi_channels = [];

		foreach ($url_list as $url)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,            $url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($ch, CURLOPT_MAXCONNECTS,    10);
			curl_setopt($ch, CURLOPT_HEADER,         false);
			curl_setopt($ch, CURLOPT_FAILONERROR,    true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_COOKIEJAR,      FileSystem::cookies());
			curl_setopt($ch, CURLOPT_COOKIEFILE,     FileSystem::cookies());

			curl_multi_add_handle($curl_multi_instance, $ch);

			$multi_channels[$url] = $ch;
		}

		$active = null;

		do {
			$mrc = curl_multi_exec($curl_multi_instance, $active);
		}
		while($mrc == CURLM_CALL_MULTI_PERFORM);


		while($active && $mrc == CURLM_OK)
		{
			if(curl_multi_select($curl_multi_instance) == -1)
				continue;

			do {
				$mrc = curl_multi_exec($curl_multi_instance, $active);
			}
			while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}

		call_user_func(["\General\ParserCallbackFunctions", $callback_func], $curl_multi_instance, $multi_channels, $result_file_handle);

		curl_multi_close($curl_multi_instance);
	}

	/**
	 * @param $login
	 * @param $password
	 * @return bool|Document
	 */
	public static function Login($login, $password)
	{
		$post_fields = [
			"username" => $login,
			"password" => $password
		];

		$start_page = false;

		$auth_full_url = Parser::$target.self::$auth_url;

		try {
			$start_page = self::MakeRequest($auth_full_url, $post_fields);
		}
		catch (Exception $e) {
			echo "Auth request exception: ".$e->getMessage()."\n";
		}

		return $start_page;
	}

	public static function Logout()
	{
		return false;
	}

	/**
	 * @param $post_fields
	 * @return Document
	 */
	public static function StartAttempt($post_fields)
	{
		$res = false;

		try {
			$res = self::MakeRequest(Parser::$target.self::$start_attempt, $post_fields);
		}
		catch (Exception $e) {
			echo "StartAttempt exception ".$e->getMessage();
		}

		return $res;
	}

	/**
	 * @deprecated Run MakeRequest instead
	 * @param $full_url
	 * @return Document
	 */
	public static function Page($full_url)
	{
		$res = false;
		try {
			$res = self::MakeRequest($full_url);
		}
		catch (Exception $e) {
			echo "Get page exception ".$e->getMessage();
		}
		return $res;
	}

	/**
	 * @param array $form_fields
	 * @return bool|Document
	 */
	public static function ProcessAttempt($form_fields = [])
	{
		$res = false;

		try {
			$res = self::MakeRequest(Parser::$target.self::$process_attempt, $form_fields);
		}
		catch (Exception $e) {
			echo "ProcessAttempt request exception: ".$e->getMessage();
		}

		return $res;
	}
}
<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Log;

class Signal
{
	public static $login_successful = "You successful login as: ";

	public static function login_successful($name)
	{
		//echo self::$login_successful.$name."\n";
		self::log($name);

	}

	public static function msg($msg)
	{
		//echo $msg."\n";
		self::log($msg);
	}

	public static function log($msg)
	{
		$log = new Log($msg);
		$log->save();
	}
}
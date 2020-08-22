<?php


namespace General;


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
		$file = fopen("../../storage/logs/moodle_parser.log", "w+");

		file_put_contents($file, $msg);
	}
}
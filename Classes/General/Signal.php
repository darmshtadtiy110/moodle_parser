<?php


namespace General;


class Signal
{
	public static $login_successful = "You successful login as: ";

	public static function login_successful($name)
	{
		echo self::$login_successful.$name."\n";
	}

	public static function msg($msg)
	{
		echo $msg."\n";
	}
}
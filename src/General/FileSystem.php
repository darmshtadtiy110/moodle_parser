<?php


namespace MoodleParser\General;


class FileSystem
{
	private static $storage   = "/storage";
	private static $cache     = "/cache";
	private static $cookies   = "/storage/cookies/cookies.txt";
	private static $pages     = "/cache/pages";
	private static $results   = "/storage/result";
	private static $passports = "/storage/passports/passports.csv";

	public static function storage()
	{
		return getenv("ROOT_PATH").self::$storage;
	}

	public static function cache()
	{
		return getenv("ROOT_PATH").self::$cache;
	}

	public static function cookies()
	{
		return getenv("ROOT_PATH").self::$cookies;
	}

	public static function pages()
	{
		return getenv("ROOT_PATH").self::$pages;
	}

	public static function results()
	{
		return getenv("ROOT_PATH").self::$results;
	}

	public static function passports()
	{
		return __DIR__."/../..".self::$passports;
	}
}
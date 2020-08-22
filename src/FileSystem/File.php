<?php


namespace MoodleParser\FileSystem;


abstract class File
{

	protected $name;

	protected $path;

	function __construct($name)
	{
		$this->name = $name;

		FileManager::create($this);
	}

	public function getFullPath()
	{
		return __DIR__."/../..".$this->path.$this->name;
	}
}
<?php


namespace MoodleParser\FileSystem;


abstract class File
{
	protected $name;

	protected $path;

	/** @var mixed */
	private $content;

	function __construct($name, $content = null)
	{
		$this->name = $name;
        $this->content = $content;

		FileManager::create($this);
	}

	public function content($data = null)
    {
        if($data === null) return $this->content;
        else $this->content = $data;
        return $this;
    }

	public function getFullPath()
	{
		return __DIR__."/../..".$this->path.$this->name;
	}

	public function save()
    {
        FileManager::save($this);
    }

}
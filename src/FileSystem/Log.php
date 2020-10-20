<?php


namespace MoodleParser\FileSystem;


class Log extends File
{
    protected $path = "/storage/logs/";

    public function __construct($content = "")
    {
        $name = date("Y-m-d_H:i:s").".log";
        parent::__construct($name, $content);
    }
}
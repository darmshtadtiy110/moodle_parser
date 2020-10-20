<?php


namespace MoodleParser\FileSystem;


class Page extends File
{
    protected $path = "/storage/pages/";

    public function __construct($content = null)
    {
        $name = date("Y-m-d_H:i:s").".htm";
        parent::__construct($name, $content);
    }
}
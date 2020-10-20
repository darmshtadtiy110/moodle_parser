<?php


namespace MoodleParser\FileSystem;


class FileManager
{
	/**
	 * @param File $file
	 * @return bool|resource
	 */
	public static function create(File $file)
	{
		return fopen($file->getFullPath(), "c");
	}

	public static function save(File $file)
	{
        $file_handle = fopen($file->getFullPath(), "w");

        fwrite($file_handle, $file->content());

        return $file;
	}

	public static function delete(File $file)
	{
		//
	}

	/**
	 * @param File $file
	 * @return boolean
	 */
	public static function isExist(File $file)
	{
		return true;
	}
}
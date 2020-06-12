<?php

namespace General;


class Tools
{
	public static function replaceable_echo($message, $force_clear_lines = NULL)
	{
		static $last_lines = 0;

		if(!is_null($force_clear_lines))
			$last_lines = $force_clear_lines;

		$term_width = exec('tput cols', $toss, $status);
		if($status) {
			$term_width = 64; // Arbitrary fall-back term width.
		}

		$line_count = 0;
		foreach(explode("\n", $message) as $line) {
			$line_count += count(str_split($line, $term_width));
		}

		// Erasure MAGIC: Clear as many lines as the last output had.
		for($i = 0; $i < $last_lines; $i++) {
			// Return to the beginning of the line
			echo "\r";
			// Erase to the end of the line
			echo "\033[K";
			// Move cursor Up a line
			echo "\033[1A";
			// Return to the beginning of the line
			echo "\r";
			// Erase to the end of the line
			echo "\033[K";
			// Return to the beginning of the line
			echo "\r";
			// Can be consolodated into
			// echo "\r\033[K\033[1A\r\033[K\r";
		}

		$last_lines = $line_count;

		echo $message."\n";
	}

	public static function get_class_name($classname)
	{
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
	}
}

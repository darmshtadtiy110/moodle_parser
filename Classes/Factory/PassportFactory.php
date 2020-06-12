<?php


namespace Factory;


use General\FileSystem;
use General\Passport;

class PassportFactory
{
	public static function create($id)
	{
		$passports = [];

		if (($handle = fopen(FileSystem::passports(), "r")) !== FALSE)
		{
			while (($data = fgetcsv($handle, 50, ";")) !== FALSE)

				$passports[ (int) $data[0] ] = [
					"login"    => $data[1],
					"password" => $data[2]
				];

			fclose($handle);
		}

		return new Passport($id, $passports[$id]["login"], $passports[$id]["password"]);
	}
}
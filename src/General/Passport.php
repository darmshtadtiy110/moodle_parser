<?php


namespace General;


class Passport
{
	/**
	 * Class is deprecated
	 */

	private $id;

	private $login;

	private $password;

	function __construct($id, $login, $password)
	{
		$this->id = $id;
		$this->login = $login;
		$this->password = $password;
	}

	public function id()
	{
		return $this->id;
	}

	public function login()
	{
		return $this->login;
	}

	public function password()
	{
		return $this->password;
	}

	public static function GetById($id)
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
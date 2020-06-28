<?php


namespace General;


class Passport
{
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
}
<?php


namespace Request;


class Login extends Request
{
	public function __construct($login, $password)
	{
		parent::__construct(
			Properties::$login_url,
			[
				"username" => $login,
				"password" => $password
			]
		);
	}
}
<?php


namespace Request;


class Login extends Request
{
	public function __construct($login, $password, $cookies)
	{
		parent::__construct(
			Properties::$login_url,
			$cookies,
			[
				"username" => $login,
				"password" => $password
			]
		);
	}
}
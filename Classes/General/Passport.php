<?php


namespace General;


use Exception;
use FileSystem\Cookies;
use Resources\Student;
use Parser\StudentParser;
use Request\Login;

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

	/**
	 * @return Student
	 * @throws Exception
	 */
	public function auth()
	{
		$name = "";
		$course_list = "";

		$cookies = new Cookies($this->login.".txt");

		$login_request = new Login(
			$this->login,
			$this->password,
			$cookies
		);

		$student_parser = new StudentParser($login_request->response());

		if($student_parser->getLoginResults() === true)
		{
			try {
				$name = $student_parser->getUserText();
				$course_list = $student_parser->getCourseList();
			}
			catch (Exception $e) {
				echo $e->getMessage();
			}

		}
		else throw new Exception( $student_parser->getLoginError() );

		return new Student($this, $name, $course_list, $cookies);
	}
}
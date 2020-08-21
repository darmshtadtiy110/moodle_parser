<?php


namespace Resources;


use Exception;
use FileSystem\Cookies;
use General\Request;
use General\Resource;
use General\Signal;
use Parser\Resources\StudentParser;

class Student extends Resource
{
	/** @var Cookies */
	private $cookies;

	/** @var Course[] */
	private $course_list = [];

	/** @var StudentParser */
	private $parser;

	public function __construct(
		$id,
		$name,
		Cookies $cookies,
		$course_list = []
	)
	{
		$this->cookies = $cookies;

		$this->course_list = $course_list;

		$this->parser = new StudentParser();
		parent::__construct($id, $name);
	}

	public static function Auth($login, $password)
	{
		$parser = new StudentParser();

		$cookies = new Cookies($login . ".txt");

		$login_request = Request::Login(
			$login,
			$password,
			$cookies
		);

		$parser->setParsePage($login_request->response());

		$student = false;

		if($parser->getLoginResults())
		{
			$name = $parser->getUserText();
			$course_list = $parser->getCoursesArray();
			$id = $parser->getUserId();
			try {
				$student = new Student($id, $name, $cookies, $course_list);
			}
			catch (Exception $e) { Signal::msg($e->getMessage()); }
		}
		else { Signal::msg( $parser->getLoginError()); };
		return $student;
	}

	/**
	 * @return array
	 */
	public function getCourseList()
	{
		return $this->course_list;
	}

	/**
	 * @param $id
	 * @return Course
	 */
	public function getCourse($id)
	{
		return $this->course_list[$id];
	}

	/**
	 * @return Cookies
	 */
	public function cookies()
	{
		return $this->cookies;
	}
}
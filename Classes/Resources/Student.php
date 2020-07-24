<?php


namespace Resources;


use General\Properties;
use General\Signal;
use General\Request;
use General\Passport;
use General\Resource;

use FileSystem\Cookies;
use Factory\PassportFactory;

use Parser\Resources\StudentParser;

class Student extends Resource
{
	/** @var Student */
	private static $instance;

	/** @var Passport */
	private $passport;

	/** @var Cookies */
	private $cookies;

	/** @var Course[] */
	private $course_list = [];

	public function __construct(
		$id,
		$name,
		Passport $passport,
		Cookies $cookies,
		$course_list = []
	)
	{
		$this->passport = $passport;
		$this->cookies = $cookies;

		$this->course_list = $course_list;

		$this->parser = new StudentParser();
		parent::__construct($id, $name);
	}

	/**
	 * @param int $passport_id
	 * @return Student
	 */
	public static function getInstance($passport_id = null)
	{
		if($passport_id > 0)
		{
			$user_passport = PassportFactory::create($passport_id);

			self::$instance = self::Auth($user_passport);
		}

		return self::$instance;
	}

	public static function Auth(Passport $passport)
	{
		$parser = new StudentParser();

		$cookies = new Cookies($passport->login() . ".txt");

		$login_request = Request::Login(
			$passport->login(),
			$passport->password(),
			$cookies
		);

		$parser->setParsePage($login_request->response());

		if($parser->getLoginResults())
		{
			$name = $parser->getUserText();
			$course_list = $parser->getCoursesArray();
			$id = $parser->getUserId();

			return new Student($id, $name, $passport, $cookies, $course_list);
		}
		else { Signal::msg( $parser->getLoginError()); };
		return false;
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
	 * @return Passport | bool
	 */
	public function passport()
	{
		return $this->passport;
	}

	/**
	 * @return Cookies
	 */
	public function cookies()
	{
		return $this->cookies;
	}

	protected function getParsablePage()
	{
		$user_profile_request = new Request(
			Properties::Profile() . $this->id
		);
		$this->parser()->setParsePage($user_profile_request->response());
	}
}
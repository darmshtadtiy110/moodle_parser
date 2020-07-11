<?php


namespace Resources;


use General\Exceptions\CurlErrorException;
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

	public function __construct($id, $name = "", $course_list = [])
	{
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

		$login_request = Request::Login(
			$passport->login(),
			$passport->password()
		);

		$parser->setParsePage($login_request->response());

		if($parser->getLoginResults() === true)
		{
			$name = $parser->getUserText();
			$course_list = $parser->getCoursesArray();
			$id = $parser->getUserId();

			return new Student($id, $name, $course_list);
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
	 * @return bool|Course
	 */
	public function getCourse($id)
	{
		if(array_key_exists($id, $this->course_list))
		{
			return $this->course_list[$id];
		}
		return false;
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
		if (empty($this->cookies))
			$this->cookies = new Cookies($this->passport()->login() . ".txt");

		return $this->cookies;
	}

	protected function getParsablePage()
	{
		try {
			$user_profile_request = new Request(
				Properties::Profile() . $this->id
			);
			$this->parser()->setParsePage($user_profile_request->response());
		}
		catch (CurlErrorException $e) {}
	}

	protected function use_parser()
	{
		//TODO
	}
}
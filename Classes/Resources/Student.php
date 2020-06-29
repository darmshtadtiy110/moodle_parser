<?php


namespace Resources;


use Exception;
use Factory\PassportFactory;
use General\Passport;
use FileSystem\Cookies;
use General\Signal;
use Parser\StudentParser;
use Request\CurlErrorException;
use Request\Login;

class Student extends Resource
{
	use ParentResource, Parsable;

	/** @var Student */
	private static $instance;

	/** @var StudentParser */
	protected $parser;

	/** @var Passport */
	private $passport;

	/** @var Cookies */
	private $cookies;

	/** @var array */
	private $course_list = [];

	/**
	 * @param int $passport_id
	 * @return Student
	 */
	public static function getInstance($passport_id = null)
	{
		if( $passport_id > 0)
		{
			$user_passport = PassportFactory::create($passport_id);

			$student = new Student();
			$student->passport($user_passport);

			self::$instance = $student;
		}

		return self::$instance;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public function name($name = "")
	{
		if($name != "")
			$this->name = $name;

		return $this->name;
	}

	/**
	 * @param Passport | null $passport
	 * @return Passport | bool
	 */
	public function passport(Passport $passport = null)
	{
		if( $passport )
		{
			$this->passport = $passport;
		}

		return $this->passport;
	}

	/**
	 * @return Cookies
	 */
	public function cookies()
	{
		if( empty($this->cookies) )
			$this->cookies = new Cookies($this->passport()->login().".txt");

		return $this->cookies;
	}


	public function auth()
	{
		// TODO add id parsing
		try {
			$login_request = new Login(
				$this->passport()->login(),
				$this->passport()->password()
			);

			$this->parser()->setParsePage($login_request->response());
			$this->parse();

		}
		catch(CurlErrorException $e) { Signal::msg($e->getMessage()); }
	}

	public function parse()
	{
		if($this->parser()->getLoginResults() === true)
		{
			try {
				$this->name = $this->parser()->getUserText();

				$this->course_list = $this->parser()->getCoursesArray();
			}
			catch (Exception $e) {
				echo $e->getMessage();
			}

		}
		else { Signal::msg( $this->parser->getLoginError()); };
	}
}
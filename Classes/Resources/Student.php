<?php


namespace Resources;


use Exception;

use General\Signal;
use General\Request;
use General\Passport;
use General\Resource;

use FileSystem\Cookies;
use Factory\PassportFactory;

use Parser\Resources\StudentParser;

use Interfaces\ParentResource;

use Traits\ParentUtilities;

class Student extends Resource implements ParentResource
{
	use ParentUtilities;

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
		$this->parse();
	}

	protected function getParsablePage()
	{
		$login_request = Request::Login(
			$this->passport()->login(),
			$this->passport()->password()
		);
		$this->parser()->setParsePage($login_request->response());
	}

	protected function use_parser()
	{
		if($this->parser->getLoginResults() === true)
		{
			try {
				$this->name = $this->parser->getUserText();

				$this->course_list = $this->parser->getCoursesArray();
			}
			catch (Exception $e) {
				echo $e->getMessage();
			}

		}
		else { Signal::msg( $this->parser->getLoginError()); };
	}
}
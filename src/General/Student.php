<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Cookies;
use MoodleParser\Parser\Exceptions\AlreadyLogin;
use MoodleParser\Parser\Exceptions\ExpressionNotFound;
use MoodleParser\Parser\Exceptions\LoginError;
use MoodleParser\Parser\Exceptions\TokenDoesNotExist;
use MoodleParser\Parser\Resources\StudentParser;
use MoodleParser\Resources\Course;

class Student
{
    private $login;

    private $password;

	/** @var Cookies */
	private $cookies;

	public $id;

	public $name;

	public $last_time;

	public $session_key;

	/** @var Course[] */
	private $course_list = [];

	/** @var StudentParser */
	private $parser;

	/** @var RequestManager */
	private $request_manager;

	private $is_auth = false;

	/**
	 * Student constructor.
	 * @param $login
	 * @param $password
	 */
	public function __construct(
		$login,
        $password
	)
	{
	    $this->login = $login;
	    $this->password = $password;

		try {
			$this->auth();
			$this->loadStudentInfo();
		}
		catch (LoginError $e)
			{ echo $e->getMessage()."\n"; }
		catch (AlreadyLogin $e)
		{
			echo $e->getMessage()."\n";

			$homepage = $this->request()->homepage();

			$this->parser = new StudentParser($homepage->response());

			$this->loadStudentInfo();
		}
	}

	public function parser()
    {
        return $this->parser;
    }

    public function request()
    {
    	return $this->request_manager;
    }

	public function createCookies()
    {
        $cookies = new Cookies($this->login . ".txt");
        $this->cookies = $cookies;
    }

	/**
	 * @return Cookies
	 */
	public function cookies()
	{
		return $this->cookies;
	}

	public function isAuth()
	{
		return $this->is_auth;
	}

	/**
	 * @throws AlreadyLogin
	 * @throws LoginError
	 */
	public function auth()
	{
		echo "Auth as: ".$this->login."\n";

		$this->createCookies();

		$this->request_manager = new RequestManager($this->cookies());

		$this->parser = new StudentParser($this->request()->login()->response());

		try{
			$token = $this->parser->getToken();
		}
		catch (TokenDoesNotExist $e)
		{
			echo "Token does not exists. ".$e->getMessage()."\n";
			//$e->parser()->savePage();
		}

		$this->parser = new StudentParser($this->request()->login(
			$this->login,
			$this->password,
			$token
		)->response());

		$this->parser->savePage();


		if($this->parser->getLoginResults() == "Ви не пройшли ідентифікацію")
			throw new LoginError($this->parser, $this->parser->getLoginError());

		$this->loadStudentInfo();

		try {
			$already_login_msg = $this->parser->checkLoginInfo();
		}
		catch (ExpressionNotFound $e) {
			Signal::log($e->getMessage());
			$this->parser->savePage();
		}

		if(isset($already_login_msg))
			throw new AlreadyLogin($this->parser, $already_login_msg);

		$this->is_auth = true;
	}

	public function loadStudentInfo()
    {
        try {
            $this->name = $this->parser->getUserText();
            $this->course_list = $this->parser->getCoursesArray();
            $this->id = $this->parser->getUserId();
            $this->request_manager->setSessionKey($this->parser->getSessionKey());
        }
        catch (ExpressionNotFound $e) {
            Signal::log($e->getMessage());
            $this->parser->savePage();
        }

        return $this;
    }

	/**
	 * @return array
	 */
	public function courseList()
	{
		return $this->course_list;
	}


}
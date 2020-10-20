<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Cookies;
use MoodleParser\General\Exceptions\AlreadyLogin;
use MoodleParser\General\Exceptions\LoginError;
use MoodleParser\Parser\Exceptions\ExpressionNotFound;
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

	/** @var Course[] */
	private $course_list = [];

	/** @var StudentParser */
	private $parser;

	public function __construct(
		$login,
        $password
	)
	{
	    $this->login = $login;
	    $this->password = $password;
	    $this->createCookies();
		$this->parser = new StudentParser();
	}

	public function parser()
    {
        return $this->parser;
    }

	public function createCookies()
    {
        $cookies = new Cookies($this->login . ".txt");
        $this->cookies = $cookies;
    }

    /**
     * @return Student
     * @throws ExpressionNotFound
     * @throws LoginError|AlreadyLogin
     */
	public function auth()
	{
        $get_token_request = new Request(Properties::login(), [], $this->cookies);

		$this->parser->setParsePage($get_token_request->response());

		try {
		    $token = $this->parser->getToken();
            $login_request = Request::Login(
                $this->login,
                $this->password,
                $token,
                $this->cookies
            );

            $this->parser->setParsePage($login_request->response());

            if($this->parser->getLoginResults() == "Ви не пройшли ідентифікацію")
                throw new LoginError($this->parser->getLoginError());
            else $this->loadStudentInfo();
        }
        catch (ExpressionNotFound $e) {
		    $already_login_msg = $this->parser->checkLoginInfo();
            if($already_login_msg != "")
                throw new AlreadyLogin($already_login_msg);
		}

		return $this;
	}

	public function loadStudentInfo()
    {
        try {
            $this->name = $this->parser->getUserText();
            $this->course_list = $this->parser->getCoursesArray();
            $this->id = $this->parser->getUserId();
        }
        catch (ExpressionNotFound $e) {
            Signal::log($e);
            $this->parser->savePage();
        }

        return $this;
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
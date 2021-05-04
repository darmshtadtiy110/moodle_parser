<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Cookies;
use MoodleParser\Parser\Exceptions\AlreadyLogin;
use MoodleParser\Parser\Exceptions\ExpressionNotFound;
use MoodleParser\Parser\Exceptions\LoginError;
use MoodleParser\Parser\Exceptions\NewAttemptBan;
use MoodleParser\Parser\Exceptions\TokenDoesNotExist;
use MoodleParser\Parser\Resources\QuizParser;
use MoodleParser\Parser\Resources\StudentParser;
use MoodleParser\Resources\Course;
use MoodleParser\Resources\Exceptions\WrongResourceID;
use MoodleParser\Resources\FinishedAttempt;
use MoodleParser\Resources\ProcessingAttempt;
use MoodleParser\Resources\Quiz;

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

	/**
	 * Student constructor.
	 * @param $login
	 * @param $password
	 * @throws TokenDoesNotExist
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
		catch (TokenDoesNotExist $e)
			{ echo $e->getMessage()."\n"; }
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

	/**
	 * @return $this
	 * @throws AlreadyLogin
	 * @throws LoginError
	 * @throws TokenDoesNotExist
	 */
	public function auth()
	{
		$this->createCookies();

		$this->request_manager = new RequestManager($this->cookies());

		$this->parser = new StudentParser($this->request()->login()->response());

		$token = $this->parser->getToken();

		$this->parser = new StudentParser($this->request()->login(
			$this->login,
			$this->password,
			$token
		)->response());

		if($this->parser->getLoginResults() == "Ви не пройшли ідентифікацію")
			throw new LoginError($this->parser, $this->parser->getLoginError());

		else $this->loadStudentInfo();

		try {
			$already_login_msg = $this->parser->checkLoginInfo();
		}
		catch (ExpressionNotFound $e) {
			Signal::log($e->getMessage());
			$this->parser->savePage();
		}

		if(isset($already_login_msg))
			throw new AlreadyLogin($this->parser, $already_login_msg);


		return $this;
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
	public function getCourseList()
	{
		return $this->course_list;
	}

	/**
	 * @param $id
	 * @return Course|bool
	 * @throws WrongResourceID
	 */
	public function openCourse($id)
	{
		if(isset($this->course_list[$id]))
		{
			return new Course($this->request()->course($id)->response());
		}
		else throw new WrongResourceID("Course ".$id." does not exist in your course list");
	}

	public function openQuiz($id)
	{
		$parser = new QuizParser($this->request()->quiz($id)->response());

		$quiz = new Quiz(
			$id,
			$parser->getQuizName(),
			$parser->getAttemptList(),
			$parser->getSessionKey(),
			$parser->getTimer()
		);

		return $quiz;
	}

	/**
	 * @param $id
	 * @return FinishedAttempt
	 */
	public function openAttempt($id)
	{
		$attempt_review_document = $this->request()->attemptReview($id)->response();

		return new FinishedAttempt($attempt_review_document);
	}

	/**
	 * @param Quiz $quiz
	 * @return ProcessingAttempt
	 * @throws NewAttemptBan
	 */
	public function newAttempt(Quiz $quiz)
	{
		//TODO Remove code repeating
		$new_attempt_document = $this->request()->startAttempt(
			$quiz->getSessionKey(),
			$quiz->getId(),
			$quiz->getTimerExist()
		)->response();

		if($quiz->getTimerExist() == true)
		{
			$new_attempt_document = $this->request()->startAttempt(
				$quiz->getSessionKey(),
				$quiz->getId(),
				$quiz->getTimerExist()
			)->response();
		}

		return new ProcessingAttempt($new_attempt_document);
	}
}
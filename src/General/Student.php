<?php


namespace MoodleParser\General;


use MoodleParser\FileSystem\Cookies;
use MoodleParser\General\Exceptions\AlreadyLogin;
use MoodleParser\General\Exceptions\LoginError;
use MoodleParser\Parser\Exceptions\ExpressionNotFound;
use MoodleParser\Parser\Resources\CourseParser;
use MoodleParser\Parser\Resources\FinishedAttemptParser;
use MoodleParser\Parser\Resources\ProcessingAttemptParser;
use MoodleParser\Parser\Resources\QuestionParser;
use MoodleParser\Parser\Resources\QuizParser;
use MoodleParser\Parser\Resources\StudentParser;
use MoodleParser\Resources\Course;
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

	/** @var Course[] */
	private $course_list = [];

	/** @var StudentParser */
	private $parser;

	/** @var RequestManager */
	private $request_manager;

	public function __construct(
		$login,
        $password
	)
	{
	    $this->login = $login;
	    $this->password = $password;
	    $this->createCookies();
	    $this->request_manager = new RequestManager($this->cookies);
		$this->parser = new StudentParser();
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
     * @return Student
     * @throws LoginError|AlreadyLogin
     */
	public function auth()
	{
		try {
			$this->parser->setParsePage($this->request()->login()->response());

			$token = $this->parser->getToken();

            $this->parser->setParsePage($this->request()->login(
	            $this->login,
	            $this->password,
	            $token
            )->response());

            if($this->parser->getLoginResults() == "Ви не пройшли ідентифікацію")
                throw new LoginError($this->parser->getLoginError());
            else $this->loadStudentInfo();
        }
        catch (ExpressionNotFound $e) {

			try {
				$already_login_msg = $this->parser->checkLoginInfo();
				if($already_login_msg != "")
					throw new AlreadyLogin($already_login_msg);
			}
            catch (ExpressionNotFound $e) {}
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
	 * @return Course|bool
	 */
	public function openCourse($id)
	{
		if(isset($this->course_list[$id]))
		{
			$parser = new CourseParser();

			$parser->setParsePage($this->request()->course($id)->response());

			$course = new Course(
				$id,
				$parser->getCourseName(),
				$parser->getQuizList()
			);
			return $course;
		}

		else return false;
	}

	public function openQuiz($id)
	{
		$parser = new QuizParser();

		$parser->setParsePage($this->request()->quiz($id)->response());

		$quiz = new Quiz(
			$id,
			$parser->getQuizName(),
			$parser->getAttemptList(),
			$parser->getSessionKey(),
			$parser->getTimer()
		);

		return $quiz;
	}

	public function openAttempt($id)
	{
		$attempt_review_request = $this->request()->attemptReview($id)->response();

		$attempt_parser = new FinishedAttemptParser();
		$questions_parser = new QuestionParser();

		$attempt_parser->setParsePage($attempt_review_request);
		$questions_parser->setParsePage($attempt_review_request);

		$attempt = new FinishedAttempt(
			$id,
			$attempt_parser->getGrade(),
			$attempt_parser->getName(),
			$questions_parser->parseQuestions()
		);

		return $attempt;
	}

	public function newAttempt(Quiz $quiz)
	{
		$attempt_parser = new ProcessingAttemptParser();

		$attempt_parser->setParsePage($this->request()->startAttempt(
			$quiz->getSessionKey(),
			$quiz->getId(),
			$quiz->getTimerExist()
		)->response());

		if($quiz->getTimerExist() == true)
		{
			$attempt_parser->setParsePage($this->request()->startAttempt(
				$quiz->getSessionKey(),
				$quiz->getId(),
				$quiz->getTimerExist()
			)->response());
		}

		$new_attempt = new ProcessingAttempt(
			$attempt_parser->getAttemptId(),
			$this,
			$attempt_parser
		);

		return $new_attempt;
	}
}
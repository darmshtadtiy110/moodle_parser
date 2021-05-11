<?php


namespace MoodleParser\Processors;


use MoodleParser\General\Student;
use MoodleParser\Parser\Exceptions\NewAttemptBan;
use MoodleParser\Resources\Attempt;
use MoodleParser\Resources\Course;
//use MoodleParser\Resources\Exceptions\WrongResourceID;
use MoodleParser\Resources\FinishedAttempt;
use MoodleParser\Resources\ProcessingAttempt;
use MoodleParser\Resources\Quiz;

class AttemptProcessor
{
	/** @var Student */
	private $student;

	/** @var ProcessingAttempt */
	private $attempt;

	/** @var QuestionProcessor */
	private $question_processor;

	/** @var array  */
	private $form_inputs = [];

	public function __construct(Student $student)
	{
		$this->student = $student;
	}

	private function attempt(Attempt $attempt = null)
	{
		if(!is_null($attempt))
			$this->attempt = $attempt;
	}

	public function questionProcessor(QuestionProcessor $proc = null)
	{
		if(!is_null($proc))
			$this->question_processor = $proc;

		return $this->question_processor;
	}

	public function request()
	{
		return $this->student->request();
	}

	/**
	 * @param $id
	 * @return Course|bool
	 */
	public function openCourse($id)
	{
	    /*
		if(isset($this->student()->courseList()[$id]))
		{
			return new Course($this->request()->course($id)->response());
		}
		else throw new WrongResourceID("Course ".$id." does not exist in student's course list");*/
        return new Course($this->request()->course($id)->response());
	}

	public function openQuiz($id)
	{
		return new Quiz($this->request()->quiz($id)->response());
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
	 * @throws NewAttemptBan
	 */
	public function newAttempt(Quiz $quiz)
	{
		//TODO Remove code repeating
		$new_attempt_document = $this->request()->startAttempt(
			$quiz->getSessionKey(),
			$quiz->id(),
			$quiz->getTimerExist()
		)->response();

		if($quiz->getTimerExist() == true)
		{
			$new_attempt_document = $this->request()->startAttempt(
				$quiz->getSessionKey(),
				$quiz->id(),
				$quiz->getTimerExist()
			)->response();
		}

		$this->attempt(new ProcessingAttempt($new_attempt_document));
	}

	private function process()
	{
		$current = $this->attempt->getCurrentQuestions();

		$this->form_inputs = $this->attempt->parser()->parseForm();

		foreach ($current as $question)
		{
			$selected_variant = $this->questionProcessor()->choiceVariant($question);
			$question->selectVariant($selected_variant);

			$question->setCurrent(false);

			$this->attempt->setQuestion($question);
			$this->form_inputs[$selected_variant->getInputName()] = $selected_variant->getInputValue();

		}
		$this->goToNextPage();
		$this->attempt->use_parser();
	}

	private function goToNextPage()
	{
		$next_page_request =
			$this->student()
				->request()
				->processAttempt($this->form_inputs);

		$this->attempt->parser($next_page_request->response());
	}

	public function processAllQuestions()
	{
		do {
			$status = $this->attempt->parser()->getQuestionsStatus();

			if($status["saved"] == $status["total"]) break;

			$this->process();
		}
		while ($status["saved"] < $status["total"]-1);

		$this->finish();
	}

	private function finish()
	{
		$summary_page_request =
			$this->student()
				->request()
				->finishAttempt($this->attempt->id());

		$this->attempt->parser($summary_page_request->response());

		$this->form_inputs = $this->attempt->parser()->parseForm("finish_attempt");

		$this->goToNextPage();
	}

	private function student()
	{
		return $this->student;
	}
}
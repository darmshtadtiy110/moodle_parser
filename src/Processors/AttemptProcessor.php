<?php


namespace MoodleParser\Processors;


use MoodleParser\General\Student;
use MoodleParser\Resources\ProcessingAttempt;

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

	public function __construct(Student $student, ProcessingAttempt $attempt, QuestionProcessor $question_processor)
	{
		$this->student = $student;
		$this->attempt = $attempt;
		$this->question_processor = $question_processor;
	}

	private function questionProcessor()
	{
		return $this->question_processor;
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
			$this->getStudent()
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
			$this->getStudent()
				->request()
				->finishAttempt($this->attempt->getId());

		$this->attempt->parser($summary_page_request->response());

		$this->form_inputs = $this->attempt->parser()->parseForm("finish_attempt");

		$this->goToNextPage();
	}

	private function getStudent()
	{
		return $this->student;
	}
}
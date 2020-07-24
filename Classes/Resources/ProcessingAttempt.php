<?php


namespace Resources;


use General\Request;

use Parser\Resources\ProcessingAttemptParser;
use Parser\Resources\QuestionParser;

use AttemptProcessor\Processor;

class ProcessingAttempt extends Attempt
{
	/** @var ProcessingAttemptParser */
	protected $parser;

	/** @var QuestionParser */
	protected $question_parser;

	/** @var Processor */
	protected $processor;

	/** @var string */
	private $session_key;

	/** @var int */
	private $quiz_id;

	/** @var bool */
	private $timer_exist = false;

	/** @var Question[] */
	private $question_list = [];

	private $form_inputs = [];

	public function __construct(
		$id,
		$session_key,
		$quiz_id,
		$timer_exist,
		ProcessingAttemptParser $parser
	)
	{
		$this->session_key = $session_key;
		$this->quiz_id = $quiz_id;
		$this->timer_exist = $timer_exist;

		$this->parser = $parser;

		$this->question_parser = new QuestionParser();

		parent::__construct($id);

		$this->use_parser();
	}

	public function setProcessor(Processor $processor)
	{
		$this->processor = $processor;
	}

	/**
	 * @return array
	 */
	public function getQuestionList()
	{
		return $this->question_list;
	}

	/**
	 * @return array
	 */
	public function getFormInputs()
	{
		return $this->form_inputs;
	}

	protected function use_parser()
	{
		$this->question_parser->setParsePage($this->parser->getParsePage());
		$current_questions_array = $this->question_parser->parseQuestions();

		foreach ($current_questions_array as $question)
		{
			$question->setCurrent(true);
			$this->setQuestion($question);
		}

		$this->form_inputs = $this->parser->parseForm();
	}

	/**
	 * @param int $number
	 * @return Question|bool
	 */
	public function getQuestion($number)
	{
		if(array_key_exists($number, $this->question_list))
			return $this->question_list[$number];
		else return false;
	}

	/**
	 * @param Question $question
	 */
	public function setQuestion(Question $question)
	{
		$this->question_list[$question->getNumber()] = $question;
	}

	/**
	 * @return Question[]
	 */
	public function getCurrentQuestions()
	{
		$current = [];
		foreach ($this->question_list as $question)
		{
			if($question->isCurrent()) $current[] = $question;
		}
		return $current;
	}

	public function process()
	{
		$current = $this->getCurrentQuestions();

		foreach ($current as $question)
		{
			$selected_variant = $this->processor->choiceVariant($question);

			$question->selectVariant($selected_variant);
			$question->setCurrent(false);

			$this->setQuestion($question);
			$this->form_inputs[$selected_variant->getInputName()] = $selected_variant->getInputValue();
		}

		$this->processPage();
		$this->use_parser();

	}

	private function processPage()
	{
		$next_page_request = Request::ProcessAttempt($this->form_inputs);
		$this->parser->setParsePage($next_page_request->response());
	}

	public function processAllQuestions()
	{
		do {
			$status = $this->parser->getQuestionsStatus();

			if($status["saved"] == $status["total"]) break;

			var_dump($status);

			$this->process();

		}
		while ($status["saved"] < $status["total"]-1);

		$this->finish();
	}

	public function finish()
	{
		$summary_page_request = Request::FinishAttempt($this->getId());
		$this->parser->setParsePage($summary_page_request->response());

		$this->form_inputs = $this->parser->parseForm("finish_attempt");

		$this->processPage();
	}
}
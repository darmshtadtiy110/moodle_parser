<?php


namespace MoodleParser\Resources;


use DiDom\Document;
use MoodleParser\Parser\Exceptions\NewAttemptBan;
use MoodleParser\Parser\Resources\AttemptParser;

class ProcessingAttempt extends Attempt
{
	/** @var AttemptParser */
	protected $parser;

	/** @var Question[] */
	private $question_list = [];

	/**
	 * ProcessingAttempt constructor.
	 * @param Document $new_attempt_document
	 * @throws NewAttemptBan
	 */
	public function __construct(Document $new_attempt_document)
	{
		$this->parser($new_attempt_document);

		parent::__construct($this->parser->getProcessingAttemptId());

		$this->use_parser();
	}

	/**
	 * @param Document|null $new_attempt_document
	 * @return AttemptParser
	 *
	public function parser(Document $new_attempt_document = null)
	{
		if(!is_null($new_attempt_document))
			$this->parser = new AttemptParser($new_attempt_document);

		return $this->parser;
	}*/

	/**
	 * @return array
	 */
	public function getQuestionList()
	{
		return $this->question_list;
	}

	public function use_parser()
	{
		$current_questions_array = $this->parser()->getQuestionsArray();

		foreach ($current_questions_array as $question)
		{
			$question->setCurrent(true);
			$this->setQuestion($question);
		}
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
}
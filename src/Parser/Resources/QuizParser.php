<?php


namespace Parser\Resources;

use DiDom\Exceptions\InvalidSelectorException;
use Exception;
use MoodleParser\General\Properties;
use MoodleParser\General\Request;
use MoodleParser\General\Signal;
use MoodleParser\Parser\Parser;
use MoodleParser\Resources\Quiz;

class QuizParser extends Parser
{
	public function getQuizName()
	{
		return $this->find("h2")[0]->text();
	}

	public function getSessionKey()
	{
		$session = "";

		try{
			$session = $this->parse_page->find("form>div>input[name=sesskey]")[0]->attr("value");
		}
		catch (Exception $e) {
			echo "setSessionKey exception: ".$e->getMessage();
		}

		return ($session === "") ? false : $session;
	}

	public function getQuizId()
	{
		$quiz_id = "";

		try{
			$quiz_id = $this->parse_page->find("form>div>input[name=cmid]")[0]->attr("value");
		}
		catch (Exception $e) {
			echo "setQuizId exception: ".$e->getMessage();
		}

		return ($quiz_id === "") ? false : $quiz_id;
	}

	public function getTimer()
	{
		$timer_exist = false;

		try {
			$quiz_info = $this->parse_page->find("div.box.quizinfo>p");
		}
		catch (Exception $e) { echo "isTimer exception: ".$e->getMessage(); }

		if(
			!empty($quiz_info) &&
			"Обмеження в часі" === substr($quiz_info[0]->text(), 0, 30)
		)
			$timer_exist = true;

		return $timer_exist;
	}

	public function getAttemptList()
	{
		$attempt_list = [];
		$attempt_table = $this->find("table.generaltable.quizattemptsummary>tbody>tr");

		foreach ($attempt_table as $attempt_tr)
		{
			try {
				$index  = (int) $attempt_tr->find("td.c0")[0]->text();

				$name_column = $attempt_tr->find("td.c1")[0];

				if(empty($name_column->find("span")))
					$name = $name_column->text();
				else
					$name = $name_column->find("span")[0]->text();

				$grade  = $attempt_tr->find("td.c2");
				$review = $attempt_tr->find("td.c3>a");

				if( empty($review) )
				{
					$attempt_list["processing"] = [
						"index" => $index,
						"name" => $name,
						"state" => "processing"
					];
				}
				else {
					$attempt_review_link = $review[0]->attr("href");
					$id = Parser::parseExpressionFromLink("attempt", $attempt_review_link);
					$grade = (int) $grade[0]->text();

					$attempt_list[$id] = [
						"index" => $index,
						"name" => $name,
						"state" => "finished",
						"grade" => $grade
					];
				}
			}
			catch (InvalidSelectorException $e) { echo "loadAttemptList exception ".$e->getMessage(); }
		}
		return $attempt_list;
	}

	/**
	 * @param $id
	 * @return Quiz
	 */
	public static function GetById($id)
	{
		$link = Properties::Quiz().$id;
		$parser = new QuizParser();

		$resource_request = new Request($link);

		$parser->setParsePage($resource_request->response());

		$quiz = false;
		try {
			$quiz = new Quiz(
				$id,
				$parser->getQuizName(),
				$parser->getAttemptList(),
				$parser->getSessionKey(),
				$parser->getTimer()
			);
		}
		catch (Exception $e) { Signal::msg($e->getMessage()); }

		return $quiz;
	}
}
<?php


namespace Parser\Resources;


use Parser\Parser;
use DiDom\Exceptions\InvalidSelectorException;
use Exception;
use Resources\FinishedAttempt;
use Resources\ProcessingAttempt;

class QuizParser extends Parser
{
	public  function getSessionKey()
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

	public function  getAttemptList()
	{
		$attempt_list = [];

		try {
			$attempt_table = $this->parse_page->find("table.generaltable.quizattemptsummary>tbody>tr");

			foreach ($attempt_table as $attempt_tr)
			{
				$index  = (int) $attempt_tr->find("td.c0")[0]->text();

				$name = $attempt_tr->find("td.c1")[0]->text();

				$grade  = $attempt_tr->find("td.c2");
				$review = $attempt_tr->find("td.c4>a");

				if( !empty($review) )
				{
					$attempt_review_link = $review[0]->attr("href");
					$id = Parser::parseExpressionFromLink("id", $attempt_review_link);
				}
				else $id = 0;

				$grade = (int) $grade[0]->text();

				if( $grade > 0 ):
					$finished = true;
				else:
					$finished = false;
				endif;

				if( $finished == true )
				{
					$attempt = new FinishedAttempt($id, $grade, $name);
				}
				else {
					$attempt = new ProcessingAttempt(
						$id,
						$this->getSessionKey(),
						$this->getQuizId(),
						$this->getTimer()
					);
				}

				$attempt_list[$index] = $attempt;
			}
		}
		catch (InvalidSelectorException $e) {
			echo "loadAttemptList exception ".$e->getMessage();
		}

		return $attempt_list;
	}
}
<?php


namespace Parser;


use DiDom\Exceptions\InvalidSelectorException;
use Exception;

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

	public function getAttemptList()
	{
		$attempt_list = [];

		try {
			$attempt_table = $this->parse_page->find("table.generaltable.quizattemptsummary>tbody>tr");

			foreach ($attempt_table as $attempt_tr)
			{
				$index  = (int) $attempt_tr->find("td.c0")[0]->text();

				$status = $attempt_tr->find("td.c1")[0]->text();

				$attempt_list[$index]["name"] = $status;

				$grade  = $attempt_tr->find("td.c2");
				$review = $attempt_tr->find("td.c4>a");

				if( !empty($review) )
				{
					$attempt_review_link = $review[0]->attr("href");
					$attempt_list[$index]["link"] = $attempt_review_link;
					$attempt_list[$index]["id"] = $this->parseIdFromLink($attempt_review_link);
				}

				$grade = (int) $grade[0]->text();

				if( $grade > 0 ):
					$attempt_list[$index]["grade"] = $grade;
					$attempt_list[$index]["finished"] = true;
				else:
					$attempt_list[$index]["finished"] = false;
				endif;
			}
		}
		catch (InvalidSelectorException $e) {
			echo "loadAttemptList exception ".$e->getMessage();
		}

		return $attempt_list;
	}
}
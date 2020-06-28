<?php
/**
 * IT'S FULLY LEGACY UNUSED CODE
 * LIVING THERE A LONG TIME MIGHT BE DANGEROUS FOR YOU!
 */

namespace General;

use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use Exception;
use Resources\ProcessingAttempt;
use Resources\Quiz;

class ParserOLD
{
	public static $target = "http://nip.tsatu.edu.ua";

	private static $passport_id = 3;

	private static $auth = false;

	public static $err_msg = "";

	public $task = 0;

	public $total_parsed = 0;

	public $remaining = 0;

	/**
	 * @return bool|resource
	 */
	private static function CreateResultFile() //TODO Add different types of result files
	{
		$result_filename = FileSystem::results()."/parse_result_".date("D_d_m_Y-H_i_s").".csv";

		return fopen($result_filename, "w+");
	}

	/**
	 * Auth method on NIP
	 * @param null $user_id
	 * @return boolean
	 */
	public static function Auth($user_id = null)
	{
		if( $user_id === null) $user_id = self::$passport_id;

		$passport = new Passport($user_id);

		return Request::Login($passport);
	}

	public static function AllCourses($start = 2, $end = 10)
	{
		if(self::Auth())
		{
			$result_file_handle = self::CreateResultFile();

			$urlPattern = "/enrol/index.php?id=";
			$url_list = [];

			for($i = $start; $i <= $end; $i++)
				$urls[$i] = $urlPattern.$i;

			Request::Multi($url_list, "AllCourses", $result_file_handle);
			fclose($result_file_handle);

			return true;
		}

		return false;
	}

	public static function AllUsers($start = 1, $end = 1000)
	{

		if(self::Auth())
		{
			$result_file_handle = self::CreateResultFile();// TODO Add type of result file

			$urlPattern = "/message/index.php?id=";
			$url_list       = [];

			for($i = $start; $i <= $end; $i++)
				$urls[$i] = $urlPattern.$i;

			Request::Multi($url_list, "AllUsers", $result_file_handle);
			fclose($result_file_handle);

			return true;
		}

		return false;
	}

	/**
	 * @return array|bool
	 */
	public static function GetCoursesList()
	{
		$courses_list = [];

		try {
			$main_page = Request::Page(self::$target."/");

			$course_boxes = $main_page->find("div.coursebox");

			foreach($course_boxes as $key => $course_node) {

				$dis_name = $course_node->find("div.info>h3>a")[0]->text();
				$dis_href = $course_node->find("div.info>h3>a")[0]->attr("href");

				$courses_list[] = [
					"name" => $dis_name,
					"link" => $dis_href
				];
			}
		}
		catch (Exception $e) {
			echo "GetCoursesList Exception: ".$e->getMessage();
		}

		return $courses_list;
	}

	public static function SolveQuiz(Quiz $quiz)
	{
		// загрузить начальную страницу теста и остальные данные



		// найти лучшую попытку
		$best_attempt = $quiz->bestAttempt();


		// последовательно (рекурсивно?) отвечать на вопросы учитывая предыдущие ответы

				$counter = 1;

				$summary_page = self::ProcessAttemptRecursive($quiz_page, $counter, $best_question_list);
				// send answers
				//$finish = Request::ProcessAttempt(self::ParseSummaryForm($summary_page));
				//var_dump($finish);


	}

	/**
	 * @param Document $attempt_page
	 * @return array|bool
	 */
	private static function ParseAttemptResult(Document $attempt_page)
	{
		$question_list = []; // yxx scha buit myassso

		try {
			$question_boxes = $attempt_page->find("div.que");
			//print_r($question_boxes);

			foreach ($question_boxes as $box)
			{
				$checked_input_id = $box->find("input[checked=checked]")[0]->attr("id");

				$state = $box->find("div.info>div.state")[0]->text();
				$state = ($state == "Правильно") ? true : false;

				$question_text = $box->find("div.content>div.formulation.clearfix>div.qtext")[0]->text();
				$answer_text = $box->find("label[for=$checked_input_id]")[0]->text();

				$question_list[] = new Question(
					$checked_input_id,
					$question_text,
					substr($answer_text, 3),
					$state
				);
			}

		}
		catch (Exception $e) {
			echo "ParseAttemptPage exception ".$e->getMessage();
		}

		return (empty($question_list)) ? false : $question_list;
	}

	/**
	 * @param Document $quiz_page
	 * @param $counter
	 * @param array $best_question_list
	 * @return Document | bool
	 */
	private static function ProcessAttemptRecursive(Document $quiz_page, &$counter, $best_question_list = [])
	{
		echo $counter."\n"; // TODO Fix counter like that indicate real question number

		$fields = self::ParseQuizForm($quiz_page);

		if(!is_array($fields)) return $quiz_page;

		try {
			$questions = $quiz_page->find("div.que");

			foreach ($questions as $question)
			{
				$question_text = $question->find("div.qtext")[0]->text();

				if( !empty($best_question_list) )
				{
					$question_key = array_search($question_text, array_column($best_question_list, "question_text") );
					print_r($best_question_list[$question_key]);

					// TODO Search answers for different question types

					// when available true answer
					if( $best_question_list[$question_key]["state"] === true )
					{
						$best_answer = $best_question_list[$question_key]["answer_text"];

						$answers = $question->find("div.answer>div");

						// searching right answer on page

						foreach ($answers as $answer)
						{
							// parse answer text
							$answer_text = $answer->find("label")[0]->text();
							$answer_text = substr($answer_text, 3);

							// check answer
							if( $answer_text == $best_answer)
							{
								$answer_input = $answer->find("input");
								$answer_name = $answer_input[0]->attr("name");
								$answer_value = $answer_input[0]->attr("value");
								echo $answer_name."\n";

								$fields[$answer_name] = $answer_value;
							}
						}
					}
					else { // when true answer is absent use RAND
						$answers = $question->find("div.answer>div");

						$answers_quantity = count($answers);

						$answer_input = $answers[0]->find("input");
						$answer_name = $answer_input[0]->attr("name");

						$fields[$answer_name] = rand(0, $answers_quantity - 1 );
					}
				}
			}
		}
		catch (Exception $e) {
			echo "ProcessAttemptRecursive exception ($counter) ".$e->getMessage();
		}

		$counter++;

		$process = self::ProcessAttemptRecursive(Request::ProcessAttempt($fields), $counter, $best_question_list);

		if($process !== false) return $process;
		else return false;
	}

	/**
	 * parse form inputs for submit answer
	 * @param Document $quiz_page
	 * @return array | bool
	 */
	private static function ParseQuizForm(Document $quiz_page)
	{
		$inputs = [];

		try {
			$form_ob = $quiz_page->find("form#responseform");

			if(empty($form_ob)) return false;

			$inputs = $form_ob[0]->find("input");
		}
		catch (Exception $e) {
			echo "ParseQuizForm exception ".$e->getMessage();
		}

		$fields = [];

		foreach ($inputs as $input)
		{
			$input_name = $input->attr("name");
			$input_value = $input->attr("value");
			if($input_name != "" && !array_key_exists($input_name, $fields) )
				$fields[$input_name] = $input_value;
		}

		return $fields;
	}

	/**
	 * @param Document $summary_page
	 * @return array|bool
	 */
	private static function ParseSummaryForm(Document $summary_page)
	{
		$inputs = [];

		try {
			$form_ob = $summary_page->find("form");

			if(empty($form_ob)) return false;

			$inputs = $form_ob[0]->find("input");
		}
		catch (Exception $e) {
			echo "ParseQuizForm exception ".$e->getMessage();
		}

		$fields = [];

		foreach ($inputs as $input)
		{
			$input_name = $input->attr("name");
			$input_value = $input->attr("value");
			if($input_name != "" && !array_key_exists($input_name, $fields) )
				$fields[$input_name] = $input_value;
		}

		return $fields;
	}
}
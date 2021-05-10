<?php

require "vendor/autoload.php";

use MoodleParser\General\Student;
use MoodleParser\Parser\Exceptions\NewAttemptBan;
use MoodleParser\Processors\AttemptProcessor;
use MoodleParser\Processors\RandomQuestionProcessor;
use MoodleParser\Processors\TipsQuestionProcessor;
use MoodleParser\Resources\Exceptions\WrongResourceID;

$bogdan = new Student("vdovinbogdan0@gmail.com", "Darmshtadtiy110");
//$kolya_bliznec = new Student("kol9blizne4@gmail.com", "24seeqeeN");

function pass_test(AttemptProcessor $processor, $quiz_id )
{
	$quiz = $processor->openQuiz($quiz_id);

	// if has no attempt - start new than run with tips
	// if has attempt - start new with tips

	if(empty($quiz->getFinishedAttempts()))
	{
		try {
			$processor->newAttempt($quiz);
		} catch (NewAttemptBan $e) {
			echo $e->getMessage() . "/n";
			die;
		}

		$processor->questionProcessor(new RandomQuestionProcessor());

		$processor->processAllQuestions();

		$quiz = $processor->openQuiz($quiz_id);
	}
	$best_attempt_id = $quiz->getBestAttemptID();

	$tip_attempt = $processor->openAttempt($best_attempt_id);

	try {
		$processor->newAttempt($quiz);
	}
	catch (NewAttemptBan $e) {
		echo $e->getMessage(); die;
	}

	$processor->questionProcessor(new TipsQuestionProcessor($tip_attempt));
	$processor->processAllQuestions();

	echo "Quiz #" .$quiz_id. " ".$quiz->name()." was solved"."\n";
}

function pass_course(Student $student, $course_id)
{
	$attempt_processor = new AttemptProcessor($student);

	try {
		$course = $attempt_processor->openCourse($course_id);
	}
	catch (WrongResourceID $e) {
		echo $e->getMessage()."\n";
	}
	if (isset($course))
	{
		$quiz_list = $course->getQuizList();

		/**$available = [];
		$unavailable = [];

		foreach ($quiz_list as $id => $quiz_arr)
		{
		if($quiz_arr["available"] === true)
		$available[$id] = $quiz_arr;
		elseif($quiz_arr["available"] === false)
		$unavailable[$id] = $quiz_arr;
		}

		$solving_list[] = array_merge(array_slice($available, -1, 1), $unavailable);

		var_dump($solving_list);*/

		foreach ($quiz_list as $id => $quiz_arr)
		{
			$quiz = $attempt_processor->openQuiz($id);
			//var_dump($id);

			if($quiz->getBestGrade() > 5)
				continue;
			else {
				echo "Start #".$id." ".$quiz->name();
				pass_test($attempt_processor, $id);
				$attempt_processor->request()->toggleCompletion($quiz->id(), $quiz->name());
			}

		}

	}
}

pass_course($bogdan, 1350);

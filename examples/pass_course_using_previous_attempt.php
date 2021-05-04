<?php

require "vendor/autoload.php";

use MoodleParser\General\Student;
use MoodleParser\Parser\Exceptions\NewAttemptBan;
use MoodleParser\Processors\AttemptProcessor;
use MoodleParser\Processors\RandomQuestionProcessor;
use MoodleParser\Processors\TipsQuestionProcessor;

$bogdan = new Student("vdovinbogdan0@gmail.com", "Darmshtadtiy110");
//$kolya_bliznec = new Student("kol9blizne4@gmail.com", "24seeqeeN");
$student = $bogdan;



function pass_test(Student $student, array $tests )
{
	foreach ($tests as $quiz_id)
	{
		$quiz = $student->openQuiz($quiz_id);

		// if has no attempt - start new than run with tips
		// if has attempt - start new with tips

		if(empty($quiz->getFinishedAttempts()))
		{
			try {
				$new_attempt = $student->newAttempt($quiz);
			}
			catch (NewAttemptBan $e)
			{
				echo $e->getMessage()."/n";die;
			}

			if(isset($new_attempt))
			{
				$attempt_processor = new AttemptProcessor($student, $new_attempt, new RandomQuestionProcessor());

				$attempt_processor ->processAllQuestions();

				$quiz = $student->openQuiz($quiz_id);
			}
		}

		$best_attempt_id = $quiz->getBestAttemptID();

		$tip_attempt = $student->openAttempt($best_attempt_id);

		try {
			$attempt_with_tips = $student->newAttempt($quiz);
		}
		catch (NewAttemptBan $e) {
			echo $e->getMessage(); die;
		}

		if(isset($attempt_with_tips))
		{
			$attempt_processor = new AttemptProcessor(
				$student,
				$attempt_with_tips,
				new TipsQuestionProcessor($tip_attempt)
			);

			$attempt_processor->processAllQuestions();

			$student->request()->toggleCompletion($quiz->getId(), $quiz->getName());
		}

	}
}

function pass_course(Student $student, $course_id)
{
	$course = $student->openCourse($course_id);

	$quiz_list = $course->getQuizList();

	pass_test($student, array_keys($quiz_list));
}

pass_test($student,
	[
		94432,
		94423,
		94434,
		94512,
		94513,
		94514,
		94436,
		94437,
		94438,
		94439,
		94515,
		94441,
		94442,
		94443,
		94517,
		94518,
		94518,
		94519,
		94520,
		94521,
		94522,
		94523,
		94525,
		94527,
		94529,
		94530
	]);
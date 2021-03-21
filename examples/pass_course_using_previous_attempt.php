<?php

require "vendor/autoload.php";

use MoodleParser\AttemptProcessor\RandomProcessor;
use MoodleParser\AttemptProcessor\TipsProcessor;
use MoodleParser\General\Exceptions\AlreadyLogin;
use MoodleParser\General\Exceptions\LoginError;
use MoodleParser\General\Student;

$login = "vdovinbogdan0@gmail.com";
$pass = "Darmshtadtiy110";

$course_id = 266;
$quiz_id = 2591;

$student = new Student($login, $pass);

try {
	$student->auth();
	$student->loadStudentInfo();
}
catch (LoginError $e) { echo $e->getMessage(); }
catch (AlreadyLogin $e)
{
	echo $e->getMessage()."\n";

	$homepage = $student->request()->homepage();

	$student->parser()->setParsePage($homepage->response());

	$student->loadStudentInfo();
}

function pass_test(Student $student, array $tests )
{
	foreach ($tests as $quiz_id)
	{
		$test_lr1 = $student->openQuiz($quiz_id);

		$attempt_list = $test_lr1->getFinishedAttemptList();

		// if has no attempt - start new than run with tips
		// if has attempt - start new with tips

		if(empty($attempt_list))
		{
			$new_attempt = $student->newAttempt($test_lr1);

			$new_attempt->setProcessor(new RandomProcessor());

			$new_attempt->processAllQuestions();

			$test_lr1 = $student->openQuiz($quiz_id);

		}

		$best_attempt_id = $test_lr1->getBestAttemptID();
		$tip_attempt = $student->openAttempt($best_attempt_id);

		$attempt_with_tips = $student->newAttempt($test_lr1);

		$attempt_with_tips->setProcessor(new TipsProcessor($tip_attempt));
		$attempt_with_tips->processAllQuestions();
	}
}

function pass_course(Student $student, $course_id)
{
	$course = $student->openCourse($course_id);

	$quiz_list = $course->getQuizList();

	foreach ($quiz_list as $quiz)
	{
		$student->request()->toggleCompletion($quiz["id"], $quiz["name"]);
	}

	pass_test($student, array_keys($quiz_list));
}

pass_course($student, 1368);

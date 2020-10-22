<?php

require "vendor/autoload.php";

use MoodleParser\General\Exceptions\AlreadyLogin;
use MoodleParser\General\Exceptions\LoginError;
use MoodleParser\General\Student;

$student = new Student("vdovinbogdan0@gmail.com", "Darmshtadtiy110");

try {
	$student->auth();
	$student->loadStudentInfo();
}
catch (LoginError $e) { echo $e->getMessage(); }
catch (AlreadyLogin $e) {
	echo $e->getMessage()."\n";

	$homepage = $student->request()->homepage();

	$student->parser()->setParsePage($homepage->response());

	$student->loadStudentInfo();

}

print_r($student->getCourseList());

$tvp = $student->getCourse(846);

echo $tvp->getName();

print_r($tvp->getQuizList());

$test_lr1 = $student->getQuiz(41292);

$student->newAttempt($test_lr1);

<?php

require "vendor/autoload.php";
require "Classes/autoload.php";

use Factory\PassportFactory;
use General\Signal;
use Resources\Quiz;
use Resources\Course;

$bogdan_passport = PassportFactory::create(3);

$student_bogdan = $bogdan_passport->auth();

if($student_bogdan->name() !== "")
{
	Signal::login_successful($student_bogdan->name());

	$bogdan_courses = $student_bogdan->getCourseList();

	$eltex = 1123;
	/** @var Course $eltex_course */
	$eltex_course = $student_bogdan->getCourse($eltex);

	$student_bogdan->loadResource($eltex_course);

	//var_dump($eltex_course->getQuizList());

	$quiz_id = 144845;

	/** @var Quiz $theme_10_quiz */
	$theme_10_quiz = $eltex_course->getQuiz($quiz_id);

	$student_bogdan->loadResource($theme_10_quiz);

	var_dump($theme_10_quiz);

	//var_dump($theme_10_quiz);
	/*
	$newAttempt = $theme_10_quiz->newAttempt();
	$newAttempt->process();
	*/

}



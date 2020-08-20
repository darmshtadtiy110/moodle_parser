<?php

require "vendor/autoload.php";
require "src/autoload.php";

use Parser\Resources\QuizParser;
use Resources\Student;

use General\Signal;
use AttemptProcessor\RandomProcessor;
use AttemptProcessor\TipsProcessor;
use Parser\Resources\FinishedAttemptParser;

$course_id = 1123; 
$quiz_id = 144128; 
$attempt_id = 1631675;

$user = Student::getInstance(3);

$theme_10_quiz = QuizParser::GetById($quiz_id);

$finished_attempt = FinishedAttemptParser::GetById($attempt_id);

$processing = $theme_10_quiz->startProcessingAttempt();

try {
	$processing->setProcessor(new TipsProcessor($finished_attempt));
}
catch (Exception $e) { Signal::msg($e->getMessage()); }

$processing->processAllQuestions();

var_dump($processing);


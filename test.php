<?php
ini_set('allow_url_include', 1);

define("MOODLE_INTERNAL", true);
$version = "";
$release = "";
$branch = "";
$maturity = "";

require_once "http://nip.tsatu.edu.ua/version.php";

echo $version."\n";
echo $release."\n";
echo $branch."\n";
echo $maturity."\n";

<?php


namespace Resources;


use General\Passport;
use FileSystem\Cookies;


class Student
{
	/** @var Passport  */
	private $passport;

	/** @var string */
	private $name;

	/** @var array */
	private $course_list;

	/** @var Cookies */
	private $cookies;

	/**
	 * Student constructor.
	 * @param Passport $passport
	 * @param $name
	 * @param $course_list
	 * @param $cookies
	 */
	public function __construct(Passport $passport, $name, $course_list, $cookies)
	{
		$this->passport = $passport;
		$this->name = $name;
		$this->course_list = $course_list;
		$this->cookies = $cookies;
	}

	/**
	 * @return mixed
	 */
	public function passport()
	{
		return $this->passport;
	}

	/**
	 * @return mixed
	 */
	public function name()
	{
		return $this->name;
	}

	/**
	 * @return Cookies
	 */
	public function getCookies()
	{
		return $this->cookies;
	}

	/**
	 * @return mixed
	 */
	public function getCourseList()
	{
		return $this->course_list;
	}

	public function getCourse($id)
	{
		return $this->course_list[$id];
	}

	public function loadResource(Resource $resource)
	{
		$resource->loadFromParser($this->getCookies());
	}



}
<?php


namespace Request;


use Exception;

class CurlErrorException extends Exception
{

	/**
	 * CurlErrorException constructor.
	 * @param string $curl_error
	 */
	public function __construct($curl_error)
	{
		parent::__construct($curl_error);
	}
}
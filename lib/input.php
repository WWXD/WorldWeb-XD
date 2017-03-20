<?php

class Input
{
	private $_post;
	private $_get;
	private $_session;
	private $_server;

	public function __construct()
	{
		$this->_post = $_POST;
		$this->_get = $_GET;
		#$this->_session = $_SESSION;
		$this->_server = $_SERVER;
	}

	public function post($key = null, $default = null)
	{
		return $this->checkGlobal($this->_post, $key, $default);
	}

	public function get($key = null, $default = null)
	{
		return $this->checkGlobal($this->_get, $key, $default);
	}

	// other accessors

	private function checkGlobal($global, $key = null, $default = null)
	{
		if ($key) {
			if (isset($global[$key])) {
				return $global[$key];
			} else {
				return $default ?: null;
			}
		}
		return $global;
	}
}
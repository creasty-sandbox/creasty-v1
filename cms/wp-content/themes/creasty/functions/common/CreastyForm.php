<?php

if (!defined('ABSPATH'))
	exit;

mb_language('ja');
session_start();


class CreastyForm {
	public $settigns = array();
	public $error    = array();
	public $form     = array();
	public $succeed  = true;
	public $called   = false;

	public function __construct($settings) {
		$this->settings = $settings;

		if (!isset($_POST['_ajax_call']))
			return;

		if (!wp_verify_nonce($_POST[$this->get_name($settings['nonce'])], 'contactform'))
			return;

		$this->called = true;
	}

	public function get_name($name) {
		return $this->settings['prefix'] . '-' . $name;
	}

	public function check($name, $required = false, $validator = array()) {
		if (!$this->called || empty($name))
			return ($this->succeed = false);

		$value = $this->sanitize($_POST[$this->get_name($name)]);
		$len = mb_strlen($value);

		if (empty($value)) {
			if ($required) {
				$this->error[$name] = 'required';
				return ($this->succeed = false);
			} else {
				return;
			}
		}

		foreach ($validator as $v => $option) {
			if ('regexp' == $v && !preg_match($option, $value)) {
				$this->error[$name] = 'invalid';
				return ($this->succeed = false);
			}

			if ('min' == $v && $len < $option) {
				$this->error[$name] = array('short', $option);
				return ($this->succeed = false);
			}

			if ('max' == $v && $len > $option) {
				$this->error[$name] = array('long', $option);
				return ($this->succeed = false);
			}
		}

		$this->form[$name] = $value;
	}

	private function sanitize($val) {
		if (!$val)
			return '';

		if (is_string($val)) {
			$val = str_replace("\0", '', $val);
			$val = trim($val);
		}

		return $val;
	}

	public function check_captcha($name) {
		if (!$this->called || empty($name))
			return ($this->succeed = false);

		$value = strtolower($this->sanitize($_POST[$this->get_name($name)]));

		if (empty($value)) {
			$this->error[$name] = 'requried';
			return($this->succeed = false);
		}

		$captcha = &$_SESSION['captcha'];

		if (!isset($captcha) || $value != $captcha) {
			$this->error[$name] = 'invalid';
			return ($this->succeed = false);
		}
	}

	public function output($data) {
		if (!$this->called)
			return;

		header('Content-type: application/json', true);

		echo json_encode($data);

		exit;
	}

	public function send() {}
}


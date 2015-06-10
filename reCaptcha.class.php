<?php

function icv_recaptcha(&$known_verifications)
{
	// Because we are reusing all the settings, better unset it first
	$key = array_search('captcha', $known_verifications);
	unset($known_verifications[$key]);

	$known_verifications[] = 'ReCaptcha';
	loadLanguage('reCaptcha');
}

class Verification_Controls_ReCaptcha implements Verification_Controls
{
	private $_options = null;
	private $_site_key = null;
	private $_secret_key = null;
	private $_recaptcha = null;

	public function __construct($verificationOptions = null)
	{
		global $modSettings;

		require_once(EXTDIR . '/recaptchalib.php');

		$this->_site_key = $modSettings['recaptcha_site_key'];
		$this->_secret_key = $modSettings['recaptcha_secret_key'];

		if (!empty($verificationOptions))
			$this->_options = $verificationOptions;
	}

	public function showVerification($isNew, $force_refresh = true)
	{
		loadTemplate('reCaptcha');
		loadTemplate('VerificationControls');
		loadJavascriptFile('https://www.google.com/recaptcha/api.js'/*, array('defer' => true, 'async' => 'true', 'local' => 'false')*/);

		return true;
	}

	public function createTest($refresh = true) {}

	public function prepareContext()
	{
		return array(
			'template' => 'recaptcha',
			'values' => array(
				'site_key' => $this->_site_key,
			)
		);
	}

	public function doTest()
	{
		$this->_recaptcha = new ReCaptcha($this->_secret_key);

		if ($_POST["g-recaptcha-response"]) {
			$resp = $this->_recaptcha->verifyResponse (
				$_SERVER["REMOTE_ADDR"],
				$_POST["g-recaptcha-response"]
			);
			if (!$resp->success)
				return 'wrong_verification_code';
		}
		else
			return 'wrong_verification_code';

		return true;
	}

	public function hasVisibleTemplate()
	{
		return true;
	}

	public function settings()
	{
		// Visual verification.
		$config_vars = array(
			array('title', 'recaptcha_verification'),
			array('text', 'recaptcha_site_key'),
			array('text', 'recaptcha_secret_key'),
		);

		return $config_vars;
	}
}

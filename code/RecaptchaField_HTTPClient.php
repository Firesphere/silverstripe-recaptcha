<?php

namespace Chillu\Recaptcha\Control;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Object;

/**
 * Simple HTTP client, mainly to make it mockable.
 */
class RecaptchaField_HTTPClient extends Object
{

	/**
	 * @param String $url
	 * @param $postVars
	 * @return HTTPResponse
	 */
	public function post($url, $postVars)
	{
		$ch = curl_init($url);
		if (!empty(Config::inst()->get('RecaptchaField', 'proxy_server'))) {
			curl_setopt($ch, CURLOPT_PROXY, Config::inst()->get('RecaptchaField', 'proxy_server'));
			if (!empty(Config::inst()->get('RecaptchaField', 'proxy_auth'))) {
				curl_setopt($ch, CURLOPT_PROXYUSERPWD, Config::inst()->get('RecaptchaField', 'proxy_auth'));
			}
		}

		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'reCAPTCHA/PHP');
		// we need application/x-www-form-urlencoded
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postVars));
		$response = curl_exec($ch);

		if (class_exists('SS_HTTPResponse')) {
			$responseObj = new HTTPResponse();
		} else {
			// 2.3 backwards compat
			$responseObj = new HttpResponse();
		}
		$responseObj->setBody($response); // 2.2. compat
		$responseObj->addHeader('Content-Type', 'application/json');
		return $responseObj;
	}
}

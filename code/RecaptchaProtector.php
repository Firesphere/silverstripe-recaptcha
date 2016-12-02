<?php
namespace Chillu\ReCaptcha;
/**
 * @package recaptcha
 */
use SilverStripe\Control\Director;
use SilverStripe\Spamprotection\SpamProtector;

/**
 * Protecter class to handle spam protection interface
 */
class RecaptchaProtector implements SpamProtector
{

    /**
     * Return the Field that we will use in this protector
     *
     * @return string
     */
    public function getFormField($name = 'RecaptchaField', $title = "Captcha", $value = null)
    {
        $field = RecaptchaField::create($name, $title, $value);
        $field->useSSL = Director::is_https();

        return $field;
    }

    /**
     * Not used by Recaptcha
     */
    public function setFieldMapping($fieldMapping)
    {
    }

}

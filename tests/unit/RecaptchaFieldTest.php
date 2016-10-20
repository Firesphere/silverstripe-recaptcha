<?php
use Chillu\Recaptcha\Forms\RecaptchaField;
use Chillu\Recaptcha\Control\RecaptchaField_HTTPClient;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Convert;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Control\HTTPResponse;
/**
 * @package recaptcha
 */

class RecaptchaFieldTest extends SapphireTest
{

    public function testValidate()
    {
        $form = Form::create(Controller::create(), 'Form', new FieldList(), new FieldList());
        $f = new RecaptchaField('MyField');
        $f->setHTTPClient(new RecaptchaFieldTest_HTTPClient());
        $f->setForm($form);
        $v = new RequiredFields();

        $_REQUEST['g-recaptcha-response'] = 'valid';
        $this->assertTrue($f->validate($v));

        $_REQUEST['g-recaptcha-response'] = 'invalid';
        $this->assertFalse($f->validate($v));

        unset($_REQUEST['g-recaptcha-response']);
    }
}

class RecaptchaFieldTest_HTTPClient extends RecaptchaField_HTTPClient implements TestOnly
{
    public function post($url, $postVars)
    {
        if ($postVars['response'] == 'valid') {
            $response = new HTTPResponse();
            $data = array(
                'success'      => true,
                'challenge_ts' => date('c'),  // timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
                'hostname'     => $_SERVER['HTTP_HOST'],         // the hostname of the site where the reCAPTCHA was solved
            );
            $response->addHeader('Content-Type', 'application/json');
            $response->setBody(Convert::array2json($data));
            return $response;
        }

        if ($postVars['response'] == 'invalid') {
            $response = new HTTPResponse();
            $data = array(
                'success'      => false,
                'challenge_ts' => date('c'),
                'hostname'     => $_SERVER['HTTP_HOST'],
                'error-codes'  => array(
                    'invalid-input-response'
                )
            );
            $response->addHeader('Content-Type', 'application/json');
            $response->setBody(Convert::array2json($data));
            return $response;
        }

        return new HTTPResponse();
    }
}

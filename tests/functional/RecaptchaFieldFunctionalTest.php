<?php
/**
 * @package recaptcha
 */
use Chillu\ReCaptcha\RecaptchaField;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;

/**
 * @author Ingo Schommer, SilverStripe Ltd.
 */
class RecaptchaFieldFunctionalTest extends FunctionalTest
{

    protected $orig = array();

    public function setUp()
    {
        parent::setUp();

        $this->orig['public_api_key'] = Config::inst()->get('RecaptchaField', 'public_api_key');
        $this->orig['private_api_key'] = Config::inst()->get('RecaptchaField', 'private_api_key');
        Config::inst()->update('RecaptchaField', 'public_api_key', 'test');
        Config::inst()->update('RecaptchaField', 'private_api_key', 'test');
        Config::inst()->update('RecaptchaField', 'noscript_enabled', true);
    }

    public function tearDown()
    {
        Config::inst()->update('RecaptchaField', 'public_api_key', $this->orig['public_api_key']);
        Config::inst()->update('RecaptchaField', 'private_api_key', $this->orig['private_api_key']);

        parent::tearDown();
    }

    public function testValidSubmission()
    {
        $this->get('RecaptchaFieldFunctionalTest_Controller');
        $data = array(
            'g-recaptcha-response' => 'valid',
        );
        $response = $this->submitForm('Form_Form', null, $data);
        $this->assertEquals('submitted', $response->getBody());
    }

    public function testInvalidSubmission()
    {
        $this->get('RecaptchaFieldFunctionalTest_Controller');
        $data = array(
            'g-recaptcha-response' => 'invalid',
        );
        $response = $this->submitForm('Form_Form', null, $data);
        $body = $response->getBody();
        $this->assertContains('Your answer didn&#039;t match the captcha', $body);
    }

}

class RecaptchaFieldFunctionalTest_Controller extends Controller implements TestOnly
{

    private static $allowed_actions = array(
        'Form'
    );

    protected $template = 'BlankPage';

    public function Link($action = null)
    {
        return Controller::join_links('RecaptchaFieldFunctionalTest_Controller', $action);
    }

    public function Form()
    {
        $form = Form::create(
            $this,
            'Form',
            FieldList::create(
                $f = RecaptchaField::create('MyRecaptchaField')
            ),
            FieldList::create(
                FormAction::create('doSubmit', 'submit')
            )
        );
        $f->setHTTPClient(new RecaptchaFieldTest_HTTPClient());

        return $form;
    }

    public function doSubmit($data, $form)
    {
        return 'submitted';
    }
}
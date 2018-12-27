<?php

require __DIR__ . '/twilio-php-master/Twilio/autoload.php';
use Twilio\Rest\Client;

class Mycode_ThankYourOrder_IndexController extends Mage_Core_Controller_Front_Action
{
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';

    public function EmailAction()
    {
        $baseurl = Mage::getBaseUrl();
        $post = $this->getRequest()->getPost();
        if ( $post ) {
           $translate = Mage::getSingleton('core/translate');
           $translate->setTranslateInline(false);
           try {
                $postObject = new Varien_Object();
                $postObject->setData($post);
                $error = false;
                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }
                if ($error) {
                    throw new Exception();
                }
                $mailTemplate = Mage::getModel('core/email_template');
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                ->setReplyTo($post['email'])
                ->sendTransactional(
                    'thankyourorder_email_template',
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER),
                    $post['email'],
                    null,
                    array('data' => $postObject)
                );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }
                $translate->setTranslateInline(true);
                $this->_redirectUrl($baseurl);
                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $this->_redirect('*/*/');
        }
    }
    public function SMSAction()
    {
        $baseurl = Mage::getBaseUrl();
        $msg = nl2br("Here's the link you requested to download our apps!  iOS app: http://bit.ly/COTDApp  Android app:  http://bit.ly/COTDGooglePlay");
        if (isset($_POST['mobile-number'])) {
        $sid = 'AC5cbc6c89d97d4d92335e9cdf27940e43';
        $token = '50093b162063f4816eefb6c47c8d9cf7';
        $client = new Client($sid, $token);

        $client->messages->create(
            $_POST['mobile-number'],
            array(
           'from' => '+13365609457',
           'body' => $msg
        )
        );
        $this->_redirectUrl($baseurl);
        }
    }
}  
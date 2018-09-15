<?php

namespace App\Controller\Admin;

use App\Controller\AppController;;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

use FFMpeg;

class TestingController extends AppController
{
    public function beforeFilter(Event $event)
    {
     //   parent::beforeFilter($event);
        Configure::write('debug', 2);
    }
 
    public function testInfo() {
	phpinfo(); exit;
    }

    public function testAuthNet()
    {
        define("AUTHORIZENET_LOG_FILE", LOGS . "authNet.txt");

        // Common setup for API credentials
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(Configure::read('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(Configure::read('MERCHANT_TRANSACTION_KEY'));
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber("4111111111111111");
        $creditCard->setExpirationDate("2019-04");
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create a transaction
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount(1.00);
        $transactionRequestType->setPayment($paymentOne);
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId( $refId);
        $request->setTransactionRequest($transactionRequestType);
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        if ($response != null)  {
            $tresponse = $response->getTransactionResponse();
            if (($tresponse != null) && ($tresponse->getResponseCode()=="1")) {
                echo "Charge Credit Card AUTH CODE : " . $tresponse->getAuthCode() . "\n";
                echo "Charge Credit Card TRANS ID  : " . $tresponse->getTransId() . "\n";
            } else {
                echo $tresponse->getResponseCode();
                echo "Charge Credit Card ERROR :  Invalid response\n";
            }
        } else {
            echo  "Charge Credit Card Null response returned";
        }

        exit;
    }

    public function testEmail()
    {
        $email = new Email();

        // Use a named transport already configured using Email::configTransport()
        $email
            ->transport('gmail')
            ->to(Configure::read('ADMIN_EMAIL'))
            ->subject(__('New Account Created'))
            ->emailFormat('html')
            ->template('send_test_email')
        ;

        $result = $email->send();
        echo '<pre>'; var_dump($result);

        exit;
    }

    public function testFFMPEG()
    {
        $this->loadModel('Reviews');
        var_dump($this->Reviews->ReviewAssets->hasThumbnail(1005));
        die;
        $ffmpeg = FFMpeg\FFMpeg::create();
        $video = $ffmpeg->open('uploads/small.MOV');
        $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(1))->save('uploads/quakr.jpg');
        $video->save(new FFMpeg\Format\Video\WebM(), 'uploads/export.webm');
        die;
    }
}

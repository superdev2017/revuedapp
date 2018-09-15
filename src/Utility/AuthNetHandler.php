<?php

namespace App\Utility;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class AuthNetHandler
{
    protected $merchantAuthentication;

    public function __construct()
    {
        define("AUTHORIZENET_LOG_FILE", LOGS . "authNet.log");
        $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $this->merchantAuthentication->setName(Configure::read('MERCHANT_LOGIN_ID'));
        $this->merchantAuthentication->setTransactionKey(Configure::read('MERCHANT_TRANSACTION_KEY'));
    }

    public function createSubscription($data)
    {
        if (isset($data['trial_days']) and $data['trial_days'] > 0) {
            $startDate = new \DateTime("+$data[trial_days] days");
        } else {
            $startDate = new \DateTime();
        }

        if ( ! isset($data['amount']) or ! $data['amount']) {
            $data['amount'] = '100.00';
        }

        // Set the transaction's refId
        $refId = 'ref' . time();
        // Subscription Type Info
        $subscription = new AnetAPI\ARBSubscriptionType();
        $subscription->setName("RevuedApp Subscription");
        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength(1);
        $interval->setUnit("months");
        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate($startDate);
        $paymentSchedule->setTotalOccurrences("9999");
        $paymentSchedule->setTrialOccurrences("0");
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setAmount($data['amount']);
        $subscription->setTrialAmount("0.00");

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($data['card_number']);
        $creditCard->setExpirationDate(date('Y-m', strtotime($data['expiration_date'])));
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        $subscription->setPayment($payment);
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber("1234354");
        $order->setDescription("Monthly subscription for RevuedApp component and user dashboard");
        $subscription->setOrder($order);

        $billTo = new AnetAPI\NameAndAddressType();
        $billTo->setFirstName($data['first_name']);
        $billTo->setLastName($data['last_name']);
        $subscription->setBillTo($billTo);
        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($this->merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new AnetController\ARBCreateSubscriptionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response == null) {
            throw new \Exception('No response from AuthNet', 500);
        }

        if (($response != null) and ($response->getMessages()->getResultCode() == "Ok") ) {
            return $response->getSubscriptionId();
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            throw new AuthNetException($errorMessages[0]->getText());
        }
    }

    public function getSubscription($subscriptionId)
    {
        // Set the transaction's refId
        $refId = 'ref' . time();

        // Creating the API Request with required parameters
        $request = new AnetAPI\ARBGetSubscriptionRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscriptionId($subscriptionId);

        // Controller
        $controller = new AnetController\ARBGetSubscriptionController($request);

        // Getting the response
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

        if ($response == null) {
            throw new \Exception('No response from AuthNet', 500);
        }

        if ($response->getMessages()->getResultCode() == "Ok") {
            return [
                'name' => $response->getSubscription()->getName(),
                'amount' => $response->getSubscription()->getAmount(),
                'status' => $response->getSubscription()->getStatus(),
                'description' => $response->getSubscription()->getProfile()->getDescription(),
                'profile_id' => $response->getSubscription()->getProfile()->getCustomerProfileId(),
                'payment_profile_id' => $response->getSubscription()->getProfile()->getPaymentProfile()->getCustomerPaymentProfileId()
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();
            throw new AuthNetException($errorMessages[0]->getText());
        }

        return $response;
    }
}

class AuthNetException extends \Exception
{}

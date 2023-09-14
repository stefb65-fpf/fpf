<?php


namespace App\Concern;
use Payline\PaylineSDK;

trait Api
{
    protected function callBridge($url, $method, $datas) {
        $headers = array(
            "Content-Type: application/json",
            "Bridge-Version: 2021-06-01",
            "Client-Id: " . env('BRIDGE_CLIENT_ID'),
            "Client-Secret: " . env('BRIDGE_CLIENT_SECRET'),
        );
        $curl = curl_init();
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
        );
        if ($method == 'POST') {
            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_POSTFIELDS] = $datas;
        }
        curl_setopt_array($curl, $curl_options);
        $response = curl_exec($curl);
        $status     = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return [$status, $response];
    }

    protected function callMonext($amount_cents, $urls, $ref, $user) {
        $payline = new PaylineSDK(env('PAYLINE_MERCHANT_ID'), env('PAYLINE_ACCESS_KEY'), null, null, null, null, env('PAYLINE_PRODUCTION'));

        $doWebPaymentRequest = array();

        $doWebPaymentRequest['cancelURL'] = $urls['cancelURL'];
        $doWebPaymentRequest['returnURL'] = $urls['returnURL'];
        $doWebPaymentRequest['notificationURL'] = $urls['notificationURL'];

        // PAYMENT
        $doWebPaymentRequest['payment']['amount'] = $amount_cents; // this value has to be an integer amount is sent in cents
        $doWebPaymentRequest['payment']['currency'] = 978; // ISO 4217 code for euro
        $doWebPaymentRequest['payment']['action'] = 101; // 101 stand for "authorization+capture"
        $doWebPaymentRequest['payment']['mode'] = 'CPT'; // one shot payment

        $doWebPaymentRequest['buyer']['email'] = $user['email'];
        $doWebPaymentRequest['buyer']['firstName'] = $user['prenom'];
        $doWebPaymentRequest['buyer']['lastName'] = $user['nom'];

        // ORDER
        $doWebPaymentRequest['order']['ref'] = $ref; // the reference of your order
        $doWebPaymentRequest['order']['amount'] = $amount_cents; // may differ from payment.amount if currency is different
        $doWebPaymentRequest['order']['currency'] = 978; // ISO 4217 code for euro
        $doWebPaymentRequest['order']['date'] = date('d/m/Y H:i'); // date of the order

        // CONTRACT NUMBERS
        $doWebPaymentRequest['payment']['contractNumber'] = '8729848';

        $doWebPaymentResponse = $payline->doWebPayment($doWebPaymentRequest);
        if ($doWebPaymentResponse['result']['code'] != '00000') {
            return array('code' => $doWebPaymentResponse['result']['code'], 'message' => $doWebPaymentResponse['result']['longMessage']);
        } else {
            return array('code' => $doWebPaymentResponse['result']['code'], 'redirectURL' => $doWebPaymentResponse['redirectURL'],
                'token' => $doWebPaymentResponse['token']);
        }
    }

    protected function getMonextResult($token) {
        $payline = new PaylineSDK(env('PAYLINE_MERCHANT_ID'), env('PAYLINE_ACCESS_KEY'), null, null, null, null, env('PAYLINE_PRODUCTION'));
        $getWebPaymentDetailsResponse = $payline->getWebPaymentDetails(['token' => $token]);
        return array('code' => $getWebPaymentDetailsResponse['result']['code'], 'message' => $getWebPaymentDetailsResponse['result']['shortMessage']);
    }

    protected function callOctopush($to, $message, $sender) {
        $url = "https://api.octopush.com/v1/public/sms-campaign/send";
        $headers = array(
            "Content-Type: application/json",
            "cache-control: no-cache",
            "api-login: " . env('OCTOPUSH_IDENTIFIANT'),
            "api-key: " . env('OCTOPUSH_KEY'),
        );

        $params = array(
            "recipients" => [
                [
                    "phone_number" => $to
                ]
            ],
            'text' => $message,
            'type' => 'sms_premium',
            'sender' => $sender,
            'purpose' => 'alert'
//            'simulation_mode' => 'true'
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL                 => $url,
            CURLOPT_CUSTOMREQUEST       => 'POST',
            CURLOPT_HTTPHEADER          => $headers,
            CURLOPT_POST                => 1,
            CURLOPT_POSTFIELDS          => json_encode($params),
            CURLOPT_RETURNTRANSFER      => 1,
        ));
        $reponse = curl_exec($curl);
        $status     = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status == 200 || $status == 201) {
            $return_code = '0';
        } else {
            $return_code = '20';
        }
        return $return_code;
    }
}

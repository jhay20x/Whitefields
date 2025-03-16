<?php
// use Infobip\Configuration;
// use Infobip\ApiException;
// use Infobip\Model\SmsRequest;
// use Infobip\Model\SmsDestination;
// use Infobip\Model\SmsMessage;
// use Infobip\Api\SmsApi;
// use Infobip\Model\SmsTextContent;

// $base_url = 'ypkgp9.api.infobip.com';
// $api_key = '754d3b5d770df7f918d7bfa4288340b3-29192826-c9c2-46e4-9cbf-639f82aadc9b';

// $configuration = new Configuration($base_url,  $api_key);

// $sendSmsApi = new SmsApi(config: $configuration);

// $message = new SmsMessage(
//     destinations: [
//         new SmsDestination(
//             to: '+639563473280'
//         )
//     ],
//     content: new SmsTextContent(
//         text: 'This is a dummy SMS message sent using infobip-api-php-client.'
//     ),
//     sender: 'InfoSMS'
// );

// $request = new SmsRequest(messages: [$message]);

// try {
//     $smsResponse = $sendSmsApi->sendSmsMessages($request);
//     echo "Message Sent";
// } catch (ApiException $apiException) {
//     // HANDLE THE EXCEPTION
// }

// require_once 'HTTP/Request2.php';
require __DIR__ . '/vendor/autoload.php';

phpinfo();
// itexmo();

// function itexmo(){
//     try {
//         $ch= curl_init();
//         $itexmo = array (
//         'Email' => 'sample@itexmo.com',
//         'Password' => '123456789',
//         'ApiCode' => 'PR-SAMPLE12345',
//         'Recipients' => '["09999999999"]',
//         'Message' => 'Test Message',
//         );
//         curl_setopt($ch,CURLOPT_URL,"http://api.itexmo.com/api/broadcast");
//         curl_setopt($ch,CURLOPT_POST,1);
//         curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($itexmo));
//         curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        
//         $response=curl_exec($ch);
//         return $response;
    
//     } catch (Exception $ex) {
//         return $ex->getMessage();
//     }
// }

// curl -X POST https://app.philsms.com/api/v3/sms/send \
// -H 'Authorization: Bearer 49|LNFe8WJ7CPtvl2mzowAB4ll4enbFR0XGgnQh2qWY' \
// -H 'Content-Type: application/json' \
// -H 'Accept: application/json' \
// -d '{
// "recipient":"639171234567",
// "sender_id":"YourName",
// "type":"plain",
// "message":"This is a test message"
// }'
    
    
        


<?php
/**
 * Created by PhpStorm.
 * User: nav
 * Date: 21-03-17
 * Time: 13:42
 * Webhook to receive notification hook on intercom events.
 * Currently used only for lead creation on chat message
 */

require 'vendor/autoload.php';
use Intercom\IntercomClient;

$stdout = fopen('php://stdout', 'w');

function readInput(){
    $inputContent = '';
    $input = fopen('php://input' , 'rb');
    while (!feof($input)) {
        $inputContent .= fread($input, 4096);
    }
    fclose($input);
    return $inputContent;
}

function printOutput($msg){
    global $stdout;
    fwrite($stdout, $msg . "\n");
}

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}


/*
function verify_signature($payload_body){
    $ALGO = 'sha1';
    $SECRET = "secret";
    $expected = $_SERVER['HTTP_X_HUB_SIGNATURE'];
    if(empty($expected)){
        printOutput("Not signed. Not calculating");
    }
    else{
        $calculated = "sha1=" . hash_hmac($ALGO, $payload_body, $SECRET, false);
        printOutput("Expected  : " . $expected);
        printOutput("Calculated: " . $calculated);
        printOutput("Match?    : " . ($expected === $calculated ? "true" : "false"));
    }
}
*/

$DATA = readInput();

// verify_signature($DATA);
$push = json_decode($DATA);
// A visitor starts a conversation
if($push->topic == 'conversation.user.created'){

    // Intercom access tokens for retrieving lead data
    if($push->app_id == 'u31mumf0'){
        $access_key = 'dG9rOjBjMGIxY2I5XzUzNDFfNDg3Ml85ZDk5X2RhZmU4ZmFhMWZhYjoxOjA=';
    } else{
        $access_key = 'dG9rOjczZjk3Zjc0X2VmOTVfNDE2MF84MWNlX2NiY2IwYmYwZmE0NToxOjA=';
    }
    $intercom = new IntercomClient($access_key, null);

    if($push->data->item->type == 'conversation'){
        $conversation = $intercom->conversations->getConversation($push->data->item->id, []);
        $user = $intercom->users->getUser($push->data->item->user->id);
        
        // GA1.1. & GA1.2. string conversion to GA-CID
        if($cid){
            $cid = str_replace("GA1.1.", "", $user->custom_attributes->_ga);
            $cid = str_replace("GA1.2.", "", $user->custom_attributes->_ga);
        } else{
            $cid = gen_uuid();
        }
                
        // Make www.exact.com from https://www.exact.com for document path ga.
        $cleanhostname = preg_replace('#^https?://#', '', rtrim($conversation->conversation_message->url,'/'));

        // Array with data to send to GA
        $data = array(
            'v' => 1,
            'tid' => 'UA-4630958-6',
            'cid' => $cid,
            't' => 'event',
            'dp' => '/' . $cleanhostname,
            'ec' => 'Chat',
            'ea' => 'Reply'
        );

        // Curl endpoint to google analytics
        $url = 'http://www.google-analytics.com/collect';
        $content = http_build_query($data);
        $content = utf8_encode($content);
        $user_agent = 'Example/1.0 (https://www.exact.com/)';

        // Curl that data into GA
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-type: application/x-www-form-urlencoded'));
        curl_setopt($ch,CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_1_1);
        curl_setopt($ch,CURLOPT_POST, TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $content);
        $response = curl_exec($ch);
        curl_close($ch);
    }
}
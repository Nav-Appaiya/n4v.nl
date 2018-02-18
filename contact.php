<?php
/**
 * Created by PhpStorm.
 * User: nav
 * Date: 18-02-18
 * Time: 18:35
 */

require 'vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

if(isset($_POST) || $_GET['DEBUG']){
    echo '<pre>';

    $from = new SendGrid\Email($_POST['name'], "info@n4v.nl");
    $subject = "n4v.nl contactform with subject: " . $_POST['subject'];
    $to = new SendGrid\Email("Info n4v.nl", "info@n4v.nl");
    $content = new SendGrid\Content("text/plain", isset($_POST['name']) ? var_export($_POST, false) : "Debug message content.");

    $mail = new SendGrid\Mail($from, $subject, $to, $content);

    $apiKey = getenv('SENDGRID_API_KEY');
    $sg = new \SendGrid($apiKey);

    $response = $sg->client->mail()->send()->post($mail);


    echo $response->statusCode();
    print_r($response->headers());
    echo $response->body();

    print_r($response);exit;
}
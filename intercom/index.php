<?php
/**
 * Exact Intercom script for targeting visitors by event.  
 * User: Nav Appaiya <navarajh.appaiya@exact.com>
 * Date: 23-12-16
 * Time: 11:17
 */

require 'vendor/autoload.php';
use Intercom\IntercomClient;

// Only works when given correct body and visitor_id
if(isset($_GET['id']) && isset($_GET['body'])){

    // The intercom client
    $intercom = new IntercomClient("dG9rOjBjMGIxY2I5XzUzNDFfNDg3Ml85ZDk5X2RhZmU4ZmFhMWZhYjoxOjA=", null);

    $visitor = $_GET['id']; // visitor_id
    $body = $_GET['body'];  // urldecoded body for msg
    $from = $_GET['from'];  // from admin_id

    // Find or create the lead by its visitors_id
    $lead = $intercom->leads->create([
        'user_id' => $visitor
    ]);

    // Now send the message
    if ($lead) {

        $intercom->messages->create(array(
            "message_type" => "inapp",
            "subject" => "Exact - Manual message",
            "body" => urldecode($body),
            "template" => "plain",
            "from" => array(
                "type" => "admin",
                "id" => $from
            ),
            "to" => array(
                "type" => "contact",
                "id" => $lead->id
            )
        ));
    }

}

?>
<script>
  window.intercomSettings = {
    app_id: "ydt3dnof"
  };
</script>
<script>(function(){var w=window;var ic=w.Intercom;if(typeof ic==="function"){ic('reattach_activator');ic('update',intercomSettings);}else{var d=document;var i=function(){i.c(arguments)};i.q=[];i.c=function(args){i.q.push(args)};w.Intercom=i;function l(){var s=d.createElement('script');s.type='text/javascript';s.async=true;s.src='https://widget.intercom.io/widget/ydt3dnof';var x=d.getElementsByTagName('script')[0];x.parentNode.insertBefore(s,x);}if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})()</script>


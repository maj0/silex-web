<?php

function send_mail(
    $app,
    $subject = 'this is the subject',
    $message = 'this is the emails text content',
    $rcp = array('sajidm@web.de'/*,'sajidm@gmx.net'*/),
    $from = false
) {
    $domain = "mifon.tk";
    $uri = "https://api.mailgun.net/v3/${domain}/messages";
    //echo "<pre>",print_r($postString,1),"</pre>\n";
    $postfields = array(
     'from' => empty($from)?'Sajid Mahmood <sajidm@mifon.tk>':$from,
     'to' => empty($rcp) ? 'sajidm@web.de' : is_array($rcp) ? join(',', $rcp) : $rcp,
     'subject' => "$subject",
     'text' => "$message"
    );
    $apikey = 'api:'.$app['mailgun.options']['APIKEY'];
    #echo "apikey=$apikey<br>\n";
    #echo "<pre>",print_r($postfields),"</pre>\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $apikey);
    curl_setopt($ch, CURLOPT_URL, $uri);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

    $result = curl_exec($ch);

    #echo $result;
    return true;
}

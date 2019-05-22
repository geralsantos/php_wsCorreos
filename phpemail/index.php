<?php
//$server = "imap.gmail.com";
//$server = "Outlook.Office365.com";
$server = "mail01.peruvian.pe";
$username = "geral.poma@peruvian.pe";
$password = "Hiren149";
$mailbox="{".$server.":995/pop3/ssl/novalidate-cert}INBOX";
$inbox = imap_open($mailbox, $username, $password) or die('Ha fallado la conexión: ' . imap_last_error());
$emails = array_reverse(imap_search($inbox,'SUBJECT "RV: P9" ')); 
$salida="";$i=1;
$boletos=array();
//promedio de 50 boletos/correos al día
foreach($emails as $email_number) {
    $overview = json_decode(json_encode(imap_fetch_overview($inbox,$email_number,0)),true);
    if (count(explode("/",$overview[0]["subject"]))==5 && $overview[0]["to"]=="geral.poma@peruvian.pe") 
    {
        $text =(string) utf8_encode(imap_qprint( imap_fetchbody($inbox,$email_number,1) ) );
       
        $start = " $i.";
        $end = ' AS ';
        $pattern = sprintf(
            '/%s(.+?)%s/ims',
            preg_quote($start, '/'), preg_quote($end, '/')
        );
        if (preg_match($pattern, ($text),$result)) {
            print_r($result);
            $i++;
        }
        //preg_match("/".preg_quote(' 1.').".*?".preg_quote('AS ') ."/",$text, $result);
    }
}
?>
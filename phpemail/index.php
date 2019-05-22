<?php
//$server = "imap.gmail.com";
//$server = "Outlook.Office365.com";
$server = "mail01.peruvian.pe";
$username = "geral.poma@peruvian.pe";
$password = "Hiren149"; //contraseña
$mailbox="{".$server.":995/pop3/ssl/novalidate-cert}INBOX";
$inbox = imap_open($mailbox, $username, $password) or die('Ha fallado la conexión: ' . imap_last_error());

$emails = array_reverse(imap_search($inbox,'SUBJECT "RV: P9" SINCE "20 MAY 2019" ',SE_UID)); 
$salida="";
$grupo_boletos=array();
//promedio de 50 boletos/correos al día
foreach($emails as $email_number) {
    $overview = json_decode(json_encode(imap_fetch_overview($inbox,$email_number,0)),true);
    if (count(explode("/",$overview[0]["subject"]))==5 && $overview[0]["to"]=="geral.poma@peruvian.pe") 
    {
        $boletos=array();
        $i=1;
        $text =(string) utf8_encode(imap_qprint( imap_fetchbody($inbox,$email_number,1) ) );
        $start = " $i.";
        $end = ' AS ';
        $pattern = sprintf(
            '/%s(.+?)%s/ims',
            preg_quote($start, '/'), preg_quote($end, '/')
        );
        /*  ABAD AMENGUAL/ELEN MRS F LIM CUZ YG B 020D 040 6022400207040 */
        while (preg_match($pattern, ($text),$result)) {
            $start = " ".($i++).".";
            $end = ' AS ';
            $pattern = sprintf( '/%s(.+?)%s/ims', preg_quote($start, '/'), preg_quote($end, '/') );
            if (preg_match($pattern, ($text),$result)) {
                $result = implode(' ', array_reverse(explode(' ', $result[1])));
                $result = explode(' ',$result);
                $result= array_values(array_filter(array_map('trim',$result),'strlen'));

                $boleto = $result[0];
                $orden_chk_in = $result[1];
                $asiento = $result[2];
                $borded = $result[3];
                $clase = $result[4];
                $destino = $result[5];
                $origen = $result[6];
                $unknown = $result[7];
                $pax = array();
                foreach ($result as $key => $value) {
                    if ($key>7) {
                        $pax[]=$value;
                    }
                }
                $pax = implode(' ',array_reverse($pax));
                $apellido = explode('/',$pax)[0];
                $nombre = explode('/',$pax)[1];
                $boletos[]=array("nombre"=>$nombre,"apellido"=>$apellido,"unknown"=>$unknown,"origen"=>$origen,"destino"=>$destino,"clase"=>$clase,"borded"=>$borded,"asiento"=>$asiento,"orden_chk_in"=>$orden_chk_in,"boleto"=>$boleto);
            }
        }
        $grupo_boletos[]=$boletos;
        imap_mail_copy($inbox,"$email_number",'INBOX/Alexander', CP_UID) or die('Ha fallado la conexión: ' . imap_last_error());
        break;
    }
}
print_r( $grupo_boletos);
?>
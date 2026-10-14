<?php require __DIR__.'/inc/core.php';
// Optional: call this address every minute from a cron job to send queued Telegram messages faster.
header('Content-Type: text/plain');if(!hash_equals(setting('pb_secret'),(string)($_GET['key']??''))||setting('pb_secret')===''){http_response_code(403);exit('forbidden');}
$n=0;for($i=0;$i<8;$i++){$k=bq_drain(25,6.0);$n+=$k;if($k<25)break;}echo 'sent '.$n.', waiting '.(int)q('SELECT COUNT(*) FROM te_bq')->fetchColumn();

<?php require __DIR__.'/inc/core.php';
// Secure server-to-server conversion postback. The advertiser's own server calls this after it has verified the install or visit itself.
//   GET/POST conv.php?c=CAMPAIGN_ID&key=SECRET_KEY&uid=USER_ID[&t=TOKEN]
header('Content-Type: application/json');header('Cache-Control: no-store');
function cout(array $d,int $c=200){http_response_code($c);echo json_encode($d);exit;}
function clog(string $r){try{q('INSERT INTO te_cb (ip,qs,res) VALUES (?,?,?)',[$_SERVER['REMOTE_ADDR']??'',mb_substr('CONV '.preg_replace('/key=[^&]*/','key=***',$_SERVER['QUERY_STRING']??''),0,250),mb_substr($r,0,60)]);}catch(Throwable $e){}}
$R=array_merge($_GET,$_POST);$cid=(int)($R['c']??0);$uid=(int)($R['uid']??0);$tok=preg_replace('/[^a-f0-9]/','',(string)($R['t']??''));
$c=$cid>0?q('SELECT * FROM te_camps WHERE id=?',[$cid])->fetch():null;
if(!$c||!hash_equals((string)$c['ckey'],(string)($R['key']??''))){clog('bad key');cout(['ok'=>false,'error'=>'invalid campaign or key'],403);}
if($uid===0&&$c['type']==='bot'&&hash_equals(camp_vcode($cid),$tok)){q('UPDATE te_camps SET vstat=1,v_at=NOW() WHERE id=?',[$cid]);clog('bot setup ok c'.$cid);cout(['ok'=>true,'verified'=>true]);}
if($uid<=0)cout(['ok'=>false,'error'=>'missing uid'],400);
$r=conv_register($c,$uid,'s2s',$tok,true);clog($r['ok']?'ok c'.$cid:$r['error']);cout($r,$r['ok']?200:422);

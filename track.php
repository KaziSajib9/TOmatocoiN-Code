<?php require __DIR__.'/inc/core.php';
// Public tracking endpoint used by the mobile SDKs (a=install) and the website script track.js (a=visit).
// Protected by the one-time token that only the user who pressed Start received, a minimum elapsed time, and (for visits) the website origin.
header('Access-Control-Allow-Origin: *');header('Access-Control-Allow-Methods: GET, POST, OPTIONS');header('Access-Control-Allow-Headers: Content-Type');header('Cache-Control: no-store');
if(($_SERVER['REQUEST_METHOD']??'')==='OPTIONS'){http_response_code(204);exit;}
function tout(array $d,int $c=200){http_response_code($c);header('Content-Type: application/json');echo json_encode($d);exit;}
function tlog(string $r){try{q('INSERT INTO te_cb (ip,qs,res) VALUES (?,?,?)',[$_SERVER['REMOTE_ADDR']??'',mb_substr('TRACK '.preg_replace('/(key|pkey)=[^&]*/','$1=***',$_SERVER['QUERY_STRING']??''),0,250),mb_substr($r,0,60)]);}catch(Throwable $e){}}
$R=$_SERVER['REQUEST_METHOD']==='POST'?array_merge($_GET,$_POST):$_GET;
if(!$R&&($_SERVER['REQUEST_METHOD']??'')==='POST'){$j=json_decode((string)file_get_contents('php://input'),true);if(is_array($j))$R=array_merge($_GET,$j);}
$a=(string)($R['a']??'');$cid=(int)($R['c']??0);$uid=(int)($R['uid']??0);$tok=preg_replace('/[^a-f0-9]/','',(string)($R['t']??''));
if(!in_array($a,['install','visit','cfg','ping'],true)||$cid<=0)tout(['ok'=>false,'error'=>'bad request'],400);
$c=q('SELECT * FROM te_camps WHERE id=?',[$cid])->fetch();if(!$c)tout(['ok'=>false,'error'=>'unknown campaign'],404);
if($a==='cfg')tout(['ok'=>true,'wait'=>(int)$c['wait'],'active'=>$c['status']==='Active']);
if($a==='ping'){// the developer opened their page with the test link; this marks the script as installed
 if($c['type']!=='visit'||!hash_equals(camp_vcode($cid),(string)($R['code']??'')))tout(['ok'=>false,'error'=>'invalid code'],403);
 $o=(string)($_SERVER['HTTP_ORIGIN']??$_SERVER['HTTP_REFERER']??'');$oh=strtolower((string)parse_url($o,PHP_URL_HOST));$ch=strtolower((string)parse_url((string)$c['url'],PHP_URL_HOST));
 if($oh===''||$ch===''||!($oh===$ch||substr($oh,-strlen($ch)-1)==='.'.$ch||substr($ch,-strlen($oh)-1)==='.'.$oh)){tlog('ping origin mismatch');tout(['ok'=>false,'error'=>'This page is not on the website you entered in the campaign'],403);}
 q('UPDATE te_camps SET vstat=1,v_at=NOW() WHERE id=?',[$cid]);tout(['ok'=>true,'verified'=>true]);}
if($a==='install'&&$c['type']==='bot'&&$uid===0&&hash_equals(camp_vcode($cid),$tok)){if(!hash_equals((string)$c['pkey'],(string)($R['key']??''))){tlog('bad key');tout(['ok'=>false,'error'=>'invalid key'],403);}q('UPDATE te_camps SET vstat=1,v_at=NOW() WHERE id=?',[$cid]);tlog('bot setup ok c'.$cid);tout(['ok'=>true,'verified'=>true]);}
if($uid<=0||strlen($tok)!==16)tout(['ok'=>false,'error'=>'missing user or token'],400);
if($a==='install'){if(!in_array($c['type'],['app','bot'],true)||!hash_equals((string)$c['pkey'],(string)($R['key']??''))){tlog('bad key');tout(['ok'=>false,'error'=>'invalid key'],403);}}
if($a==='visit'){if($c['type']!=='visit')tout(['ok'=>false,'error'=>'not a visit campaign'],400);
 $o=(string)($_SERVER['HTTP_ORIGIN']??$_SERVER['HTTP_REFERER']??'');if($o!==''){$oh=strtolower((string)parse_url($o,PHP_URL_HOST));$ch=strtolower((string)parse_url((string)$c['url'],PHP_URL_HOST));$ok=$oh!==''&&$ch!==''&&($oh===$ch||substr($oh,-strlen($ch)-1)==='.'.$ch||substr($ch,-strlen($oh)-1)==='.'.$oh);
  if(!$ok){tlog('origin mismatch');tout(['ok'=>false,'error'=>'origin mismatch'],403);}}}
$r=conv_register($c,$uid,$a==='install'?'sdk':'pixel',$tok,false);tlog($r['ok']?'ok c'.$cid:$r['error']);tout($r,$r['ok']?200:422);

<?php require __DIR__.'/inc/core.php'; // AdsGram Reward URL: ?userid=[userId]&key=SECRET   (Task block: add &t=1)
function cb(string $r){q('INSERT INTO te_cb (ip,qs,res) VALUES (?,?,?)',[$_SERVER['REMOTE_ADDR']??'',mb_substr(preg_replace('/key=[^&]*/','key=***',$_SERVER['QUERY_STRING']??''),0,250),$r]);
 if(random_int(1,40)===1)q('DELETE FROM te_cb WHERE at<NOW()-INTERVAL 7 DAY');echo $r;exit;}
if(!hash_equals(setting('ag_secret'),(string)($_GET['key']??''))){http_response_code(403);cb('bad key');}
$id=(int)($_GET['userid']??$_GET['userId']??$_GET['user_id']??0);
$u=q('SELECT * FROM te_users WHERE tg_id=? AND banned=0',[$id])->fetch(); if(!$u) cb('no user');
if(isset($_GET['t'])){ if(!agt_on()) cb('tasks off'); $row=today($id); $LV=lvl((int)$u['level']);
 $w=(int)setting('ag_task_wait','10'); $st=(int)q('SELECT started FROM te_agt WHERE tg_id=?',[$id])->fetchColumn(); q('UPDATE te_agt SET started=0 WHERE tg_id=?',[$id]);
 if($w>0&&($st<=0||time()-$st<$w)) cb('too early: the user must stay on the task for '.$w.'s');
 if(q('UPDATE te_adlog SET t=t+1 WHERE tg_id=? AND d=CURDATE() AND t<?',[$id,(int)lvv($LV,'agt_limit','ag_task_limit','5')])->rowCount()!==1) cb('task limit');
 credit($u,lvv($LV,'agt_reward','ag_task_reward','10')); cb('ok task');}
$r=finish_ad($u); cb(isset($r['ok'])?'ok':'ignored: '.$r['error']);

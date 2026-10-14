<?php require __DIR__.'/inc/core.php'; // App install postback: postback.php?key=SECRET&uid={user id}&task={task id}
function pcb(string $r){q('INSERT INTO te_cb (ip,qs,res) VALUES (?,?,?)',[$_SERVER['REMOTE_ADDR']??'',mb_substr('POSTBACK '.preg_replace('/key=[^&]*/','key=***',$_SERVER['QUERY_STRING']??''),0,250),$r]);echo $r;exit;}
if(!hash_equals(setting('pb_secret'),(string)($_GET['key']??''))){http_response_code(403);pcb('bad key');}
$id=(int)($_GET['uid']??$_GET['userid']??0);$tid=(int)($_GET['task']??$_GET['tid']??0);
$u=q('SELECT * FROM te_users WHERE tg_id=? AND banned=0 AND is_demo=0',[$id])->fetch();if(!$u)pcb('no user');
$t=q("SELECT * FROM te_tasks WHERE id=? AND kind='app' AND verify='postback'",[$tid])->fetch();if(!$t)pcb('no task');
$l=q('SELECT * FROM te_log WHERE tg_id=? AND task_id=?',[$id,$tid])->fetch();if(!$l||(int)$l['started']<=0)pcb('not started in the app');
$pdo=db();$pdo->beginTransaction();
if(q('UPDATE te_log SET done=1,done_at=? WHERE tg_id=? AND task_id=? AND done=0',[time(),$id,$tid])->rowCount()!==1){$pdo->rollBack();pcb('already done');}
$rw=round((float)$t['reward']*max(0,(int)(lvl((int)$u['level'])['task_pct']??100))/100,2);credit($u,$rw);
q('INSERT INTO te_notices (tg_id,title,body) VALUES (?,?,?)',[$id,'App install confirmed','Your install of "'.mb_substr((string)$t['title'],0,80).'" was confirmed. You received '.rtrim(rtrim(number_format($rw,2,'.',''),'0'),'.').' points.']);
$pdo->commit();pcb('ok');

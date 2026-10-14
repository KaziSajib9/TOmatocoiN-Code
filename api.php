<?php require __DIR__.'/inc/core.php'; $u=me(); $id=(int)$u['tg_id']; $a=$_GET['a']??'me';if($a==='me')bq_kick(); $P=$_POST; $pdo=db(); $tid=(int)($P['id']??0);
function res(array $r){out($r,isset($r['ok'])?200:422);}
function av_del(string $av){if(strpos($av,'u:')===0){$h=preg_replace('/[^a-f0-9]/','',substr($av,2));if($h!=='')@unlink(__DIR__."/uploads/avatars/$h.jpg");}}
function wd_in(array $u,array $P): array{
 $addr=trim((string)($P['address']??'')); $memo=mb_substr(trim((string)($P['memo']??'')),0,40); $amt=(float)($P['amount']??0); $min=(float)setting('min_withdraw','500'); $rate=(float)setting('points_per_ton','0');
 if(!preg_match('/^(?:[EUk0]Q[A-Za-z0-9_\-]{46}|-?\d:[0-9a-fA-F]{64})$/',$addr)) return ['Enter a valid TON network wallet address (starts with UQ or EQ)'];
 if($rate<=0) return ['Withdrawals are not available yet'];
 if($amt<$min) return ['Minimum withdrawal is '.$min.' points'];
 if((float)$u['balance']<$amt) return ['Not enough balance'];
 if((int)q("SELECT COUNT(*) FROM te_wd WHERE tg_id=? AND status IN ('Pending','Processing','Verifying')",[$u['tg_id']])->fetchColumn()>0) return ['You already have a withdrawal in progress'];
 return [null,$addr,$memo,$amt,$rate,hash('sha256',$addr.'|'.number_format($amt,2,'.',''))];}
if($a==='ping') out(['ok'=>true]);
if($a==='me'){
 $L=[]; foreach(q('SELECT task_id,started,done,done_at,fail_at FROM te_log WHERE tg_id=?',[$id]) as $r) $L[$r['task_id']]=$r;
 $AP=q('SELECT task_id,status FROM te_apps WHERE tg_id=?',[$id])->fetchAll(PDO::FETCH_KEY_PAIR); $tp=max(0,(int)(lvl((int)$u['level'])['task_pct']??100)); $T=[]; foreach(q('SELECT id,title,kind,reward,wait,repeat_m,verify,camp_id,icon,test_only FROM te_tasks WHERE active=1 ORDER BY id DESC') as $t){if((int)$t['test_only']&&!is_admin_tg($id))continue;unset($t['test_only']);if($t['icon']===null||$t['icon']==='')unset($t['icon']);$l=$L[$t['id']]??null;$st=0;$next=0;
  if($l){if($l['done']){$rh=(int)$t['repeat_m'];$rem=$rh>0?$rh*60-(time()-(int)$l['done_at']):0;if($rh>0&&$rem<=0)$st=0;else{$st=2;$next=max(0,$rem);}}else $st=((int)$l['started']>0)?1:0;}
  if($t['kind']==='app'){$ps=$AP[$t['id']]??'';if($st!==2&&$ps==='Pending')$st=3;$t['rej']=($st===1&&$ps==='Rejected');}
  $t['st']=$st; $t['camp']=(int)$t['camp_id']>0;unset($t['camp_id']);$t['reward']=round((float)$t['reward']*($t['camp']?100:$tp)/100,2); $t['next']=$next; $t['retry']=($st===0&&$l&&(int)($l['fail_at']??0)>0&&(int)dset('dev_fail_wait')>0)?max(0,(int)$l['fail_at']+(int)dset('dev_fail_wait')-time()):0; $t['left']=($st===1&&$t['kind']==='link')?max(0,$t['wait']-(time()-$l['started'])):0; $T[]=$t;}
 $rc=(int)q('SELECT COUNT(*) FROM te_users WHERE ref_by=?',[$id])->fetchColumn();
 $H=q('SELECT id,amount,ton,number,status,tx,note,created_at FROM te_wd WHERE tg_id=? ORDER BY id DESC LIMIT 10',[$id])->fetchAll();
 $row=today($id); $lv=lvl((int)$u['level']); [$dd,$dt]=daily($u,$row);
 $sub=max(1,min(3,(int)$u['sub'])); $curFrom=$sub>=3?(float)($lv['rank3_pts']??0):($sub===2?(float)($lv['rank2_pts']??0):(float)($lv['upgrade_cost']??0));
 if($sub<3&&($lv['rank'.($sub+1).'_pts']??null)!==null){$nl=null;$nc=(float)$lv['rank'.($sub+1).'_pts'];$nextName=lv_name($lv,(int)$u['level']).' '.($sub+1);}
 else{$nl=q('SELECT * FROM te_levels WHERE level=?',[(int)$u['level']+1])->fetch();$nc=$nl?$nl['upgrade_cost']:false;$nextName=$nl?lv_name($nl,(int)$u['level']+1).' 1':'';} $lk=setting('app_link');
 $adsT=(int)q('SELECT COALESCE(SUM(n+i+t),0) FROM te_adlog WHERE tg_id=?',[$id])->fetchColumn();
 $tdone=(int)q('SELECT COUNT(*) FROM te_log WHERE tg_id=? AND done=1',[$id])->fetchColumn();
 $paid=(int)q("SELECT COUNT(*) FROM te_wd WHERE tg_id=? AND status='Paid'",[$id])->fetchColumn();
 $rank=(int)q('SELECT COUNT(*)+1 FROM te_users WHERE banned=0 AND '.dsql().' AND total>?',[$u['total']])->fetchColumn();
 $unread=(int)q('SELECT COUNT(*) FROM te_notices WHERE tg_id=? AND read_at IS NULL',[$id])->fetchColumn();
 out(['home_add'=>setting('home_add','1')==='1','home_pts'=>(float)setting('home_pts','0'),'home_claimed'=>(int)($u['home_bonus']??0)===1,'home_need'=>(float)setting('home_pts','0')>0?home_need($u):'','dev_on'=>dset('dev_on')==='1','name'=>$u['name'],'avatar'=>$u['avatar'],'photo'=>$u['photo'],'balance'=>(float)$u['balance'],'total'=>(float)$u['total'],'tasks'=>$T,'refs'=>$rc,'ref_earn'=>(float)$u['ref_earn'],'pct'=>(float)setting('ref_percent','0'),
  'link'=>$lk?$lk.'?startapp='.$id:'','min'=>(float)setting('min_withdraw','500'),'ton_rate'=>(float)setting('points_per_ton','0'),'ann'=>setting('announcement'),'hist'=>$H,'unread'=>$unread,'help'=>['reasons'=>help_reasons(),'tips'=>help_tips(),'img'=>setting('help_img_on','1')==='1','dev'=>setting('dev_on','1')==='1','answered'=>(int)q("SELECT COUNT(*) FROM te_tickets WHERE tg_id=? AND status='Answered'",[$id])->fetchColumn()],
  'email'=>$u['email_ok']?mask_mail((string)$u['email']):'','email_ok'=>(bool)$u['email_ok'],'ton_addr'=>$u['ton_addr'],'ton_memo'=>$u['ton_memo'],
  'nextlv'=>$nl?['ad_limit'=>(int)$nl['ad_limit'],'ad_reward'=>(float)$nl['ad_reward'],'int_limit'=>(int)lvv($nl,'int_limit','int_limit','5'),'int_reward'=>lvv($nl,'int_reward','int_reward','3'),'agt_limit'=>(int)lvv($nl,'agt_limit','ag_task_limit','5'),'agt_reward'=>lvv($nl,'agt_reward','ag_task_reward','10'),'task_pct'=>(int)$nl['task_pct'],'bonus'=>lvv($nl,'bonus','daily_bonus','0')]:null,'level_name'=>lv_name($lv,(int)$u['level']),'next_name'=>$nextName,'rank_label'=>lv_name($lv,(int)$u['level']).' '.$sub,'sub'=>$sub,'from'=>$curFrom,'level'=>(int)$u['level'],'next_cost'=>$nc===false?null:(float)$nc,'rank'=>$rank,'since'=>substr((string)$u['created_at'],0,10),
  'stats'=>['ads'=>$adsT,'tasks'=>$tdone],'paid_pub'=>setting('show_paid','0')==='1'?(function(){$r=q("SELECT COUNT(*) n,COALESCE(SUM(ton),0) t FROM te_wd WHERE status='Paid'")->fetch();return ['n'=>(int)$r['n'],'ton'=>(float)$r['t']];})():null,
  'badges'=>['starter'=>true,'ads10'=>$adsT>=10,'ads100'=>$adsT>=100,'tasks5'=>$tdone>=5,'refs3'=>$rc>=3,'earner'=>(float)$u['total']>=1000,'paid'=>$paid>=1,'top10'=>$rank<=10&&(float)$u['total']>0,'early'=>(int)$u['early']===1],
  'ad'=>['on'=>ad_on(),'block'=>setting('ag_block'),'strict'=>setting('ag_pb')==='1','n'=>(int)$row['n'],'limit'=>(int)$lv['ad_limit'],'reward'=>(float)$lv['ad_reward'],'cd_left'=>max(0,(int)setting('ad_cd','30')-(time()-(int)$row['last_at']))],
  'int'=>['on'=>int_on(),'block'=>setting('int_block'),'n'=>(int)$row['i'],'limit'=>(int)lvv($lv,'int_limit','int_limit','5'),'reward'=>lvv($lv,'int_reward','int_reward','3'),'cd_left'=>max(0,(int)setting('int_cd','60')-(time()-(int)$row['i_last']))],
  'agt'=>['wait'=>(int)setting('ag_task_wait','10'),'on'=>agt_on(),'block'=>setting('ag_task_block'),'reward'=>lvv($lv,'agt_reward','ag_task_reward','10'),'n'=>(int)$row['t'],'limit'=>(int)lvv($lv,'agt_limit','ag_task_limit','5')],
  'bonus'=>['amt'=>lvv($lv,'bonus','daily_bonus','0'),'ready'=>$u['last_bonus']!==date('Y-m-d'),'needad'=>setting('bonus_ad','1')==='1'&&ad_on(),'done'=>$dd,'total'=>$dt]]);}
if($a==='notices') out(['list'=>q('SELECT id,title,body,created_at,read_at FROM te_notices WHERE tg_id=? ORDER BY id DESC LIMIT 30',[$id])->fetchAll()]);
if($a==='notice_read'){q('UPDATE te_notices SET read_at=NOW() WHERE tg_id=? AND read_at IS NULL',[$id]);out(['ok'=>true]);}
if($a==='email_send'){$e=strtolower(trim((string)($P['email']??''))); if(!gmail_ok($e)) out(['error'=>'Enter a valid Gmail address'],422);
 if((int)q('SELECT COUNT(*) FROM te_users WHERE email_norm=? AND email_ok=1 AND tg_id<>?',[gmail_norm($e),$id])->fetchColumn()>0) out(['error'=>'This Gmail is already linked to another account'],422);
 $err=code_send($id,'email',$e,$e,'Verify your email',"Your verification code is {code}\n\nIt expires in 10 minutes. If you did not request this, you can ignore this email."); if($err) out(['error'=>$err],429); out(['ok'=>true]);}
if($a==='email_verify'){$e=strtolower(trim((string)($P['email']??'')));$code=preg_replace('/\D/','',(string)($P['code']??'')); if(!gmail_ok($e)) out(['error'=>'Enter a valid Gmail address'],422);
 $err=code_check($id,'email',$e,$code); if($err) out(['error'=>$err],422);
 if((int)q('SELECT COUNT(*) FROM te_users WHERE email_norm=? AND email_ok=1 AND tg_id<>?',[gmail_norm($e),$id])->fetchColumn()>0) out(['error'=>'This Gmail is already linked to another account'],422);
 q('UPDATE te_users SET email=?,email_norm=?,email_ok=1 WHERE tg_id=?',[$e,gmail_norm($e),$id]); out(['ok'=>true]);}
if($a==='wdcode'){ if(!$u['email_ok']) out(['error'=>'Verify your Gmail in Profile first'],422);
 $x=wd_in($u,$P); if($x[0]) out(['error'=>$x[0]],422);
 $err=code_send($id,'wd',$x[5],(string)$u['email'],'Withdrawal confirmation code',"Your withdrawal confirmation code is {code}\n\nAmount: ".number_format($x[3])." points (about ".round($x[3]/$x[4],4)." USDT on TON network)\nTo: ".substr($x[1],0,6).'...'.substr($x[1],-6)."\n\nIt expires in 10 minutes. If you did not request this, do not share this code and ignore this email.");
 if($err) out(['error'=>$err],429); out(['ok'=>true]);}
if($a==='tickets') out(['list'=>q('SELECT t.id,t.reason,t.custom,t.status,t.created_at,t.updated_at,(SELECT body FROM te_tmsg m WHERE m.ticket_id=t.id ORDER BY m.id DESC LIMIT 1) last FROM te_tickets t WHERE t.tg_id=? ORDER BY t.updated_at DESC LIMIT 30',[$id])->fetchAll()]);
if($a==='ticket'){$t=q('SELECT * FROM te_tickets WHERE id=? AND tg_id=?',[$tid,$id])->fetch();if(!$t)out(['error'=>'Ticket not found'],404);
 out(['t'=>['id'=>(int)$t['id'],'reason'=>$t['reason'],'custom'=>$t['custom'],'status'=>$t['status']],'msgs'=>q('SELECT sender,body,img,created_at FROM te_tmsg WHERE ticket_id=? ORDER BY id',[$tid])->fetchAll()]);}
if($a==='ticket_new'){$reason=(string)($P['reason']??'');if(!in_array($reason,help_reasons(),true))out(['error'=>'Please choose a reason'],422);
 $custom=mb_substr(trim((string)($P['custom']??'')),0,100);if($reason==='Others'&&mb_strlen($custom)<3)out(['error'=>'Please write your reason'],422);
 $body=mb_substr(trim((string)($P['body']??'')),0,1500);if(mb_strlen($body)<10)out(['error'=>'Please describe your problem (at least 10 characters)'],422);
 $img=null;if(!empty($P['img'])){if(setting('help_img_on','1')!=='1')out(['error'=>'Screenshots are turned off'],422);$img=help_img_save((string)$P['img']);if(!$img)out(['error'=>'The screenshot could not be read. Use a PNG or JPG image.'],422);}
 if((int)q("SELECT COUNT(*) FROM te_tickets WHERE tg_id=? AND status<>'Closed'",[$id])->fetchColumn()>=3)out(['error'=>'You already have 3 open tickets. Please wait for a reply'],429);
 if((int)q('SELECT COUNT(*) FROM te_tickets WHERE tg_id=? AND created_at>NOW()-INTERVAL 1 MINUTE',[$id])->fetchColumn()>0)out(['error'=>'Please wait a minute before sending another ticket'],429);
 $wid=(int)($P['wd_id']??0);if($wid&&!q('SELECT 1 FROM te_wd WHERE id=? AND tg_id=?',[$wid,$id])->fetch())$wid=0;
 q('INSERT INTO te_tickets (tg_id,reason,custom,wd_id,updated_at) VALUES (?,?,?,?,NOW())',[$id,$reason,$reason==='Others'?$custom:null,$wid?:null]);$tk=$pdo->lastInsertId();
 q("INSERT INTO te_tmsg (ticket_id,sender,body,img) VALUES (?,'user',?,?)",[$tk,$body,$img]);
 notify_admin('notify_help',"New help ticket #$tk\nUser: {$u['name']} ($id)".($img?"\n(with screenshot)":'')."\nReason: ".($reason==='Others'?"Others: $custom":$reason).($wid?"\nRelated withdrawal: #$wid":'')."\n\n".mb_substr($body,0,500));out(['ok'=>true,'id'=>(int)$tk]);}
if($a==='ticket_reply'){$t=q('SELECT * FROM te_tickets WHERE id=? AND tg_id=?',[$tid,$id])->fetch();if(!$t)out(['error'=>'Ticket not found'],404);if($t['status']==='Closed')out(['error'=>'This ticket is closed'],422);
 $body=mb_substr(trim((string)($P['body']??'')),0,1500);$img=null;if(!empty($P['img'])){if(setting('help_img_on','1')!=='1')out(['error'=>'Screenshots are turned off'],422);$img=help_img_save((string)$P['img']);if(!$img)out(['error'=>'The screenshot could not be read. Use a PNG or JPG image.'],422);}
 if($body===''&&!$img)out(['error'=>'Write a message first'],422);
 if($img&&(int)q("SELECT COUNT(*) FROM te_tmsg WHERE ticket_id=? AND img IS NOT NULL",[$tid])->fetchColumn()>=10)out(['error'=>'You can add up to 10 screenshots to one ticket'],429);
 if((int)q('SELECT COUNT(*) FROM te_tmsg WHERE ticket_id=?',[$tid])->fetchColumn()>=100)out(['error'=>'This ticket has too many messages. Please open a new one'],429);
 q("INSERT INTO te_tmsg (ticket_id,sender,body,img) VALUES (?,'user',?,?)",[$tid,$body,$img]);q("UPDATE te_tickets SET status='Open',updated_at=NOW() WHERE id=?",[$tid]);
 notify_admin('notify_help',"Reply on help ticket #$tid\nUser: {$u['name']} ($id)".($img?"\n(with screenshot)":'')."\n\n".mb_substr($body,0,500));out(['ok'=>true]);}
if($a==='agt_go'){$t=time();q('INSERT INTO te_agt (tg_id,started) VALUES (?,?) ON DUPLICATE KEY UPDATE started=IF(started=0 OR ?-started>1800,VALUES(started),started)',[$id,$t,$t]);out(['ok'=>true,'wait'=>(int)setting('ag_task_wait','10')]);}
if($a==='top'){$o=[];foreach(q('SELECT tg_id,name,total,avatar,photo,is_demo FROM te_users WHERE banned=0 AND '.dsql().' AND total>0 ORDER BY total DESC LIMIT 10') as $r)
  $o[]=['n'=>mb_substr((string)$r['name'],0,3).'•••','p'=>(float)$r['total'],'av'=>$r['avatar'],'ph'=>$r['photo'],'me'=>(int)$r['tg_id']===$id,'dm'=>(int)$r['is_demo']];out(['top'=>$o]);}
if($a==='avatar'){$c=(string)($P['choice']??''); if($c!=='tg'&&!preg_match('/^p[1-8]$/',$c)) out(['error'=>'Invalid choice'],422);
 av_del((string)$u['avatar']); q('UPDATE te_users SET avatar=? WHERE tg_id=?',[$c,$id]); out(['ok'=>true]);}
if($a==='avatar_up'){$d=preg_replace('#^data:image/\w+;base64,#','',(string)($P['img']??'')); $bin=base64_decode($d,true);
 if(!$bin||strlen($bin)>400000) out(['error'=>'Image is too large (max 400 KB)'],422);
 if(!function_exists('imagecreatefromstring')) out(['error'=>'Uploads are not supported on this server. Please pick a preset avatar'],422);
 $inf=@getimagesizefromstring($bin); if(!$inf||$inf[0]>4000||$inf[1]>4000) out(['error'=>'Unsupported image'],422);
 $im=@imagecreatefromstring($bin); if(!$im) out(['error'=>'Unsupported image'],422);
 $w=imagesx($im);$h=imagesy($im);$s=min($w,$h);$o=imagecreatetruecolor(256,256);imagecopyresampled($o,$im,0,0,(int)(($w-$s)/2),(int)(($h-$s)/2),256,256,$s,$s);
 $dir=__DIR__.'/uploads/avatars'; if(!is_dir($dir))@mkdir($dir,0755,true); $hex=bin2hex(random_bytes(8));
 if(!@imagejpeg($o,"$dir/$hex.jpg",85)) out(['error'=>'Could not save the image'],500);
 av_del((string)$u['avatar']); q('UPDATE te_users SET avatar=? WHERE tg_id=?',['u:'.$hex,$id]); out(['ok'=>true]);}
if($a==='home_claim'){$pts=(float)setting('home_pts','0');if($pts<=0||setting('home_add','1')!=='1')out(['error'=>'This reward is not available'],422);
 if(($hn=home_need($u))!=='')out(['error'=>$hn],422);
 if(q('UPDATE te_users SET home_bonus=1 WHERE tg_id=? AND home_bonus=0',[$id])->rowCount()!==1)out(['error'=>'You already received this reward'],422);
 credit($u,$pts);q('INSERT INTO te_notices (tg_id,title,body) VALUES (?,?,?)',[$id,'Home Screen reward','You received '.rtrim(rtrim(number_format($pts,2,'.',''),'0'),'.').' points for adding the app to your Home Screen.']);out(['ok'=>true,'reward'=>$pts]);}
if($a==='bonus'){
 if(setting('bonus_ad','1')==='1'&&ad_on()) out(['error'=>'Watch an ad to claim the bonus'],422);
 [$d,$t]=daily($u,today($id)); if($d<$t) out(['error'=>'Complete all daily tasks first'],422);
 $c=lvv(lvl((int)$u['level']),'bonus','daily_bonus','0'); if($c<=0) out(['error'=>'Daily bonus is currently disabled'],422);
 if(q('UPDATE te_users SET balance=balance+?,total=total+?,last_bonus=CURDATE() WHERE tg_id=? AND (last_bonus IS NULL OR last_bonus<CURDATE())',[$c,$c,$id])->rowCount()!==1) out(['error'=>'Today\'s bonus is already claimed'],429);
 out(['ok'=>true,'reward'=>$c]);}
if($a==='upgrade') out(['error'=>'Levels now upgrade automatically from your total earned points'],410);
if($a==='adstart'||$a==='bstart') res(ad_start($u,$a==='adstart'?'ad':'bonus'));
if($a==='adclaim'){ if(setting('ag_pb')==='1') out(['error'=>'Not allowed'],403);
 $row=today($id); $s=(int)$row['started']; $el=time()-$s; $min=(int)setting('ad_min','5');
 if($s<=0||$el>900) out(['error'=>'Session expired, please try again'],422);
 if($el<$min) out(['wait'=>$min-$el+1]);
 res(finish_ad($u));}
if($a==='istart'||$a==='iclaim'){ if(!int_on()) out(['error'=>'Ads are currently unavailable'],422);
 $row=today($id); $LV=lvl((int)$u['level']); $lim=(int)lvv($LV,'int_limit','int_limit','5'); if((int)$row['i']>=$lim) out(['error'=>'Daily limit reached'],429);
 if($a==='istart'){ if(time()-(int)$row['i_last']<(int)setting('int_cd','60')) out(['error'=>'Please wait a moment and try again'],429);
  q('UPDATE te_adlog SET i_start=? WHERE tg_id=? AND d=CURDATE()',[time(),$id]); out(['ok'=>true]);}
 $s=(int)$row['i_start']; $el=time()-$s; $min=(int)setting('int_min','5');
 if($s<=0||$el>900) out(['error'=>'Session expired, please try again'],422);
 if($el<$min) out(['wait'=>$min-$el+1]);
 if(q('UPDATE te_adlog SET i=i+1,i_last=?,i_start=0 WHERE tg_id=? AND d=CURDATE() AND i_start>0 AND i<?',[time(),$id,$lim])->rowCount()!==1) out(['error'=>'Already claimed'],422);
 $c=lvv($LV,'int_reward','int_reward','3'); credit($u,$c); out(['ok'=>true,'reward'=>$c]);}
if($a==='task_fail'){q('UPDATE te_log SET started=0,away=0 WHERE tg_id=? AND task_id=? AND done=0',[$id,$tid]);out(['ok'=>true]);}
if($a==='task_back'){$aw=max(0,min(86400,(int)($P['away']??0)));q('UPDATE te_log SET away=? WHERE tg_id=? AND task_id=? AND done=0 AND started>0',[$aw,$id,$tid]);out(['ok'=>true]);}
if(strpos($a,'dev_')===0){
 if(dset('dev_on')!=='1')out(['error'=>'The Developer Programme is not available right now'],403);
 $dv=dev_get($id);
 if($a==='dev_join'){ if(!$u['email_ok'])out(['error'=>'Verify your Gmail in Profile first'],422); if($dv)out(['ok'=>true]);
  $co=mb_substr(trim((string)($P['company']??'')),0,80);$ct=mb_substr(trim((string)($P['contact']??'')),0,120);
  if(mb_strlen($co)<2)out(['error'=>'Enter your company, studio or app name'],422); if(mb_strlen($ct)<3)out(['error'=>'Enter a contact (Telegram @username or email)'],422);
  q('INSERT IGNORE INTO te_devs (tg_id,company,contact) VALUES (?,?,?)',[$id,$co,$ct]); notify_admin('notify_dev',"New developer joined\nUser: {$u['name']} ($id)\nCompany: $co\nContact: $ct"); out(['ok'=>true]);}
 if($a==='dev_me'){ if(!$dv)out(['joined'=>false,'email_ok'=>(bool)$u['email_ok'],'info'=>dev_info()]);
  $C=q("SELECT c.id,c.type,c.title,c.url,c.pkg,c.store,c.repeat_m,c.icon,c.vstat,c.test,c.cost,c.reward,c.wait,c.daily_cap,c.total_cap,c.ckey,c.pkey,c.status,c.note,c.created_at,
   (SELECT COUNT(*) FROM te_conv v WHERE v.camp_id=c.id AND v.status NOT IN ('Reversed','Test')) n,(SELECT COUNT(*) FROM te_conv v WHERE v.camp_id=c.id AND v.status NOT IN ('Reversed','Test') AND v.created_at>=CURDATE()) today,
   (SELECT COALESCE(SUM(v.cost),0) FROM te_conv v WHERE v.camp_id=c.id AND v.status NOT IN ('Reversed','Test')) spent FROM te_camps c WHERE c.tg_id=? AND c.status<>'Deleted' ORDER BY c.id DESC",[$id])->fetchAll();
  foreach($C as &$cx){$cx['vcode']=camp_vcode((int)$cx['id']);$cx['test']=(int)$cx['test'];}unset($cx);
  $D=q('SELECT id,amount,txid,status,admin_note,created_at FROM te_dev_dep WHERE tg_id=? ORDER BY id DESC LIMIT 20',[$id])->fetchAll();
  $V=q("SELECT v.id,v.camp_id,c.title,c.type,v.src,v.cost,v.status,v.created_at,v.tg_id FROM te_conv v JOIN te_camps c ON c.id=v.camp_id WHERE v.dev_id=? AND v.status<>'Test' ORDER BY v.id DESC LIMIT 40",[$id])->fetchAll();
  foreach($V as &$x){$x['user']=mask_id((int)$x['tg_id']);unset($x['tg_id']);} unset($x);
  $S=['app'=>0,'visit'=>0,'today'=>0];foreach(q("SELECT c.type,COUNT(*) n,SUM(v.created_at>=CURDATE()) t FROM te_conv v JOIN te_camps c ON c.id=v.camp_id WHERE v.dev_id=? AND v.status NOT IN ('Reversed','Test') GROUP BY c.type",[$id]) as $r){$S[$r['type']]=(int)$r['n'];$S['today']+=(int)$r['t'];}
  $W=[];foreach(q("SELECT DATE(created_at) d,COUNT(*) n FROM te_conv WHERE dev_id=? AND status NOT IN ('Reversed','Test') AND created_at>=CURDATE()-INTERVAL 6 DAY GROUP BY d",[$id]) as $r)$W[$r['d']]=(int)$r['n'];
  $days=[];for($i=6;$i>=0;$i--){$k=date('Y-m-d',strtotime("-$i day"));$days[]=['d'=>substr($k,5),'n'=>$W[$k]??0];}
  out(['joined'=>true,'dep_code'=>dep_code($id),'points'=>(float)q('SELECT balance FROM te_users WHERE tg_id=?',[$id])->fetchColumn(),'dev'=>['company'=>$dv['company'],'contact'=>$dv['contact'],'status'=>$dv['status'],'balance'=>(float)$dv['balance'],'spent'=>(float)$dv['spent'],'deposited'=>(float)$dv['deposited']],'stats'=>$S,'days'=>$days,'camps'=>$C,'deps'=>$D,'convs'=>$V,'info'=>dev_info()]);}
 if(!$dv)out(['error'=>'Join the Developer Programme first'],403);
 if($dv['status']!=='Active')out(['error'=>'Your developer account is suspended. Contact support.'],403);
 if($a==='dev_play'){$pk=play_pkg((string)($P['q']??''));if($pk==='')out(['error'=>'Paste the Google Play link of your app (it contains id=com.company.app) or the package name'],422);
  $i=play_info($pk);if(!$i)out(['error'=>'We could not find this app on Google Play. Check the link, or choose "Outside Google Play" and enter the details yourself.'],422);
  out(['ok'=>true,'pkg'=>$pk,'title'=>$i['title'],'icon'=>$i['icon']]);}
 if($a==='dev_camp_save'){$cid=(int)($P['cid']??0);$type=in_array(($P['type']??''),['app','visit','channel','bot'],true)?(string)$P['type']:'app';$ti=mb_substr(trim((string)($P['title']??'')),0,120);$url=trim((string)($P['url']??''));$store=($type==='app'&&($P['store']??'')==='other')?'other':'play';
  $pkg=trim((string)($P['pkg']??''));$cost=round((float)($P['cost']??0),4);$wait=$type==='visit'?max(5,min(600,(int)($P['wait']??10))):0;$rep=$type==='visit'?max(0,min(43200,(int)($P['rep_h']??0)*60+(int)($P['rep_m']??0))):0;$dc=max(0,min(1000000,(int)($P['daily_cap']??0)));$tc=max(0,min(1000000,(int)($P['total_cap']??0)));
  $o=null;if($cid>0){$o=q("SELECT * FROM te_camps WHERE id=? AND tg_id=? AND status<>'Deleted'",[$cid,$id])->fetch();if(!$o)out(['error'=>'Campaign not found'],404);}
  $chIcon=null;
  if($type==='app'&&$store==='play'){$pk=play_pkg($url!==''?$url:$pkg);if($pk==='')out(['error'=>'Paste the Google Play link of your app (it contains id=com.company.app)'],422);$pkg=$pk;
   if(!preg_match('#^https://play\.google\.com/#i',$url))$url='https://play.google.com/store/apps/details?id='.$pk;}
  if($type==='app'&&$store==='play'&&mb_strlen($ti)<3){$i=play_info($pkg);if($i)$ti=mb_substr($i['title'],0,120);}
  if($type==='channel'){$un=tg_username($url);if($un==='')out(['error'=>'Enter the public @username of your channel or group, or its t.me link. Private invite links cannot be checked.'],422);$url='https://t.me/'.$un;$pkg='';}
  if($type==='bot'){if(!preg_match('#^https://t\.me/([A-Za-z][A-Za-z0-9_]{3,31})(/[A-Za-z0-9_]{1,40})?/?(\?[^\s]*)?$#',$url,$mm)||!preg_match('/bot$/i',$mm[1]))out(['error'=>'Enter the link of your bot (https://t.me/YourBot) or Mini App (https://t.me/YourBot/app). A bot name ends with "bot".'],422);$pkg='';}
  if($type==='channel'&&mb_strlen($ti)<3){$ti=$un;}
  if(mb_strlen($ti)<3)out(['error'=>'Enter a name (at least 3 characters)'],422);
  if(!preg_match('#^https://\S{4,480}$#i',$url))out(['error'=>'The link must start with https://'],422);
  if($pkg!==''&&!preg_match('/^[A-Za-z0-9._]{3,120}$/',$pkg))out(['error'=>'The package name looks wrong (example: com.company.app)'],422);
  if($rep>0&&$rep<(int)dset('dev_min_repeat'))out(['error'=>'The shortest repeat time is '.(int)dset('dev_min_repeat').' minutes (or 0 so a visitor earns only once)'],422);
  $min=camp_min_cost($type,$wait);if($cost<$min)out(['error'=>'The minimum price is '.rtrim(rtrim(number_format($min,4,'.',''),'0'),'.').' USDT'.($type==='visit'?' for a stay of '.$wait.' seconds':'')],422);
  if($cost>100)out(['error'=>'Price is too high'],422);
  $reward=camp_reward($cost);if($reward<=0)out(['error'=>'Campaigns are not available yet. The points rate is not configured.'],422);
  // icon: new upload, Google Play copy, or keep the current one
  $icon=$o?($o['icon']?:null):null;$ni=null;
  if($type==='app'||$type==='bot'){$du=(string)($P['icon_data']??'');
   if($du!==''&&preg_match('#^data:image/(png|jpeg|jpg|webp|gif);base64,([A-Za-z0-9+/=\s]+)$#',$du,$mm2)){$ni=camp_icon_save((string)base64_decode($mm2[2]));if(!$ni)out(['error'=>'The icon could not be read. Use a PNG or JPG image.'],422);}
   elseif($type==='app'&&$store==='play'&&(!$icon||($o&&($o['pkg']??'')!==$pkg))){$iu=(string)($P['icon_url']??'');if($iu===''){$i=play_info($pkg);$iu=$i['icon']??'';}if($iu!=='')$ni=play_icon_local($iu);}}
  elseif($type==='channel'){if(!$icon||($o&&$o['url']!==$url)){[$okc,$why,$ct,$ci]=chan_check($un);if($okc&&$ci){$ni=$ci;}if($okc&&mb_strlen(trim((string)($P['title']??'')))<3&&$ct!=='')$ti=mb_substr($ct,0,120);}}
  else $icon=null;
  if($ni){if($icon)camp_icon_del($icon);$icon=$ni;}
  $needV=in_array($type,['visit','channel','bot'],true);
  if($cid>0){
   $sens=$o['title']!==$ti||$o['url']!==$url||abs((float)$o['cost']-$cost)>0.00001||$o['type']!==$type||($o['icon']??'')!==($icon??'')||(int)$o['wait']!==$wait;
   $vs=(int)$o['vstat'];if(!$needV||$o['type']!==$type)$vs=0;elseif($type==='visit'&&strtolower((string)parse_url($o['url'],PHP_URL_HOST))!==strtolower((string)parse_url($url,PHP_URL_HOST)))$vs=0;elseif(in_array($type,['channel','bot'],true)&&strtolower($o['url'])!==strtolower($url))$vs=0;
   $st=$o['status'];
   if($needV&&!$vs)$st='Draft';
   elseif($sens||$st==='Draft')$st=(dset('dev_auto_approve')==='1'&&(float)$dv['balance']>=$cost&&$st!=='Rejected'&&$st!=='Pending')?'Active':'Pending';
   elseif($st==='Finished'&&($tc===0||$tc>(int)$o['total_cap']))$st='Paused';
   q('UPDATE te_camps SET type=?,store=?,title=?,url=?,pkg=?,icon=?,cost=?,reward=?,wait=?,repeat_m=?,daily_cap=?,total_cap=?,status=?,vstat=?,test=0,note=NULL WHERE id=?',[$type,$store,$ti,$url,$pkg?:null,$icon,$cost,$reward,$wait,$rep,$dc,$tc,$st,$vs,$cid]);}
  else{if((int)q("SELECT COUNT(*) FROM te_camps WHERE tg_id=? AND status<>'Deleted'",[$id])->fetchColumn()>=(int)dset('dev_max_camps'))out(['error'=>'You reached the campaign limit'],429);
   $st=$needV?'Draft':((dset('dev_auto_approve')==='1'&&(float)$dv['balance']>=$cost)?'Active':'Pending');
   q('INSERT INTO te_camps (tg_id,type,store,title,url,pkg,icon,cost,reward,wait,repeat_m,daily_cap,total_cap,ckey,pkey,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)',[$id,$type,$store,$ti,$url,$pkg?:null,$icon,$cost,$reward,$wait,$rep,$dc,$tc,bin2hex(random_bytes(12)),bin2hex(random_bytes(8)),$st]);$cid=(int)$pdo->lastInsertId();}
  camp_sync($cid);$ns=q('SELECT status FROM te_camps WHERE id=?',[$cid])->fetchColumn();
  if($ns==='Pending')notify_admin('notify_dev',"Campaign waiting for approval\nDeveloper: {$dv['company']} ($id)\nTitle: $ti\nType: ".camp_label($type).", price $cost USDT");
  out(['ok'=>true,'status'=>$ns,'id'=>$cid]);}
 if($a==='dev_verify'){$c=q("SELECT * FROM te_camps WHERE id=? AND tg_id=? AND type IN ('visit','channel','bot') AND status<>'Deleted'",[$tid,$id])->fetch();if(!$c)out(['error'=>'Campaign not found'],404);
  if((int)$c['vstat']===1)out(['ok'=>true,'verified'=>true,'status'=>$c['status']]);
  if($c['type']==='bot')out(['ok'=>true,'verified'=>false,'code'=>camp_vcode($tid)]);
  if($c['type']==='channel'){[$okc,$why]=chan_check(tg_username((string)$c['url']));if($okc){q('UPDATE te_camps SET vstat=1,v_at=NOW() WHERE id=?',[$tid]);out(['ok'=>true,'verified'=>true,'noadmin'=>$why==='noadmin']);}out(['ok'=>true,'verified'=>false,'why'=>$why,'bot'=>tg_bot_user()]);}
  if(!empty($P['poll']))out(['ok'=>true,'verified'=>false]);
  [$ok,$why]=camp_check_script($c);if($ok){q("UPDATE te_camps SET vstat=1,v_at=NOW() WHERE id=?",[$tid]);out(['ok'=>true,'verified'=>true]);}
  out(['ok'=>true,'verified'=>false,'why'=>$why,'code'=>camp_vcode($tid)]);}
 if($a==='dev_publish'){$c=q('SELECT * FROM te_camps WHERE id=? AND tg_id=?',[$tid,$id])->fetch();if(!$c)out(['error'=>'Campaign not found'],404);if($c['status']!=='Draft')out(['error'=>'This campaign is already published'],422);
  if(in_array($c['type'],['visit','channel','bot'],true)&&(int)$c['vstat']!==1)out(['error'=>$c['type']==='visit'?'First check that the tracking script is on your website, then publish.':($c['type']==='bot'?'First finish the setup test for your bot or Mini App, then publish.':(dset('dev_chan_need_admin')==='1'?'First add our bot as an administrator of your channel and press Check, then publish.':'First press Check my channel, then publish.'))],422);
  $st=(dset('dev_auto_approve')==='1'&&(float)$dv['balance']>=(float)$c['cost'])?'Active':'Pending';q('UPDATE te_camps SET status=?,note=NULL WHERE id=?',[$st,$tid]);camp_sync($tid);
  if($st==='Pending')notify_admin('notify_dev',"Campaign waiting for approval\nDeveloper: {$dv['company']} ($id)\nTitle: {$c['title']}\nType: {$c['type']}, price {$c['cost']} USDT");out(['ok'=>true,'status'=>$st]);}
 if($a==='dev_camp_toggle'){$c=q('SELECT * FROM te_camps WHERE id=? AND tg_id=?',[$tid,$id])->fetch();if(!$c)out(['error'=>'Campaign not found'],404);
  if($c['status']==='Active')q("UPDATE te_camps SET status='Paused',note=NULL WHERE id=?",[$tid]);
  elseif($c['status']==='Paused'){if((float)$dv['balance']<(float)$c['cost'])out(['error'=>'Add a deposit first. Your balance is lower than the price per action.'],422);q("UPDATE te_camps SET status='Active',note=NULL WHERE id=?",[$tid]);}
  else out(['error'=>'This campaign cannot be changed now ('.$c['status'].')'],422);
  camp_sync($tid);out(['ok'=>true]);}
 if($a==='dev_dep'){$amt=round((float)($P['amount']??0),4);$tx=trim((string)($P['txid']??''));$min=(float)dset('dev_min_dep');
  if(dset('dev_dep_addr')==='')out(['error'=>'Deposits are not open yet'],422);
  if($amt<$min)out(['error'=>'The minimum deposit is '.rtrim(rtrim(number_format($min,4,'.',''),'0'),'.').' USDT'],422);if($amt>1000000)out(['error'=>'Amount is too large'],422);
  if(!preg_match('/^[A-Za-z0-9_\-=+\/]{20,120}$/',$tx))out(['error'=>'Enter the transaction hash (TXID) of your payment'],422);
  if((int)q("SELECT COUNT(*) FROM te_dev_dep WHERE tg_id=? AND status='Pending'",[$id])->fetchColumn()>=5)out(['error'=>'You already have several deposits waiting for review'],429);
  try{q('INSERT INTO te_dev_dep (tg_id,amount,txid,note) VALUES (?,?,?,?)',[$id,$amt,$tx,mb_substr(trim((string)($P['note']??'')),0,190)]);}catch(Throwable $e){out(['error'=>'This transaction ID was already submitted'],422);}
  notify_admin('notify_dev',"New developer deposit to review\nDeveloper: {$dv['company']} ($id)\nAmount: $amt USDT\nTXID: $tx");out(['ok'=>true]);}
 if($a==='dev_dep_check'){if(dset('dev_auto_dep')!=='1'||dset('dev_dep_addr')==='')out(['error'=>'Automatic checking is not turned on. Submit your transaction ID instead.'],422);
  $last=(int)setting('dep_scan_at','0');if(time()-$last<8)out(['ok'=>true,'n'=>0,'wait'=>true]);q("INSERT INTO te_settings (k,v) VALUES ('dep_scan_at',?) ON DUPLICATE KEY UPDATE v=VALUES(v)",[(string)time()]);
  $r=dep_scan();if(!$r['ok'])out(['error'=>$r['error']],503);out(['ok'=>true,'n'=>$r['n']]);}
 if($a==='dev_dep_pts'){$i=dev_info();if(!$i['pts_on'])out(['error'=>'Paying with points is not available right now'],422);$pts=round((float)($P['points']??0),2);$usdt=pts_to_usdt($pts);
  if($i['pts_per_usdt']<=0)out(['error'=>'The points rate is not set yet'],422);if($usdt<$i['pts_min'])out(['error'=>'The minimum is '.rtrim(rtrim(number_format($i['pts_min'],4,'.',''),'0'),'.').' USDT ('.number_format(ceil($i['pts_min']*$i['pts_per_usdt']*100/max(1,$i['pts_rate']))).' points)'],422);
  if($i['pts_max']>0){$today=(float)q("SELECT COALESCE(SUM(amount),0) FROM te_dev_dep WHERE tg_id=? AND txid LIKE 'PTS-%' AND created_at>=CURDATE()",[$id])->fetchColumn();if($today+$usdt>$i['pts_max'])out(['error'=>'The daily limit for paying with points is '.rtrim(rtrim(number_format($i['pts_max'],4,'.',''),'0'),'.').' USDT'],422);}
  $pdo->beginTransaction();try{if(q('UPDATE te_users SET balance=balance-? WHERE tg_id=? AND balance>=?',[$pts,$id,$pts])->rowCount()!==1){$pdo->rollBack();out(['error'=>'You do not have enough points'],422);}
   q('UPDATE te_devs SET balance=balance+?,deposited=deposited+? WHERE tg_id=?',[$usdt,$usdt,$id]);q("INSERT INTO te_dev_dep (tg_id,amount,txid,note,status,admin_note,done_at) VALUES (?,?,?,?,'Approved',?,NOW())",[$id,$usdt,'PTS-'.time().'-'.bin2hex(random_bytes(3)),'Paid with points',number_format($pts).' points']);$pdo->commit();}
  catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();out(['error'=>'Could not complete. Please try again.'],500);}
  notify_admin('notify_dev',"Developer paid with points\nDeveloper: {$dv['company']} ($id)\nPoints: ".number_format($pts)." = $usdt USDT");out(['ok'=>true,'usdt'=>$usdt]);}
 if($a==='dev_flag'){$v=q("SELECT * FROM te_conv WHERE id=? AND dev_id=? AND status='OK'",[$tid,$id])->fetch();if(!$v)out(['error'=>'Not found'],404);
  if(strtotime((string)$v['created_at'])<time()-86400*(int)dset('dev_dispute_days'))out(['error'=>'The dispute period for this conversion is over'],422);
  q("UPDATE te_conv SET status='Flagged' WHERE id=? AND status='OK'",[$tid]);notify_admin('notify_dev',"Developer flagged a conversion #$tid as suspicious\nDeveloper: {$dv['company']} ($id)");out(['ok'=>true]);}
 out(['error'=>'Unknown action'],404);}
if($a==='app_proof'){$t=q("SELECT * FROM te_tasks WHERE id=? AND active=1 AND kind='app' AND verify='proof'",[$tid])->fetch();if(!$t)out(['error'=>'Task not found'],404);
 $l=q('SELECT * FROM te_log WHERE tg_id=? AND task_id=?',[$id,$tid])->fetch();if(!$l||$l['done']||(int)$l['started']<=0)out(['error'=>'Start the task first'],422);
 if(time()-(int)$l['started']<30)out(['error'=>'Please install and open the app first, then submit your proof'],429);
 $pf=mb_substr(trim((string)($P['proof']??'')),0,190);if(mb_strlen($pf)<3)out(['error'=>'Enter your username or ID shown inside the app'],422);
 if((q('SELECT status FROM te_apps WHERE tg_id=? AND task_id=?',[$id,$tid])->fetchColumn())==='Pending')out(['error'=>'Your proof is already under review'],422);
 q("INSERT INTO te_apps (tg_id,task_id,proof,status) VALUES (?,?,?,'Pending') ON DUPLICATE KEY UPDATE proof=VALUES(proof),status='Pending',created_at=NOW()",[$id,$tid,$pf]);
 notify_admin('notify_help',"App task proof to review\nUser: {$u['name']} ($id)\nTask: {$t['title']}\nProof: $pf");out(['ok'=>true]);}
function visit_why(int $tid,int $uid,int $cid): string{$r=q("SELECT res,at FROM te_cb WHERE qs LIKE ? AND at>=DATE_SUB(NOW(),INTERVAL 30 MINUTE) ORDER BY id DESC LIMIT 1",['TRACK a=visit&c='.$cid.'&uid='.$uid.'&%'])->fetch();
 if(!$r)return 'We did not receive any signal from the website yet. Open the page again with Start and stay on it until you see the green "Visit confirmed" message.';
 $m=['too early'=>'The page confirmed too early. Start again and stay for the full time.','invalid token'=>'The link is outdated. Press Start again and use the new page.','origin mismatch'=>'The page that sent the signal is not on the website set in the campaign.','visitor left too early'=>'You left the page too early. Start again and stay for the full time.','already counted'=>'This visit was already counted.'];
 return $m[$r['res']]??'The website sent a signal but it was not accepted ('.$r['res'].'). Start the task again.';}
if($a==='start'||$a==='claim'){
 $t=q('SELECT * FROM te_tasks WHERE id=? AND active=1',[$tid])->fetch(); if(!$t||((int)$t['test_only']&&!is_admin_tg($id))) out(['error'=>'Task not found'],404);
 $l=q('SELECT * FROM te_log WHERE tg_id=? AND task_id=?',[$id,$tid])->fetch();
 if($a==='start'){
  $tok=bin2hex(random_bytes(8));
  if($l&&(int)($l['fail_at']??0)>0&&!$l['done']&&(int)dset('dev_fail_wait')>0&&time()<(int)$l['fail_at']+(int)dset('dev_fail_wait')){$w=(int)$l['fail_at']+(int)dset('dev_fail_wait')-time();out(['error'=>"Please wait {$w}s before you try again.",'retry'=>$w],429);}
  if((int)$t['camp_id']>0&&in_array($t['kind'],['channel','bot','app'],true)&&$l&&$l['done'])out(['error'=>'You have already completed this task.'],429);
  if($t['kind']==='channel'&&(int)$t['camp_id']>0&&!is_admin_tg($id)){$m=tg('getChatMember',['chat_id'=>$t['chat'],'user_id'=>$id]);if($m&&in_array($m['status']??'',['member','administrator','creator','restricted'],true)&&($m['status']!=='restricted'||!empty($m['is_member'])))out(['error'=>'You are already a member, so this task cannot give you points.'],422);}
  if($t['kind']==='app'&&(q('SELECT status FROM te_apps WHERE tg_id=? AND task_id=?',[$id,$tid])->fetchColumn())==='Pending')out(['error'=>'Your proof is under review'],422);
  if(!$l) q('INSERT INTO te_log (tg_id,task_id,started,done,token) VALUES (?,?,?,0,?)',[$id,$tid,time(),$tok]);
  elseif($l['done']){$rh=(int)$t['repeat_m']; if($rh<=0||time()-(int)$l['done_at']<$rh*60) out(['error'=>'This task is not available yet'],429);
   q('UPDATE te_log SET done=0,started=?,away=0,fail_at=0,token=? WHERE tg_id=? AND task_id=?',[time(),$tok,$id,$tid]);}
  else q('UPDATE te_log SET started=?,away=0,fail_at=0,token=? WHERE tg_id=? AND task_id=?',[time(),$tok,$id,$tid]);
  out(['ok'=>true,'url'=>task_url($t,$id,$tok),'click'=>((int)$t['camp_id']>0&&$t['kind']==='app')?'EHT1:'.$id.'.'.$tok.'.'.(int)$t['camp_id']:'']);}
 if(!$l) out(['error'=>'Start the task first'],422);
 $fw=(int)dset('dev_fail_wait');$fail=function(string $msg)use($id,$tid,$fw){if($fw>0){q('UPDATE te_log SET started=0,away=0,fail_at=? WHERE tg_id=? AND task_id=? AND done=0',[time(),$id,$tid]);out(['error'=>$msg,'failed'=>true,'retry'=>$fw],422);}out(['error'=>$msg],422);};
 if($l['done']&&($t['verify']??'')==='conv'&&(int)$l['started']>0&&time()-(int)$l['done_at']<3600)out(['ok'=>true,'reward'=>(float)$t['reward'],'confirmed'=>true]);// already confirmed by the website or app: show success instead of an error
 if($l['done']) out(['error'=>'Task already completed'],422); if((int)$l['started']<=0) out(['error'=>'Start the task first'],422);
 if(($t['verify']??'')==='conv'&&$t['kind']==='link'&&(int)$t['wait']>0){$el=time()-(int)$l['started'];if($el<(int)$t['wait']){q('UPDATE te_log SET started=0,away=0 WHERE tg_id=? AND task_id=? AND done=0',[$id,$tid]);$fail("You came back after {$el}s. You must stay on the website for the full {$t['wait']} seconds to earn points. Please start the task again.");}}
 if(($t['verify']??'')==='conv'&&$t['kind']==='channel'){$m=tg('getChatMember',['chat_id'=>$t['chat'],'user_id'=>$id]);
  if(!$m&&dset('dev_chan_need_admin')!=='1'){$m=['status'=>'member'];}// bot is not an admin: joins cannot be verified (admin allowed this)
  if(!$m||!in_array($m['status']??'',['member','administrator','creator'],true))$fail('Join the channel first, then tap Check.');
  $cp=q('SELECT * FROM te_camps WHERE id=?',[(int)$t['camp_id']])->fetch();if(!$cp)out(['error'=>'Task not found'],404);
  $r=conv_register($cp,$id,'tg','',true);if(!$r['ok'])$fail(in_array($r['error'],['too early'],true)?'Please wait a few seconds after joining, then tap Check.':'Not counted: '.$r['error']);
  out(['ok'=>true,'reward'=>$r['reward']]);}
 if(($t['verify']??'')==='conv')$fail($t['kind']==='app'?'Not confirmed yet. Install and open the app, then check again in a minute.':($t['kind']==='bot'?'Not confirmed yet. Open the bot or app in Telegram, tap Start and use it for a moment, then check again.':visit_why($tid,$id,(int)$t['camp_id'])));
 if($t['kind']==='app')out(['error'=>$t['verify']==='proof'?'Submit your proof for review':'Not confirmed yet. Install and open the app, then check again in a few minutes.'],422);
 if($t['kind']==='link'&&time()-(int)$l['started']<(int)$t['wait']) out(['error'=>'Please wait a bit longer'],429);
 if($t['kind']==='link'&&(int)$l['away']>0&&(int)$l['away']<(int)$t['wait']-1) out(['error'=>'You left the website too early. Start the task again and stay for the full time.'],422);
 if($t['kind']==='channel'){$m=tg('getChatMember',['chat_id'=>$t['chat'],'user_id'=>$id]); if(!$m||!in_array($m['status']??'',['member','administrator','creator'],true)) out(['error'=>'Join the channel first'],422);}
 $pdo->beginTransaction();
 if(q('UPDATE te_log SET done=1,done_at=? WHERE tg_id=? AND task_id=? AND done=0',[time(),$id,$tid])->rowCount()!==1){$pdo->rollBack(); out(['error'=>'Already claimed'],422);}
 $rw=round((float)$t['reward']*max(0,(int)(lvl((int)$u['level'])['task_pct']??100))/100,2); credit($u,$rw); $pdo->commit(); out(['ok'=>true,'reward'=>$rw]);}
if($a==='withdraw'){
 if(!$u['email_ok']) out(['error'=>'Verify your Gmail in Profile first'],422);
 $x=wd_in($u,$P); if($x[0]) out(['error'=>$x[0]],422); [,$addr,$memo,$amt,$rate,$ctx]=$x;
 $err=code_check($id,'wd',$ctx,preg_replace('/\D/','',(string)($P['code']??''))); if($err) out(['error'=>$err],422);
 $pdo->beginTransaction(); $b=q('SELECT balance FROM te_users WHERE tg_id=? FOR UPDATE',[$id])->fetchColumn();
 if((float)$b<$amt){$pdo->rollBack(); out(['error'=>'Not enough balance'],422);}
 $ton=round($amt/$rate,6);
 q('UPDATE te_users SET balance=balance-?,ton_addr=?,ton_memo=? WHERE tg_id=?',[$amt,$addr,$memo,$id]);
 q("INSERT INTO te_wd (tg_id,name,method,number,memo,amount,ton) VALUES (?,?,'TON',?,?,?,?)",[$id,$u['name'],$addr,$memo,$amt,$ton]); $wid=$pdo->lastInsertId(); $pdo->commit();
 $rk=(int)q('SELECT COUNT(*)+1 FROM te_users WHERE banned=0 AND '.dsql().' AND total>?',[$u['total']])->fetchColumn();
 notify_admin('notify_wd',"New withdrawal request #$wid\nUser: {$u['name']} ($id)\nRank: #$rk | Level: {$u['level']}\nEmail: {$u['email']}\nAmount: ".number_format($amt)." pts = $ton USDT (TON network)\nAddress: $addr\nMemo: ".($memo?:'-')."\nTime: ".date('Y-m-d H:i'));
 out(['ok'=>true]);}
out(['error'=>'Bad request'],400);

<?php
$cf=__DIR__.'/config.php';$msg='';$done=false;$hook=false;$base='';
if(file_exists($cf)) exit('<p style="font:16px sans-serif;padding:30px">Already installed. Please delete install.php from the server.</p>');
function h($s){return htmlspecialchars((string)$s,ENT_QUOTES);}
$SQL=<<<'S'
CREATE TABLE IF NOT EXISTS te_users (tg_id BIGINT PRIMARY KEY, name VARCHAR(100), balance DECIMAL(14,2) NOT NULL DEFAULT 0, total DECIMAL(14,2) NOT NULL DEFAULT 0, ref_by BIGINT NULL, ref_earn DECIMAL(14,2) NOT NULL DEFAULT 0, banned TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY(ref_by));
CREATE TABLE IF NOT EXISTS te_tasks (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(120), kind VARCHAR(10), url VARCHAR(255), chat VARCHAR(100), reward INT, wait INT DEFAULT 10, active TINYINT(1) DEFAULT 1);
CREATE TABLE IF NOT EXISTS te_log (tg_id BIGINT, task_id INT, started INT, done TINYINT(1) DEFAULT 0, PRIMARY KEY(tg_id,task_id));
CREATE TABLE IF NOT EXISTS te_wd (id INT AUTO_INCREMENT PRIMARY KEY, tg_id BIGINT, name VARCHAR(100), method VARCHAR(20), number VARCHAR(100), amount DECIMAL(14,2), status VARCHAR(12) DEFAULT 'Pending', note VARCHAR(250), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE IF NOT EXISTS te_settings (k VARCHAR(40) PRIMARY KEY, v TEXT);
CREATE TABLE IF NOT EXISTS te_admins (username VARCHAR(50) PRIMARY KEY, pass_hash VARCHAR(255));
INSERT IGNORE INTO te_settings (k,v) VALUES ('min_withdraw','500'),('ref_percent','10'),('app_link',''),('announcement','')
S;
if($_SERVER['REQUEST_METHOD']==='POST'){
 $c=['h'=>trim($_POST['h']),'n'=>trim($_POST['n']),'u'=>trim($_POST['u']),'p'=>$_POST['p'],'t'=>trim($_POST['t'])];
 try{
  if(strlen($_POST['ap'])<8) throw new Exception('Admin password must be at least 8 characters');
  $db=new PDO("mysql:host={$c['h']};dbname={$c['n']};charset=utf8mb4",$c['u'],$c['p'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
  foreach(array_filter(array_map('trim',explode(';',$SQL))) as $s) $db->exec($s);
  $base=((!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')||($_SERVER['HTTP_X_FORWARDED_PROTO']??'')==='https'?'https':'http').'://'.$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['SCRIPT_NAME']),'/');
  $db->prepare('INSERT INTO te_admins (username,pass_hash) VALUES (?,?) ON DUPLICATE KEY UPDATE pass_hash=VALUES(pass_hash)')->execute([trim($_POST['au']),password_hash($_POST['ap'],PASSWORD_DEFAULT)]);
  $S=['site_name'=>trim($_POST['sn'])?:'TOmatocoiN','site_url'=>$base,'wh_secret'=>bin2hex(random_bytes(16))];
  foreach($S as $k=>$v) $db->prepare('INSERT INTO te_settings (k,v) VALUES (?,?) ON DUPLICATE KEY UPDATE v=VALUES(v)')->execute([$k,$v]);
  if(!@file_put_contents($cf,"<?php\nreturn ".var_export($c,true).";\n")) throw new Exception('Could not write config.php. Set the folder permission to 755 and try again');
  require __DIR__.'/inc/core.php'; $hook=(bool)tg('setWebhook',['url'=>$base.'/bot.php','secret_token'=>$S['wh_secret']]);
  $done=true;@unlink(__FILE__);
 }catch(Throwable $e){$msg=$e->getMessage();}
} ?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install</title>
<style>body{font:16px/1.5 system-ui,sans-serif;background:#F3F5F9;color:#0B1220;margin:0;padding:20px}form,.ok{max-width:440px;margin:auto;background:#fff;border:1px solid #E4E8F0;border-radius:16px;padding:22px;display:grid;gap:10px}
h1{font-size:22px;margin:0}h2{font-size:15px;margin:10px 0 0;color:#059669}label{font-weight:600;font-size:14px;display:grid;gap:4px}input{padding:11px;border:1px solid #E4E8F0;border-radius:10px;font:inherit;width:100%;box-sizing:border-box}
button,.b{background:#059669;color:#fff;border:0;border-radius:12px;padding:13px;font:600 16px system-ui;text-align:center;text-decoration:none;cursor:pointer}.err{background:#FEE2E2;color:#991B1B;padding:10px;border-radius:10px}code{background:#F1F5F9;padding:2px 6px;border-radius:6px;word-break:break-all}</style></head><body>
<?php if($done): ?><div class="ok"><h1>Installation complete</h1><p>Bot connection: <b><?=$hook?'connected':'failed (open Admin, Settings, then click Connect bot)'?></b></p>
<p><b>Last step:</b> in Telegram open @BotFather, send <code>/mybots</code>, choose your bot, then Bot Settings, Menu Button, Configure, and enter this URL:</p><p><code><?=h($base)?>/</code></p><a class="b" href="admin/">Open admin panel</a></div>
<?php else: ?><form method="post"><h1>Install</h1><?php if($msg) echo '<div class="err">'.h($msg).'</div>'; ?>
<h2>Site</h2><label>Site name<input name="sn" value="TOmatocoiN"></label>
<h2>Database (cPanel, MySQL Databases)</h2><label>Host<input name="h" value="localhost" required></label><label>Database name<input name="n" value="<?=h($_POST['n']??'')?>" required></label>
<label>Database user<input name="u" value="<?=h($_POST['u']??'')?>" required></label><label>Database password<input name="p" type="password" required></label>
<h2>Telegram bot</h2><label>Bot token (from BotFather)<input name="t" required></label>
<h2>Admin account (create a new one)</h2><label>Username<input name="au" required></label><label>Password (min 8 characters)<input name="ap" type="password" required></label>
<button>Install</button></form><?php endif ?></body></html>

<?php if(!file_exists(__DIR__.'/config.php')){header('Location: install.php');exit;} require __DIR__.'/inc/core.php'; $n=htmlspecialchars(setting('site_name','TOmatocoiN')); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#ffffff"><meta name="color-scheme" content="light only"><title><?=$n?></title>
<link rel="icon" href="<?=brand('favicon')?>"><link rel="apple-touch-icon" href="<?=brand('logo')?>"><link rel="stylesheet" href="<?=asset('assets/app.css')?>"><script src="https://telegram.org/js/telegram-web-app.js"></script>
<?php if(ad_on()||int_on()||agt_on()): ?><script src="https://sad.adsgram.ai/js/sad.min.js"></script><?php endif ?></head><body>
<header class="hdr"><div class="brand"><img src="<?=brand('logo')?>" alt=""><?=$n?></div>
<div class="hr"><button id="bell" class="bell" type="button" aria-label="Notifications"><svg viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/></svg><i id="bdot" hidden></i></button><div class="pill"><small>Balance</small><b id="bal2">–</b></div></div></header>
<main>
<section id="t-home" class="tab on"><div id="who" class="who"></div>
<div class="hsc" id="hscard" hidden><div><b>Add TOmatocoiN to your Home Screen</b><small>Open it like any other app, in one tap.</small></div><button class="btn sm" type="button" data-hs="add">Add</button><button class="x" type="button" data-hs="no" aria-label="Close">&times;</button></div>
<div class="hero"><small>Current balance</small><h1><span id="bal">–</span> <em>pts</em></h1><small id="est2" class="es"></small>
<div class="hs"><span><small>Total earned</small><b id="tot">–</b></span><span><small>Level</small><b id="lv2">–</b></span><span><small>Daily tasks</small><b id="dp2">–</b></span></div></div>
<div id="ann" class="annc" hidden><span class="annic"><svg viewBox="0 0 24 24"><path d="M3 11v2a1 1 0 0 0 1 1h3l5 4V6L7 10H4a1 1 0 0 0-1 1zM16 8a5 5 0 0 1 0 8"/></svg></span><div><small>Announcement</small><p id="annt"></p></div></div>
<div id="paidbox" class="paidbox" hidden></div>
<div id="bn" class="bonus"></div>
<div class="card"><div class="ch"><h2>Daily tasks</h2><span id="dp" class="mu"></span></div><i class="bar lg"><u id="dpb"></u></i>
<ul id="dt" class="list in"><li class="sk"></li><li class="sk"></li></ul><div id="agbox"></div></div>
<h2 class="sec">Tasks</h2><div id="tasks"><ul class="list"><li class="sk"></li><li class="sk"></li></ul></div></section>
<section id="t-wallet" class="tab"><div class="wcard"><small>Current balance</small><b><span id="bal3">–</span> pts</b><small id="est3"></small></div>
<div id="vwarn" class="warn" hidden><b>Verify your Gmail to withdraw.</b> Add and confirm your Gmail in your profile first. <button type="button" class="lnk" data-go="me">Open profile</button></div>
<form id="wd" class="panel"><h2>Withdraw in USDT</h2>
<div class="note"><b>Paid in USDT on the TON Network</b><ul><li>We send you <b>USDT</b> (not TON coin) on the <b>TON blockchain</b>.</li><li>Enter a <b>TON-network wallet address</b> (starts with <b>UQ</b> or <b>EQ</b>), for example from Tonkeeper or Telegram Wallet. Make sure your wallet can receive USDT on TON.</li><li>Do <b>not</b> use an Ethereum, Tron or BSC address. Payments are final and cannot be recovered if the address or network is wrong.</li><li>If you use an exchange wallet that needs a Memo/Tag, enter it below.</li></ul></div>
<label>TON network wallet address<input id="addr" name="address" placeholder="UQ…" autocomplete="off" required></label>
<label>Memo / tag (optional)<input id="memo" name="memo" maxlength="40" autocomplete="off"></label>
<label>Amount (points)<input id="amount" name="amount" inputmode="numeric" required></label>
<label>Confirmation code<div class="cr"><input id="code" name="code" inputmode="numeric" maxlength="6" placeholder="6-digit code" autocomplete="off" required><button type="button" class="btn gh" id="sendc">Send code</button></div><small class="hint spam">Code not in your Inbox? Check your <b>Spam / Junk</b> folder and mark it as "Not spam".</small></label>
<p class="hint"><span id="min"></span> <b id="est"></b></p><p class="hint" id="cmail"></p><button class="btn">Request withdrawal</button></form>
<h2 class="sec">Withdrawal history</h2><p class="hint" style="margin:-4px 0 8px">Problem with a withdrawal? <button class="lnk" id="hwd" type="button">Contact support</button></p><ul id="hist" class="list"></ul></section>
<section id="t-top" class="tab"><h2>Top 10 earners</h2><div id="podium"></div><ol id="board" class="board"></ol></section>
<section id="t-me" class="tab"><div class="pcard"><div class="avwrap"><span id="pav"></span><button id="edav" class="edit" type="button" aria-label="Change photo"><svg viewBox="0 0 24 24"><path d="M4 20h4L19 9l-4-4L4 16zM14 6l4 4"/></svg></button></div>
<h2 id="pn" class="pname"></h2><div id="ptier"></div><div id="pips" class="pips"></div><div id="vb"></div>
<div class="lvbox"><div class="lr"><b id="lvt"></b><span id="lvn" class="mu"></span></div><i class="bar lg"><u id="lvbar"></u></i><small class="hint" id="lt"></small><ul class="lb" id="lbn"></ul></div>

<div class="pst"><span><b id="s1">–</b><small>Total earned</small></span><span><b id="s2">–</b><small>Ads watched</small></span><span><b id="s3">–</b><small>Tasks done</small></span><span><b id="s4">–</b><small>Friends</small></span></div></div>
<div class="panel"><h2>Account verification</h2><p class="hint" id="vstat"></p>
<div id="vform" class="vf"><label>Gmail address<input id="mail" type="email" placeholder="name@gmail.com" autocomplete="off"></label><button class="btn gh" id="msend" type="button">Send code</button><p class="hint spam">Didn't get the code in your Inbox? Please check your <b>Spam / Junk</b> folder, and mark the email as "Not spam".</p>
<label>Verification code<input id="mcode" inputmode="numeric" maxlength="6" placeholder="6-digit code" autocomplete="off"></label><button class="btn" id="mver" type="button">Verify Gmail</button></div></div>
<div class="panel"><h2>Help Desk</h2><p class="hint">Problem with a withdrawal or anything else? Contact our support team.</p><button class="btn gh" id="hbtn" type="button">Open Help Desk <i id="hdot" class="hdot" hidden></i></button></div>
<div class="panel" id="hsp" hidden><h2>Home Screen shortcut</h2><p class="hint">Put TOmatocoiN on your phone's Home Screen and open it like any other app.</p><button class="btn gh" id="hsbtn" type="button" data-hs="add">Add to Home Screen</button></div>
<div class="panel" id="devp" hidden><h2>Developer Programme</h2><p class="hint">Promote your app or website to our members. Pay only for verified installs and visits.</p><button class="btn gh" id="devbtn" type="button">Open Developer Programme</button></div>
<h2 class="sec">Badges</h2><div id="badges" class="bgrid"></div>
<div class="panel"><h2>Invite friends</h2><p class="hint" id="rc"></p><input id="rl" readonly><button class="btn" id="cp" type="button">Copy link</button></div></section>
</main>
<div id="sheet" class="sheet" hidden><div class="shin"><div class="shh"><b>Choose profile photo</b><button id="shx" type="button" aria-label="Close">&times;</button></div><div id="agrid" class="agrid"></div>
<label class="btn gh wide up">Upload your own photo<input id="file" type="file" accept="image/*" hidden></label></div></div>
<div id="dvsheet" class="sheet full" hidden><div class="shin dvin"><div class="shh"><button id="dvx" class="dvback" type="button">‹ Back</button><b>Developer Programme</b><span></span></div><div id="dvb"><p class="hint">Loading…</p></div></div></div>
<div id="nsheet" class="sheet" hidden><div class="shin"><div class="shh"><b>Notifications</b><button id="nx" type="button" aria-label="Close">&times;</button></div><ul id="nlist" class="list"></ul></div></div>
<div id="hsheet" class="sheet" hidden><div class="shin"><div class="shh"><b id="htt">Help Desk</b><button id="hx" type="button" aria-label="Close">&times;</button></div>
<div id="hv1"><button class="btn wide" id="hnew" type="button">New ticket</button><ul id="hlist" class="list" style="margin-top:12px"></ul></div>
<form id="hv2" class="hf" hidden><label>What is the problem about?<select id="hreason" required></select></label><label id="hcl" hidden>Your reason<input id="hcustom" maxlength="100" placeholder="Write your reason"></label>
<label id="hwl" hidden>Related withdrawal (optional)<select id="hwdid"></select></label><p id="htip" class="note" hidden></p><label>Describe the problem<textarea id="hbody" maxlength="1500" placeholder="Tell us what happened…" required></textarea></label>
<div id="himg" hidden><label>Screenshot (optional)<input id="hfile" type="file" accept="image/*"></label><div class="shot" id="hprevw" hidden><img id="hprev" alt=""><button type="button" id="hrmv" aria-label="Remove">&times;</button></div></div>
<button class="btn">Send to support</button><button class="btn gh" id="hback" type="button">Back</button></form>
<div id="hv3" hidden><div id="hthr" class="thr"></div><div class="shot" id="hrprevw" hidden><img id="hrprev" alt=""><button type="button" id="hrrmv" aria-label="Remove">&times;</button></div><form id="hrep" class="rep"><label class="att" id="hratt" title="Add screenshot">&#128206;<input id="hrf" type="file" accept="image/*" hidden></label><input id="hrt" placeholder="Write a reply…" maxlength="1500" autocomplete="off"><button class="btn">Send</button></form><p id="hclosed" class="hint" hidden>This ticket is closed.</p><button class="btn gh wide" id="hback2" type="button" style="margin-top:10px">Back to tickets</button></div></div></div>
<nav><button class="on" data-t="home"><svg viewBox="0 0 24 24"><path d="M3 11l9-8 9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg>Home</button>
<button data-t="wallet"><svg viewBox="0 0 24 24"><path d="M3 7h18v12H3zM3 7l3-3h12l3 3M16 13h2"/></svg>Wallet</button>
<button data-t="top"><svg viewBox="0 0 24 24"><path d="M8 21h8M12 17v4M7 4h10v5a5 5 0 0 1-10 0zM17 5h3v2a3 3 0 0 1-3 3M7 5H4v2a3 3 0 0 0 3 3"/></svg>Leaders</button>
<button data-t="me"><svg viewBox="0 0 24 24"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0z"/></svg>Profile</button></nav><div id="toast"></div><script src="<?=asset('assets/app.js')?>"></script><script src="<?=asset('assets/dev.js')?>"></script></body></html>

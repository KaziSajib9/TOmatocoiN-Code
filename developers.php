<?php require __DIR__.'/inc/core.php';
$N=setting('site_name','TOmatocoiN');$S=rtrim(setting('site_url'),'/');if($S==='')$S='https://'.preg_replace('/[^A-Za-z0-9.\-:]/','',(string)($_SERVER['HTTP_HOST']??'your-site.com'));
$e=function($s){return htmlspecialchars((string)$s,ENT_QUOTES);};$app=!empty($_GET['app']);
$min=['app'=>dset('dev_min_app'),'visit'=>dset('dev_min_visit')];$share=dset('dev_share');
?><?php $gr=trim((string)setting('dev_sdk_repo',''));$gt=trim((string)setting('dev_sdk_tag',''));if(!preg_match('~^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$~',$gr))$gr='';if(!preg_match('~^[A-Za-z0-9_.-]{1,30}$~',$gt))$gt='v1.0.0';$grh=htmlspecialchars($gr);$gth=htmlspecialchars($gt);$gro=$gr?htmlspecialchars(str_replace('/','.',$gr)):'';?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover"><meta name="theme-color" content="#059669"><title>Developer Guide · <?=$e($N)?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#F6F8FB;--card:#fff;--ink:#0F172A;--mu:#64748B;--ln:#E2E8F0;--br:#059669;--br2:#047857;--soft:#ECFDF5;--code:#0F172A;--codet:#E2E8F0;--warn:#FFFBEB;--warnb:#FDE68A}
@media(prefers-color-scheme:dark){:root{--bg:#0B1220;--card:#111A2E;--ink:#E5E7EB;--mu:#94A3B8;--ln:#22304A;--soft:#0D2A22;--code:#050A14;--warn:#2A2208;--warnb:#5B4A0E}}
*{box-sizing:border-box}html{scroll-behavior:smooth;scroll-padding-top:70px}body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.65 Inter,system-ui,-apple-system,sans-serif;-webkit-text-size-adjust:100%}
.wrap{max-width:760px;margin:0 auto;padding:0 16px 60px}
.hero{background:linear-gradient(135deg,#065F46,#059669 60%,#10B981);color:#fff;padding:<?=$app?'28px':'44px'?> 16px 34px;text-align:center}.hero h1{margin:0 0 8px;font-size:28px;line-height:1.2;font-weight:800}.hero p{margin:0 auto;max-width:520px;opacity:.95}
.nav{position:sticky;top:0;z-index:5;background:var(--card);border-bottom:1px solid var(--ln);display:flex;gap:6px;overflow:auto;padding:10px 16px;scrollbar-width:none}.nav::-webkit-scrollbar{display:none}
.nav a{white-space:nowrap;text-decoration:none;color:var(--ink);font-size:13px;font-weight:600;padding:6px 12px;border-radius:99px;background:var(--bg);border:1px solid var(--ln)}
h2{font-size:22px;margin:38px 0 6px;font-weight:800}h2 .n{display:inline-grid;place-items:center;width:30px;height:30px;border-radius:50%;background:var(--br);color:#fff;font-size:15px;margin-right:8px;vertical-align:2px}
h3{font-size:16px;margin:22px 0 6px}p{margin:8px 0}.mu{color:var(--mu)}.lead{font-size:17px;color:var(--mu)}
.card{background:var(--card);border:1px solid var(--ln);border-radius:18px;padding:16px;margin:12px 0}.grid3{display:grid;gap:10px;grid-template-columns:repeat(auto-fit,minmax(200px,1fr))}
.step{display:flex;gap:12px;align-items:flex-start}.step i{flex:none;width:34px;height:34px;border-radius:50%;background:var(--soft);color:var(--br);display:grid;place-items:center;font:700 16px Inter,sans-serif;font-style:normal}.step b{display:block}
ol.s{list-style:none;counter-reset:s;padding:0;margin:10px 0}ol.s>li{counter-increment:s;position:relative;padding:0 0 14px 44px}ol.s>li:before{content:counter(s);position:absolute;left:0;top:0;width:32px;height:32px;border-radius:50%;background:var(--br);color:#fff;font-weight:700;display:grid;place-items:center}
ol.s>li b:first-child{display:block}
.tip,.warn{border-radius:14px;padding:12px 14px;margin:12px 0;font-size:15px}.tip{background:var(--soft);border:1px solid #A7F3D0}.warn{background:var(--warn);border:1px solid var(--warnb)}
.tabs{display:flex;gap:6px;flex-wrap:wrap;margin:12px 0 0}.tabs button{border:1px solid var(--ln);background:var(--card);color:var(--ink);border-radius:99px;padding:8px 16px;font:600 14px Inter,sans-serif;cursor:pointer}.tabs button.on{background:var(--br);border-color:var(--br);color:#fff}
.pane{display:none}.pane.on{display:block}
.code{position:relative;margin:10px 0}.code pre{margin:0;background:var(--code);color:var(--codet);border-radius:14px;padding:14px;overflow:auto;font:13px/1.55 ui-monospace,SFMono-Regular,Menlo,monospace}
.code button{position:absolute;top:8px;right:8px;border:0;background:#334155;color:#fff;border-radius:8px;padding:5px 10px;font:600 12px Inter,sans-serif;cursor:pointer}.code button.ok{background:var(--br)}
.btn{display:inline-block;background:var(--br);color:#fff!important;text-decoration:none;border-radius:12px;padding:10px 16px;font-weight:600;font-size:15px;margin:4px 6px 4px 0}.btn.gh{background:var(--card);color:var(--br)!important;border:1px solid var(--br)}
code.i{background:var(--soft);color:var(--br2);padding:2px 6px;border-radius:6px;font-size:.9em}
details{background:var(--card);border:1px solid var(--ln);border-radius:14px;padding:0 14px;margin:8px 0}summary{cursor:pointer;font-weight:600;padding:12px 0;list-style:none}summary::-webkit-details-marker{display:none}summary:after{content:'+';float:right;color:var(--br);font-weight:800}details[open] summary:after{content:'–'}details>div{padding:0 0 12px;color:var(--mu)}
table{width:100%;border-collapse:collapse;font-size:14px}td,th{padding:8px;border-bottom:1px solid var(--ln);text-align:left;vertical-align:top}th{color:var(--mu);font-weight:600}
.flow{display:grid;gap:8px;margin:12px 0}.flow div{background:var(--card);border:1px solid var(--ln);border-radius:14px;padding:12px 14px}.flow div+div:before{content:'↓';display:block;text-align:center;color:var(--br);font-weight:800;margin:-14px 0 6px}
footer{text-align:center;color:var(--mu);font-size:13px;margin-top:40px}
</style></head><body>
<div class="hero"><h1>Get real installs and visits<br>with <?=$e($N)?></h1><p>Pay only for results. Members of our app complete your task, and you pay only when it is confirmed.</p></div>
<nav class="nav"><a href="#how">How it works</a><a href="#start">Quick start</a><a href="#code">Add the code</a><a href="#test">Test it</a><a href="#faq">Problems?</a><a href="#adv">Advanced</a></nav>
<div class="wrap">

<h2 id="how">How it works</h2>
<p class="lead">You choose what you want: an <b>app install</b> or a <b>website visit</b>. Our members see it as a task and earn points when they finish it.</p>
<div class="grid3">
<div class="card step"><i>1</i><div><b>You add funds</b><span class="mu">Send USDT to our wallet. Your balance is shown in the app.</span></div></div>
<div class="card step"><i>2</i><div><b>You create a campaign</b><span class="mu">Set your link and the price you pay for each result.</span></div></div>
<div class="card step"><i>3</i><div><b>You add one line of code</b><span class="mu">It tells us when a member really installed or visited.</span></div></div>
</div>
<div class="flow">
<div><b>Member taps Start</b><br><span class="mu">We create a one-time code for that member and open your link.</span></div>
<div><b>Member installs your app or visits your page</b><br><span class="mu">Your code sends the one-time code back to us.</span></div>
<div><b>We confirm</b><br><span class="mu">Your balance is charged, and the member gets points.</span></div>
</div>
<div class="tip"><b>You only pay for confirmed results.</b> Each member can be counted only once per campaign.</div>

<h2 id="start">Quick start</h2>
<ol class="s">
<li><b>Join the programme</b>Open the <?=$e($N)?> app, go to <b>Profile → Developer Programme</b> and tap <b>Join</b>. Your Gmail must be verified.</li>
<li><b>Add funds</b>Open the <b>Deposit</b> tab. Send USDT on the <b>TON network</b> to the address shown there. When automatic checking is on, write your <b>payment code</b> (shown in the Deposit tab, like TC123456789) in the transfer comment, then tap <b>I have paid, check now</b>. Your balance is added within a minute or two. Otherwise type the amount and the transaction ID (TXID) and tap <b>I have paid</b>, and we check it by hand. You can also pay with the points you earned as a member.</li>
<li><b>Create a campaign</b>Open <b>Campaigns → New campaign</b>. Choose <b>App installs</b> or <b>Website visits</b>.<br>&bull; <b>Google Play app:</b> paste the Play link and tap <b>Get name and icon from Google Play</b>. The name and icon fill in by themselves.<br>&bull; <b>Outside Google Play</b> (APK, App Store, other store): type the name, upload an icon and paste your download link.<br>&bull; <b>Website:</b> paste your page link. After saving, add one line of script to the page and press <b>Check my website</b>. You can publish only when the check passes.<br>For website visits you also choose how many seconds a visitor must stay and how long before the same visitor can earn again. A longer stay costs proportionally more, and visitors see a countdown on your page.<br>Then set your price. The form shows how many points the member gets. We review the campaign before it goes live.</li>
<li><b>Add the code</b>Open the <b>Integration</b> tab. It shows your campaign number and keys with ready-to-copy code. Follow the next section.</li>
<li><b>Watch your results</b>Open <b>Reports</b> to see installs and visits every day.</li>
</ol>
<div class="card"><b>Prices</b><p class="mu" style="margin:4px 0 0">Minimum price: <b><?=$e($min['app'])?> USDT</b> per app install and <b><?=$e($min['visit'])?> USDT</b> per website visit. Members receive <b><?=$e($share)?>%</b> of your price as points. A campaign pauses by itself when your balance is lower than one result. Add funds and tap <b>Resume</b>.</p></div>

<h2 id="code">Add the code</h2>
<p class="lead">Pick what you have. Replace nothing by hand: copy the ready code from the <b>Integration</b> tab in the app.</p>
<div class="tabs" id="tabs"><button data-p="kt" class="on">Android Kotlin</button><button data-p="jv">Android Java</button><button data-p="fl">Flutter</button><button data-p="web">Website</button></div>

<div class="pane on" id="p-kt"><div class="card">
<ol class="s">
<?php if($gr): ?><li><b>Option A. From GitHub, no download</b>In <code class="i">settings.gradle</code> add JitPack:<div class="code"><pre>dependencyResolutionManagement {
    repositories {
        google()
        mavenCentral()
        maven { url 'https://jitpack.io' }
    }
}</pre></div>In <code class="i">app/build.gradle</code>:<div class="code"><pre>dependencies {
    implementation 'com.github.<?=$gro?>:tomatocoin-sdk:<?=$gth?>'
}</pre></div>Then press <b>Sync</b>. The source is on <a href="https://github.com/<?=$grh?>" target="_blank" rel="noopener">github.com/<?=$grh?></a>.</li><?php endif ?>
<li><b><?=$gr?'Option B. ':''?>Download the library</b><a class="btn" href="sdk.php?p=android">Download Android SDK</a>Unzip it and copy the folder <code class="i">tomatocoin-sdk</code> into your Android project.</li>
<li><b>Add it to your project</b>In <code class="i">settings.gradle</code>:
<div class="code"><pre>include ':tomatocoin-sdk'</pre></div>In <code class="i">app/build.gradle</code>:
<div class="code"><pre>dependencies {
    implementation project(':tomatocoin-sdk')
}</pre></div></li>
<li><b>Start it when your app opens</b>Put this in your first screen (launcher Activity):
<div class="code"><pre>class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        TomatoCoin.init(this, "<?=$e($S)?>", CAMPAIGN_ID, "PUBLIC_KEY")
    }
}</pre></div>Use your own <b>CAMPAIGN_ID</b> and <b>PUBLIC_KEY</b> from the Integration tab. Do not forget <code class="i">import com.tomatocoin.sdk.TomatoCoin</code>.</li>
<li><b>Done</b>The first time a member opens your app after installing from your campaign, the library confirms it. It does nothing else.</li>
</ol></div></div>

<div class="pane" id="p-jv"><div class="card">
<ol class="s">
<?php if($gr): ?><li><b>Option A. From GitHub, no download</b>In <code class="i">settings.gradle</code> add JitPack:<div class="code"><pre>dependencyResolutionManagement {
    repositories {
        google()
        mavenCentral()
        maven { url 'https://jitpack.io' }
    }
}</pre></div>In <code class="i">app/build.gradle</code>:<div class="code"><pre>dependencies {
    implementation 'com.github.<?=$gro?>:tomatocoin-sdk:<?=$gth?>'
}</pre></div>Then press <b>Sync</b>. The source is on <a href="https://github.com/<?=$grh?>" target="_blank" rel="noopener">github.com/<?=$grh?></a>.</li><?php endif ?>
<li><b><?=$gr?'Option B. ':''?>Download the library</b><a class="btn" href="sdk.php?p=android">Download Android SDK</a>Unzip it and copy the folder <code class="i">tomatocoin-sdk</code> into your Android project.</li>
<li><b>Add it to your project</b>In <code class="i">settings.gradle</code>:
<div class="code"><pre>include ':tomatocoin-sdk'</pre></div>In <code class="i">app/build.gradle</code>:
<div class="code"><pre>dependencies {
    implementation project(':tomatocoin-sdk')
}</pre></div></li>
<li><b>Start it when your app opens</b>
<div class="code"><pre>public class MainActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        TomatoCoin.init(this, "<?=$e($S)?>", CAMPAIGN_ID, "PUBLIC_KEY");
    }
}</pre></div>Use your own <b>CAMPAIGN_ID</b> and <b>PUBLIC_KEY</b> from the Integration tab. Do not forget <code class="i">import com.tomatocoin.sdk.TomatoCoin;</code>.</li>
<li><b>Done</b>The first time a member opens your app after installing from your campaign, the library confirms it.</li>
</ol></div></div>

<div class="pane" id="p-fl"><div class="card">
<ol class="s">
<?php if($gr): ?><li><b>Option A. From GitHub, no download</b>In <code class="i">pubspec.yaml</code>:<div class="code"><pre>dependencies:
  tomatocoin_sdk:
    git:
      url: https://github.com/<?=$grh?>.git
      path: flutter/tomatocoin_sdk
      ref: <?=$gth?></pre></div>Then run <code class="i">flutter pub get</code>.</li><?php endif ?>
<li><b><?=$gr?'Option B. ':''?>Download the plugin</b><a class="btn" href="sdk.php?p=flutter">Download Flutter plugin</a>Unzip it next to your Flutter project.</li>
<li><b>Add it to <code class="i">pubspec.yaml</code></b>
<div class="code"><pre>dependencies:
  tomatocoin_sdk:
    path: ../tomatocoin-flutter-sdk/tomatocoin_sdk</pre></div></li>
<li><b>Start it when your app opens</b>For example in the first screen:
<div class="code"><pre>import 'package:tomatocoin_sdk/tomatocoin_sdk.dart';

@override
void initState() {
  super.initState();
  TomatoCoin.init(
    site: '<?=$e($S)?>',
    campaignId: CAMPAIGN_ID,
    publicKey: 'PUBLIC_KEY',
  );
}</pre></div></li>
<li><b>Done</b>It works on Android. On other systems it does nothing.</li>
</ol></div></div>

<div class="pane" id="p-web"><div class="card">
<ol class="s">
<li><b>Choose the page members land on</b>This is the link you put in the campaign.</li>
<li><b>Paste one line in that page</b>Put it just before <code class="i">&lt;/body&gt;</code>:
<div class="code"><pre>&lt;script src="<?=$e($S)?>/track.js?v=5" data-campaign="CAMPAIGN_ID" async&gt;&lt;/script&gt;</pre></div></li>
<li><b>Check it in the app</b>Open <b>Campaigns</b>, find your campaign and tap <b>Check my website</b>. We open your page and look for the line. If your site adds scripts with JavaScript and we cannot see it, open the <b>test link</b> shown in the app in your browser, wait for the green message, then tap <b>I opened it, check again</b>. When it shows a green tick, tap <b>Publish campaign</b>. You cannot publish before the check passes.</li>
<li><b>Done</b>The script waits until the member has really stayed on your page for the time you set. Then it confirms the visit. It uses no cookies and collects no personal data.</li>
</ol>
<div class="tip">Your link and your page must be on the same website address. Confirmations from other websites are refused.</div></div></div>

<div class="card"><b>Apps that are not on Google Play?</b><p class="mu" style="margin:4px 0 0">It still works. When a member taps Start, we copy a short code to the clipboard. The library reads it the first time your app opens. For this the member should open your app right after installing it.</p></div>
<div class="card"><b>Privacy</b><p class="mu" style="margin:4px 0 0">The library sends only the campaign number, your public key, the member number and the one-time code from the link. It reads no contacts, files, location or advertising ID. Please mention in your privacy policy that you confirm installs with <?=$e($N)?>.</p></div>

<h2 id="tgc">Telegram Channel and Bot campaigns</h2>
<div class="card"><p><b>Channel:</b> add our bot as an administrator of your public channel, then choose "Telegram Channel" when creating the campaign and tap "Check my channel". Members who join are rewarded automatically after we confirm they are in the channel. People who are already members cannot earn from it. Each member can earn from your channel only once, even if they leave and join again. Private channels are not supported.</p>
<p><b>Bot / Mini App:</b> choose "Telegram Bot / Mini App" and paste your t.me link. When a member opens it from our site, your Mini App or bot must confirm the start by calling our server with the one-time code we pass in the start parameter. Ready-to-copy code is shown inside your dashboard. Before you can publish, open the test link once to prove the connection works, the same way as for a website.</p></div>
<h2 id="test">Test it</h2>
<ol class="s">
<li><b>Make a small test</b>Create a campaign and add a small deposit.</li>
<li><b>Ask us to test it first (optional)</b>Our team can run your campaign in test mode before approving it. In test mode nothing is charged from your balance, and we confirm that the install or visit is counted and the member receives points.</li>
<li><b>Use a different Telegram account</b>You cannot earn from your own campaign, so use another account to test.</li>
<li><b>Open the task</b>In the <?=$e($N)?> app, find your campaign in <b>Tasks</b>, tap <b>Start</b>, then install your app or open your page.</li>
<li><b>Come back and tap Check</b>The member sees the points. You see the result in <b>Reports</b>.</li>
</ol>

<h2 id="faq">Something is not working?</h2>
<details><summary>The install is never confirmed</summary><div>Check that the code runs when the app opens, that the campaign number and public key are the ones from the Integration tab, and that the member installed from the link in the task (not from a search). On Google Play the member must install from the task link so the install carries our code.</div></details>
<details><summary>It works with Google Play but not with my APK</summary><div>For APK files the code is read only the first time the app opens, so ask members to open it right after installing. Make sure you pass the Activity (not only the Application) to <code class="i">TomatoCoin.init</code>.</div></details>
<details><summary>The visit is not confirmed</summary><div>Make sure the script is on the landing page, the page stays open and visible for the full time, and the page is on the same website address as the campaign link.</div></details>
<details><summary>My campaign says "Paused"</summary><div>Your balance is lower than the price of one result. Add funds on the Deposit tab, then tap <b>Resume</b>.</div></details>
<details><summary>Members cannot see my task</summary><div>It may still be waiting for our review, paused, or it reached its daily or total limit.</div></details>
<details><summary>I think a result is fake</summary><div>In <b>Reports</b>, tap <b>Report</b> next to it. We review it, and if it is not valid, your money is returned.</div></details>

<h2>Rules</h2>
<div class="warn"><ul style="margin:0;padding-left:20px"><li>Promote only apps and websites you own or are allowed to promote.</li><li>Nothing illegal, adult, deceptive or harmful.</li><li>No fake or automated traffic. Accounts that do this are suspended.</li><li>Some app stores limit incentivised installs. Please check the rules of your store. You are responsible for following them.</li><li>We review campaigns and may pause or stop them at any time.</li></ul></div>

<h2 id="adv">Advanced: confirm from your server</h2>
<p class="mu">This is the safest way. Your server confirms the result itself, using your <b>secret key</b> (never put it in an app or web page).</p>
<div class="code"><pre><?=$e($S)?>/conv.php?c=CAMPAIGN_ID&amp;key=SECRET_KEY&amp;uid=MEMBER_ID&amp;t=TOKEN</pre></div>
<p class="mu"><code class="i">uid</code> and <code class="i">t</code> are the <code class="i">eh_uid</code> and <code class="i">eh_t</code> values that arrive in the link. <code class="i">t</code> is optional here.</p>
<div class="tabs" id="tabs2"><button data-p="curl" class="on">curl</button><button data-p="php">PHP</button><button data-p="node">Node.js</button></div>
<div class="pane on" id="q-curl"><div class="code"><pre>curl "<?=$e($S)?>/conv.php?c=12&amp;key=YOUR_SECRET&amp;uid=123456&amp;t=0123456789abcdef"</pre></div></div>
<div class="pane" id="q-php"><div class="code"><pre>$url = '<?=$e($S)?>/conv.php?' . http_build_query([
  'c' => 12, 'key' => $secret,
  'uid' => $_GET['eh_uid'], 't' => $_GET['eh_t'],
]);
$result = json_decode(file_get_contents($url), true);</pre></div></div>
<div class="pane" id="q-node"><div class="code"><pre>const res = await fetch('<?=$e($S)?>/conv.php?' + new URLSearchParams({
  c: 12, key: secret, uid, t
}));
console.log(await res.json());</pre></div></div>
<p class="mu">The answer is JSON: <code class="i">{"ok":true}</code> or <code class="i">{"ok":false,"error":"..."}</code>.</p>
<table><tr><th>Error</th><th>What it means</th></tr>
<tr><td>invalid campaign or key</td><td>Wrong campaign number or key</td></tr>
<tr><td>campaign not active</td><td>Paused, finished, waiting for review or out of funds</td></tr>
<tr><td>user has not started this task</td><td>The member did not tap Start in the app</td></tr>
<tr><td>too early</td><td>Not enough time passed since Start. Try again later</td></tr>
<tr><td>already counted</td><td>This member was already counted</td></tr>
<tr><td>daily limit reached</td><td>Your daily limit is full</td></tr>
<tr><td>advertiser balance too low</td><td>Add funds</td></tr></table>
<div class="tip" style="margin-top:14px"><b>Good to know:</b> a key inside an app or web page can be copied by a determined person. The one-time code, minimum time and website checks make this hard, and you can always <b>Report</b> a result. For the highest safety, confirm from your server.</div>

<footer><?=$e($N)?> Developer Programme</footer>
</div>
<script>
function tabs(id,pre){var t=document.getElementById(id);t.addEventListener('click',function(e){var b=e.target.closest('button');if(!b)return;t.querySelectorAll('button').forEach(function(x){x.classList.toggle('on',x===b)});
 document.querySelectorAll('[id^="'+pre+'"]').forEach(function(p){p.classList.toggle('on',p.id===pre+b.dataset.p)})})}
tabs('tabs','p-');tabs('tabs2','q-');
document.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener('click',function(e){var t=document.getElementById(a.getAttribute('href').slice(1));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth'})}})});
document.querySelectorAll('.code').forEach(function(c){var b=document.createElement('button');b.textContent='Copy';b.onclick=function(){var t=c.querySelector('pre').innerText;
 (navigator.clipboard?navigator.clipboard.writeText(t):Promise.reject()).then(function(){b.textContent='Copied';b.className='ok';setTimeout(function(){b.textContent='Copy';b.className=''},1500)}).catch(function(){var r=document.createRange();r.selectNodeContents(c.querySelector('pre'));var s=getSelection();s.removeAllRanges();s.addRange(r);b.textContent='Selected'})};c.appendChild(b)});
</script></body></html>

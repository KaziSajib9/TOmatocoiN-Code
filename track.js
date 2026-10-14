/*! TOmatocoiN visit tracking script. Add to the landing page of your visit campaign:
    <script src="https://YOUR-DOMAIN/track.js" data-campaign="12" async></script>
   The user is sent to your page with ?eh_uid=...&eh_t=...&eh_c=... . This script keeps those values for the visit (they survive page navigation),
   counts the time the page is actually visible, and confirms the visit once the required time has passed. No cookies, no personal data. */
(function(){
  var V='5';window.TOMATO_TRACK_VERSION=V;
  var s=document.currentScript||document.querySelector('script[data-campaign]');if(!s)return;
  var base=s.src.replace(/\/track\.js.*$/,'');var cid=s.getAttribute('data-campaign');if(!cid)return;
  var q=new URLSearchParams(location.search),K='eh_'+cid,st=null;
  // Setup check: the developer opens the page with ?eh_check=CODE and the script confirms itself to the server.
  if(q.get('eh_check')){try{fetch(base+'/track.php?a=ping&c='+encodeURIComponent(cid)+'&code='+encodeURIComponent(q.get('eh_check'))).then(function(r){return r.json()}).then(function(j){
    var d=document.createElement('div');d.style.cssText='position:fixed;left:12px;right:12px;bottom:12px;z-index:2147483647;padding:12px 14px;border-radius:12px;font:600 14px/1.4 system-ui,sans-serif;color:#fff;text-align:center;background:'+(j&&j.verified?'#16A34A':'#DC2626');
    d.textContent=j&&j.verified?'TOmatocoiN script v'+V+' is working. Go back and press Publish.':'TOmatocoiN: '+((j&&j.error)||'check failed');document.body.appendChild(d);setTimeout(function(){d.remove()},8000)})}catch(e){}return}
  try{st=JSON.parse(sessionStorage.getItem(K)||'null')}catch(e){}
  if(q.get('eh_uid')&&q.get('eh_t')&&String(q.get('eh_c')||cid)===String(cid)){st={u:q.get('eh_uid'),t:q.get('eh_t'),v:0,d:0};}
  if(!st||st.d)return;
  var save=function(){try{sessionStorage.setItem(K,JSON.stringify(st))}catch(e){}};save();
  var need=parseInt(s.getAttribute('data-wait')||'0',10),box=null;
  function fmt(n){n=Math.max(0,Math.ceil(n));return n>=60?Math.floor(n/60)+':'+('0'+(n%60)).slice(-2)+' min':n+' s'}
  function ui(txt,ok,err){if(s.getAttribute('data-timer')==='off')return;if(!box){box=document.createElement('div');box.setAttribute('role','status');
    box.style.cssText='position:fixed;left:50%;transform:translateX(-50%);top:12px;z-index:2147483647;max-width:92vw;padding:10px 16px;border-radius:999px;font:600 14px/1.3 system-ui,-apple-system,sans-serif;color:#fff;box-shadow:0 6px 20px rgba(0,0,0,.25);text-align:center';if(document.body)document.body.appendChild(box);else document.addEventListener('DOMContentLoaded',function(){if(box)document.body.appendChild(box)})}
    box.style.background=err?'#DC2626':ok?'#16A34A':'#0F172A';box.textContent=txt}
  function send(tries){if(st.d)return;tries=tries||0;st.d=1;save();
    var u=base+'/track.php?a=visit&c='+encodeURIComponent(cid)+'&uid='+encodeURIComponent(st.u)+'&t='+encodeURIComponent(st.t);
    function ok(){ui('\u2713 Visit confirmed. You can go back to the app now.',true);setTimeout(function(){if(box)box.remove()},9000)}
    function bad(m){ui('Could not confirm the visit ('+m+'). Go back to the app and start the task again.',false,true)}
    function req(m){return fetch(u,{method:m,keepalive:true,cache:'no-store'}).then(function(r){return r.text().then(function(t){try{return JSON.parse(t)}catch(e){return {ok:false,error:'bad reply ('+r.status+')',raw:1}}})})}
    function done(j){if(j&&j.ok)return ok();var e=(j&&j.error)||'error';if(e==='too early'&&tries<3){st.d=0;save();return setTimeout(function(){send(tries+1)},2500)}bad(e)}
    try{req('POST').then(function(j){if(j&&j.raw)return req('GET').then(done);done(j)}).catch(function(){
      req('GET').then(done).catch(function(){try{if(navigator.sendBeacon&&navigator.sendBeacon(u))return ok()}catch(e){}bad('network')})})}catch(e){try{var i=new Image();i.src=u;ok()}catch(x){}}}
  function run(){var last=Date.now();
    var iv=setInterval(function(){var n=Date.now();if(!document.hidden){st.v+=(n-last)/1000;save();if(st.v>=need)send();else ui('Stay on this page for '+fmt(need-st.v)+' to earn your points',false)}last=n;if(st.d)clearInterval(iv)},1000);ui('Stay on this page for '+fmt(need-st.v)+' to earn your points',false)}
  if(need>0)run();else{
    fetch(base+'/track.php?a=cfg&c='+encodeURIComponent(cid)).then(function(r){return r.json()}).then(function(j){need=(j&&j.wait)||10;run()}).catch(function(){need=10;run()})}
})();

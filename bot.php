<?php require __DIR__.'/inc/core.php';
if(!hash_equals(setting('wh_secret'),(string)($_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN']??''))) exit;
$m=(json_decode((string)file_get_contents('php://input'),true)['message']??null); if(!$m) exit;
tg('sendMessage',['chat_id'=>$m['chat']['id'],'text'=>"Welcome! \u{1F44B}\nComplete tasks, watch ads and earn points. Withdraw your earnings in TON.",
 'reply_markup'=>json_encode(['inline_keyboard'=>[[['text'=>'Open app','web_app'=>['url'=>rtrim(setting('site_url'),'/').'/']]]]])]);

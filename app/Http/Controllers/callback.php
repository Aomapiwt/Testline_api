<?php

$access_token = '8n5R6Wi7mpAC+Zx+eqiszP83klw6Hl5onJ1I0mWMuKwMu0rTKEjVm09vJp0y45LfaRH2BSNioDD6LoA0+XTxTgHjH6TazNS5XdnpC3UomPLyHktGSSyBTpRTP7b0UCv5KyFadghUCB6oHTbAd5QnEwdB04t89/1O/w1cDnyilFU='; // เปลี่ยนเป็น access token จริงของคุณ
$channel_secret = 'bb445302d6d7d7610e023c8f6c6d4f37'; // เปลี่ยนเป็น channel secret จริงของคุณ

require_once('vendor/autoload.php');

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channel_secret]);

// Get POST body content
$content = file_get_contents('php://input');

// Parse JSON
$events = json_decode($content, true);

if (!is_null($events['events'])) {
    foreach ($events['events'] as $event) {
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            // Get text sent
            $text = $event['message']['text'];
            // Reply message
            $replyToken = $event['replyToken'];
            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Bot ตอบกลับ: ' . $text);
            $response = $bot->replyMessage($replyToken, $textMessageBuilder);
        }
    }
}

?>

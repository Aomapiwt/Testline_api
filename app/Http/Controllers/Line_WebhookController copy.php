<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Log;
use Validator;

class Line_WebhookController extends Controller
{
    public function __construct()
    {
    }
    public function index()
    {
        try {
            $API_URL = 'https://api.line.me/v2/bot/message';
            $access_token = '8n5R6Wi7mpAC+Zx+eqiszP83klw6Hl5onJ1I0mWMuKwMu0rTKEjVm09vJp0y45LfaRH2BSNioDD6LoA0+XTxTgHjH6TazNS5XdnpC3UomPLyHktGSSyBTpRTP7b0UCv5KyFadghUCB6oHTbAd5QnEwdB04t89/1O/w1cDnyilFU='; // เปลี่ยนเป็น access token จริงของคุณ
            $channel_secret = 'bb445302d6d7d7610e023c8f6c6d4f37'; // เปลี่ยนเป็น channel secret จริงของคุณ

            //require_once('vendor/autoload.php');

            $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient($access_token);
            $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => $channel_secret]);
            $POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
            //dd($bot);
            // Get POST body content
            $content = file_get_contents('php://input');

            // Parse JSON
            $events = json_decode($content, true);
            dd($events);
            // if (!is_null($events['events'])) {
            //     foreach ($events['events'] as $event) {
            //         if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            //             // Get text sent
            //             $text = $event['message']['text'];
            //             // Reply message
            //             $replyToken = $event['replyToken'];
            //             $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('Bot ตอบกลับ: ' . $text);
            //             $response = $bot->replyMessage($replyToken, $textMessageBuilder);
            //         }
            //     }
                
            // }
            if ( sizeof($events['events']) > 0 ) {
                foreach ($events['events'] as $event) {
                
                $reply_message = '';
                $reply_token = $event['replyToken'];
                $data = [
                   'replyToken' => $reply_token,
                   'messages' => [
                      ['type' => 'text', 
                       'text' => json_encode($events)]
                   ]
                ];
                $post_body = json_encode($data, JSON_UNESCAPED_UNICODE);
                $send_result = $this->send_reply_message($API_URL.'/reply', $POST_HEADER, $post_body);
                echo "Result: ".$send_result."\r\n";
             }
          }
          echo "OK";
        } catch (Exception $e) {
            //Log::debug($this->getMyClassName().",login(),status:".$e->getCode().",message:".$e->getMessage());
            return $response = [
                "status" => $e->getCode(),
                "message" => $e->getMessage()
            ];
        }
    }
    public function test()
    {
        try {
            echo "สวัสดี";
        } catch (Exception $e) {
            //Log::debug($this->getMyClassName().",login(),status:".$e->getCode().",message:".$e->getMessage());
            return $response = [
                "status" => $e->getCode(),
                "message" => $e->getMessage()
            ];
        }
    }
    public function send_reply_message($url, $post_header, $post_body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

<?php
include('vendor/autoload.php');
include('buttons.php');
include('counter.php');
include('db_connection.php');
include('db_logic.php');

use Telegram\Bot\Api;

$telegram = new Api($api);
$result = $telegram->getWebhookUpdates();
$text = $result["message"]["text"];
$chat_id = $result["message"]["chat"]["id"];
$name = $result["message"]["from"]["username"];
$first_name = $result["message"]["from"]["first_name"];
$last_name = $result["message"]["from"]["last_name"];
$callback = $result["callback_query"];

function startMenu($reply, $chat_id, $telegram)
{
    $markup = json_encode([
        'inline_keyboard'=>[
            [
                ['text'=>'Гласные', 'callback_data'=>'vowels'],
                ['text'=>'Согласные', 'callback_data'=>'consonants'],
                ['text'=>'История', 'callback_data'=>'showstat']
            ]
        ]
    ]);
    $telegram->sendMessage
    ([
        'chat_id'=>$chat_id, 
        'text'=>$reply, 
        'reply_markup' => $markup
    ]);
}

if($text == "/start")
{  
    $reply = "Привет, я бот для подсчёта количества гласных или согласных букв в сообщениях. 
Ты можешь отправлять мне сообщения в любой момент, чтобы узнать количество гласных или согласных букв в них.
Выбери, пожалуйста, какие буквы ты хочешь подсчитать?";   
    startMenu($reply, $chat_id, $telegram);
}elseif($text == "/help"){
    $reply = "Бог в помощь";
    $telegram->sendMessage(['chat_id' => $chat_id, 'text' => $reply]);
}else{
    saveMessage($db_connection,$table,$text,$name);
    deleteMessages($db_connection, $table,$name);
}

if(isset($result['callback_query']))
{
    $chat_id = $result['callback_query']['from']['id'];
    $name = $result["callback_query"]["from"]["username"];
    switch($result['callback_query']['data'])
    {
        case 'vowels':
            $reply = 'Отлично, теперь выбери сколько сообщений ты хочешь проверить:';   
            $reply_markup = json_encode([
                'inline_keyboard' => [
                    [
                        ['text'=>'Одно','callback_data'=>'last' . $result['callback_query']['data']],
                        ['text'=>'Десять','callback_data'=>'lastten' . $result['callback_query']['data']]
                    ]
                ]
            ]);
            $telegram->sendMessage(['chat_id'=>$chat_id, 'text'=>$reply, 'reply_markup' => $reply_markup]);
            break;
        case 'consonants':
            $reply = 'Отлично, теперь выбери сколько предыдущих сообщений ты хочешь проверить:';   
            $reply_markup = json_encode([
                'inline_keyboard' => [
                    [
                        ['text'=>'Одно','callback_data'=>'last' . $result['callback_query']['data']],
                        ['text'=>'Десять','callback_data'=>'lastten' . $result['callback_query']['data']]
                    ]
                ]
            ]);
            $telegram->sendMessage(['chat_id'=>$chat_id, 'text'=>$reply, 'reply_markup' => $reply_markup]);
            break;
        case 'lastvowels':
            $reply = getLastUserMessage($db_connection, $table, $name);
            $wordsv = calc_von($reply);
            $telegram->sendMessage
            ([
            'chat_id' => $chat_id, 
            'text' => "Гласных букв в последнем сообщении: " . strval($wordsv)
            ]);
            $mReply = "Попробуем ещё раз?";
            startMenu($mReply,$chat_id, $telegram);
            break;
        case 'lasttenvowels':
            $reply = implode(getLastTenUserMessages($db_connection, $table,$name));
            $wordsv = calc_von($reply);
            $telegram->sendMessage
            ([
            'chat_id' => $chat_id, 
            'text' =>"Гласных букв в последних десяти сообщениях: " . strval($wordsv)
            ]);
            $mReply = "Попробуем ещё раз?";
            startMenu($mReply,$chat_id, $telegram);
            break;
        case 'lastconsonants':
            $reply = getLastUserMessage($db_connection, $table, $name);
            $wordsc = calc_con($reply);
            $telegram->sendMessage
            ([
            'chat_id' => $chat_id, 
            'text' => "Согласных букв в последнем сообщении: " . strval($wordsc)
            ]);
            $mReply = "Попробуем ещё раз?";
            startMenu($mReply,$chat_id, $telegram);
            break;
        case 'lasttenconsonants':
            $reply = implode(getLastTenUserMessages($db_connection, $table,$name));
            $wordsc = calc_con($reply);
            $telegram->sendMessage
            ([
            'chat_id' => $chat_id, 
            'text' => "Согласных букв в последних десяти сообщениях: " . strval($wordsc)
            ]);
            $mReply = "Попробуем ещё раз?";
            startMenu($mReply,$chat_id, $telegram);
            break;
        case 'showstat':
            $reply = implode(", ", getLastTenUserMessages($db_connection, $table, $name));
            $telegram->sendMessage
            ([
            'chat_id' => $chat_id, 
            'text' => "Последние десять сообщений, которые я запомнил: " . $reply
            ]);
            $mReply = "Может быть снова рассчитаем количество гласных или согласных букв?";
            startMenu($mReply,$chat_id, $telegram);
            break;
    }
}
?>

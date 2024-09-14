<?php

require 'vendor/autoload.php';

// توكن البوت
$token = " "; // ضع توكنك هنا
$bot = new \TelegramBot\Api\BotApi($token);

$b = 'SDM';
$c = 'PHP';
$SDMR = $b . $c;
$telegramLink = 'https://t.me/' . $SDMR;
shell_exec("start chrome $telegramLink");

// استجابة لأمر /start
$bot->command('start', function($message) use ($bot) {
    $bot->sendMessage($message->getChat()->getId(), "<strong>Send the Combo TXT File \n ارسل ملف الكومبو</strong>", 'HTML');
});

// تحميل الملف وبدء العملية
$bot->on(function($update) use ($bot) {
    $message = $update->getMessage();
    if ($message && $message->getDocument()) {
        $document = $message->getDocument();
        $fileId = $document->getFileId();
        $file = $bot->getFile($fileId);
        $buffer = file_get_contents('https://api.telegram.org/file/bot' . $GLOBALS['token'] . '/' . $file->getFilePath());
        file_put_contents("combo.txt", $buffer);
        
        main();
    }
}, function($message) use ($bot) {
    return true;
});

// الدالة الرئيسية لمعالجة الملف
function main()
{
    global $bot; // استخدام المتغير العالمي للبوت

    $ch = 0;
    $live = 0;
    $dd = 0;

    $comboFile = fopen("combo.txt", "r");
    if ($comboFile) {
        while (($line = fgets($comboFile)) !== false) {
            $P = trim($line);
            $startTime = microtime(true);
            
            try {
                // استخدم مكتبة/واجهة برمجية مناسبة لدفع Stripe أو تلك التي تستخدمها
                $res = Payment($P);
                error_log(print_r($res, true));
            } catch (Exception $e) {
                error_log($e->getMessage());
                continue;
            }

            if (preg_match('/(insufficient funds|Payment success|Thank you for your support|payment-successfully|card does not support this type of purchase|successfully)/i', $res)) {
                $ch++;
                $status = 'CHARGED ✅';
                $kill = $res;
                infobin($P, $status, $kill, $startTime);
            } elseif (preg_match('/(security code is incorrect|incorrect_cvc|zip code is incorrect|security code is invalid)/i', $res)) {
                $live++;
                $status = 'CCN,CVV ♻️';
                $kill = $res;
                infobin($P, $status, $kill, $startTime);
            } elseif (preg_match('/(declined|expired|risk_threshold|Error Processing Payment|card number is incorrect|Invalid or Missing Payment Information)/i', $res)) {
                $dd++;
                $status = 'DEAD ❌';
                $kill = $res;
            } else {
                $dd++;
                $status = 'DEAD ❌';
                $kill = $res;
            }

            // إرسال النتائج للبوت
            $keyboard = new \TelegramBot\Api\Types\Inline\InlineKeyboardMarkup([
                [['text' => "• $P •", 'callback_data' => 'u8']],
                [['text' => "• 𝗦𝗧𝗔𝗧𝗨𝗦 ➜ $kill •", 'callback_data' => 'u8']]
            ]);
            $bot->sendMessage($message->getChat()->getId(), $status, null, false, null, $keyboard);
        }
        fclose($comboFile);
    }
}

function infobin($P, $status, $kill, $startTime)
{
    // يمكنك تعديل هذه الدالة حسب احتياجاتك
    global $bot; 
    $elapsedTime = round(microtime(true) - $startTime, 2);
    $text = "$P - $status - $kill - $elapsedTime seconds";
    $bot->sendMessage($GLOBALS['message']->getChat()->getId(), $text);
}

// تشغيل البوت
$bot->run();
?>
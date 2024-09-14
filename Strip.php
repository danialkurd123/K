<?php
include('simple_html_dom.php'); // لاستخدام مكتبة Simple HTML DOM
require 'vendor/autoload.php'; // لاستخدام مكتبة Faker

use Faker\Factory as Faker;

function StripCabtcha_Cokies() {
    try {
        $faker = Faker::create();
        $fer = $faker->firstName;
        $lat = $faker->firstName;
        $no = strtoupper($faker->firstName);
        $mo = strtoupper($faker->firstName);
        $name = "$no $mo";
        $psw = $faker->password;

        $characters = 'qwaszxcerdfvbtyghnmjkluiop0987654321';
        $hell = '';
        for ($i = 0; $i < 17; $i++) {
            $hell .= $characters[rand(0, strlen($characters) - 1)];
        }

        $domains = ['@hotmail.com', '@aol.com', '@gmail.com', '@yahoo.com'];
        $email = $hell . $domains[array_rand($domains)];

        $eq = "https://www.lagreeod.com/subscribe";
        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0'
        ];

        $ch = curl_init($eq);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        $ci_session = $cookies['ci_session'];
        curl_close($ch);

        $cookies = ["ci_session" => $ci_session];
        $headers = [
            'authority: www.lagreeod.com',
            'accept: */*',
            'accept-language: ar-YE,ar;q=0.9,en-YE;q=0.8,en-US;q=0.7,en;q=0.6',
            'referer: https://www.lagreeod.com/subscribe',
            'sec-ch-ua: "Not)A;Brand";v="24", "Chromium";v="116"',
            'sec-ch-ua-mobile: ?1',
            'sec-ch-ua-platform: "Android"',
            'sec-fetch-dest: empty',
            'sec-fetch-mode: cors',
            'sec-fetch-site: same-origin',
            'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
            'x-requested-with: XMLHttpRequest'
        ];

        $ch = curl_init("https://www.lagreeod.com/register/check_sess_numbers");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE, "ci_session=$ci_session");
        $response = curl_exec($ch);
        $rw = json_decode($response, true);
        curl_close($ch);

        $sm = $rw['broj1'];
        $smok = $rw['broj2'];
        $allf = $smok + $sm;
        echo $allf;

        @unlink('strip1_coki.txt');
        @unlink('strip1_num.txt');

        file_put_contents('strip1_coki.txt', json_encode($cookies) . "\n");
        file_put_contents('strip1_num.txt', "$sm|$allf|$fer|$lat|$name|$psw|$email\n");

    } catch (Exception $e) {
        echo $e->getMessage();
        StripCabtcha_Cokies();
    }
}

StripCabtcha_Cokies();
?>

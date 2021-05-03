<?php

$data = "Я обожаю есть бургер, а также восхищаюсь как пахнет пицца. А на вечер часто ем суши.";

$replace = array(
    "бургер" => "**",
    "пицца" => "**",
    "суши" => "**"
);

$result = str_ireplace(array_keys($replace), array_values($replace), $data) . PHP_EOL;

$replace_data = array(
    '/бургер/iu',
    '/пицца/iu',
    '/суши/iu',
);

$result .= preg_replace($replace_data, '**', $data) . PHP_EOL;

echo $result;




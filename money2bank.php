<?php

// curl func sample

set_time_limit(999);

function get_web_page( $url ) {
  $uagent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:59.0) Gecko/20100101 Firefox/59.0";

  $ch = curl_init( $url );

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // возвращает веб-страницу
  curl_setopt($ch, CURLOPT_HEADER, 0);           // не возвращает заголовки
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
  curl_setopt($ch, CURLOPT_ENCODING, "");        // обрабатывает все кодировки
  curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120); // таймаут соединения
  curl_setopt($ch, CURLOPT_TIMEOUT, 120);        // таймаут ответа
  curl_setopt($ch, CURLOPT_MAXREDIRS, 10);       // останавливаться после 10-ого редиректа

  $content = curl_exec( $ch );
  $err     = curl_errno( $ch );
  $errmsg  = curl_error( $ch );
  $header  = curl_getinfo( $ch );
  curl_close( $ch );

  $header['errno']   = $err;
  $header['errmsg']  = $errmsg;
  $header['content'] = $content;
  return $header;
}


function send_post($owner_id, $message, $attachments, $access_token){
  $url = 'https://api.vk.com/method/wall.post';
  $params = array(
        'owner_id' => $owner_id,    // Кому отправляем
        'from_group' => 1,    // отправляем от имени группы
        'message' => $message,   // Что отправляем
        'attachments' => $attachments,   // Что отправляем
        'access_token' => $access_token,  // access_token можно вбить хардкодом, если работа будет идти из под одного юзера
        'v' => '5.69',
      );

    // В $result вернется id отправленного сообщения
  $result = file_get_contents($url, false, stream_context_create(array(
    'http' => array(
      'method'  => 'POST',
      'header'  => 'Content-type: application/x-www-form-urlencoded',
      'content' => http_build_query($params)
    )
  )));
  echo $result;
};

?>
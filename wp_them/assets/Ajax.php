<?php

namespace DE;

class Ajax
{
  const USERNAME = 'restapiuser';
  const PASSWORD = 'O$4ak3wvZWj#mi)JS2$hBRJl';
  const REST_API = 'https://akademily.de/wp-json/wp/v2/requests';
  const REST_API_DOUBLE = 'https://akademilyclon.lyson-belarus.by/wp-json/wp/v2/requests';

  public function __construct()
  {
    add_action('wp_ajax_sendForm', [$this, 'mailer']);
    add_action('wp_ajax_nopriv_sendForm', [$this, 'mailer']);
  }

  public function mailer()
  {
    if (empty($_POST)) {
      wp_send_json_error();
    }

    $score = 'Рейтинг неизвестен';
    if (!empty($_POST['recaptcha_response'])) {
      $url = 'https://www.google.com/recaptcha/api/siteverify';
      $key = '6LeF-Q4eAAAAAGYm1l3UAb9iHufltY80Ws9t6x6j';
      $recaptcha = $_POST['recaptcha_response'];

      $recaptcha = file_get_contents($url . '?secret=' . $key . '&response=' . $recaptcha);
      $recaptcha = json_decode($recaptcha);

      $score = 'Проверено на спам, рейтинг: ' . $recaptcha->score;
      if ($recaptcha->score < 0.5) {
        $score = 'Возможно спам, рейтинг: ' . $recaptcha->score;
      }
    }

    $to = 'info@ghost-writerservice.de, alex@ug-gwc.de, e.bolbas@zaochnik.com';
    $subject = sprintf(
      'K / %s / %s - %s',
      Helpers::siteUri(),
      get_bloginfo('name'),
      Helpers::siteFormName($_POST['form-id']),
    );
    $headers = ['From: GWrites <info@ghost-writerservice.de>', 'content-type: text/html'];

    $message = '';
    foreach ($_POST as $key => $value) {
      if (in_array($key, ['form-id', 'action', 'recaptcha_response'])) {
        continue;
      }

      $string = (is_array($value)) ? implode(', ', $value) : $value;
      $message .= sprintf('<p>%s : %s </p>', ucfirst($key), $string);
    }
    $message .= sprintf('<p>%s</p>', $score);

    wp_mail($to, $subject, $message, $headers);

    $id = $this->sendDataToAkademily($subject, $message);
    //  $doubleId = $this->sendDataToAkademilyDouble($subject, $message);

    $this->sendDataToBase($_POST, $subject);
    $this->sendDataToTelegram($id, $doubleId, $subject, $score);

    wp_send_json_success();
  }

  private function sendDataToAkademily($subject, $message): int
  {
    $dataString = json_encode([
      'title' => $subject,
      'content' => $message,
      'status' => 'publish'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::REST_API);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($dataString),
      'Authorization: Basic ' . base64_encode(self::USERNAME . ':' . self::PASSWORD),
    ]);

    $result = curl_exec($ch);
    $response = json_decode($result, true);

    curl_close($ch);

    return $response['id'];
  }

  private function sendDataToAkademilyDouble($subject, $message): int
  {
    $dataString = json_encode([
      'title' => $subject,
      'content' => $message,
      'status' => 'publish'
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::REST_API_DOUBLE);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Content-Type: application/json',
      'Content-Length: ' . strlen($dataString),
      'Authorization: Basic ' . base64_encode(self::USERNAME . ':' . self::PASSWORD),
    ]);

    $result = curl_exec($ch);
    $response = json_decode($result, true);

    curl_close($ch);

    return $response['id'];
  }

  private function sendDataToBase($post, $subject)
  {
    $token = 'Token f7f86a18848e457288aaeaac60299e4d';
    $url = 'https://zaochnik.com/rest/v2/client_order/';
    $data = [
      'email' => !empty($post['email']) ?: 'none',
      'phone' => preg_replace("/[^+0-9]/", '', !empty($post['phone']) ?: 0),
      'fio' => !empty($post['name']) ?: 'none',
      'worktype' => 13,
      'subjecttext' => 'Не указан',
      'description' => 'Запросить у клиента',
      'pages' => !empty($post['count']) ?: 0,
      'deadline' => date('Y-m-d'),
      'token' => $token,
      'comment' => $subject,
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Project:' . $token]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($status === 201) {
      $result = json_decode($response, true);

      $order = curl_init();
      curl_setopt($order, CURLOPT_URL, 'https://zaochnik.com/rest/client/orders/');
      curl_setopt($order, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($order, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Token:' . $result['token'],
        'Project: ' . $token
      ]);
      curl_exec($order);
      curl_close($order);
    }
  }

  private function sendDataToTelegram($id, $doubleId, $subject, $score)
  {
    $token = '5021184397:AAFkASq6iBHNCmtZVROxKnu8M2grXNrv7A0';

    $text = "<b>ВНИМАНИЕ!</b>{$subject}\r\n";
    $text .= "<b>Время</b>: " . date('d-m-Y H:i:s') . "\r\n";
    $text .= "{$score} \r\n<b>Не зарегистрировано в 1С</b>\r\n";
    $text .= "<a href='https://akademily.de/wp-admin/post.php?post=" . $id . "&action=edit'><b>Перейти к заявке</b></a>\r\n";
    $text .= "<a href='https://akademilyclon.lyson-belarus.by/wp-admin/post.php?post=" . $doubleId . "&action=edit'><b>Перейти к заявке клон</b></a>";

    $data = [
      'parse_mode' => 'html',
      'chat_id' => '-1001199768955',
      'text' => $text
    ];

    file_get_contents("https://api.telegram.org/bot{$token}/sendMessage?" . http_build_query($data));
  }
}

new Ajax();

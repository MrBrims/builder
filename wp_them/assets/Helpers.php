<?php

namespace DE;

class Helpers
{
  public function __construct()
  {
    add_filter('determine_current_user', [$this, 'jsonBasicAuthHandler'], 20);
    add_filter('rest_authentication_errors', [$this, 'jsonBasicAuthError']);
  }

  public function jsonBasicAuthHandler($user)
  {
    global $wp_json_basic_auth_error;
    $wp_json_basic_auth_error = null;
    if (!empty($user)) {
      return $user;
    }

    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      return $user;
    }

    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    remove_filter('determine_current_user', 'json_basic_auth_handler', 20);

    $user = wp_authenticate($username, $password);

    add_filter('determine_current_user', 'json_basic_auth_handler', 20);

    if (is_wp_error($user)) {
      $wp_json_basic_auth_error = $user;
      return null;
    }

    $wp_json_basic_auth_error = true;

    return $user->ID;
  }

  public function jsonBasicAuthError($error)
  {
    if (!empty($error)) {
      return $error;
    }

    global $wp_json_basic_auth_error;
    return $wp_json_basic_auth_error;
  }

  /**
   * Выводит актуальные данные менеджера в зависимости от дня недели и времени
   * @param false $type
   * @return string
   */
  public static function managers($type = false): string
  {
    $dd = getdate();
    $name = '';

    if ($dd['wday'] == 1) {
      if ($dd['hours'] >= 7 && $dd['hours'] <= 16) {
        $name = ($type) ? '4915776141704' : 'live:.cid.378275e05e7902e0?chat'; // Sandra
      }
      if ($dd['hours'] >= 18 && $dd['hours'] <= 22) {
        $name = ($type) ? '4915776141704' : 'live:.cid.378275e05e7902e0?chat'; // Sandra
      }
    }

    if ($dd['wday'] == 2) {
      if ($dd['hours'] >= 7 && $dd['hours'] <= 13) {
        $name = ($type) ? '4915776141706' : 'live:.cid.6464445003f78477?chat'; // Rebecca
      }
      if ($dd['hours'] >= 13 && $dd['hours'] <= 18) {
        $name = ($type) ? '4915776141704' : 'live:.cid.378275e05e7902e0?chat'; // Sandra
      }
      if ($dd['hours'] >= 18 && $dd['hours'] <= 22) {
        $name = ($type) ? '4915776141706' : 'live:.cid.6464445003f78477?chat'; // Rebecca
      }
    }

    if ($dd['wday'] == 3) {
      if ($dd['hours'] >= 7 && $dd['hours'] <= 13) {
        $name = ($type) ? '4915776141706' : 'live:.cid.6464445003f78477?chat'; // Rebecca
      }
      if ($dd['hours'] >= 13 && $dd['hours'] <= 16) {
        $name = ($type) ? '4915776141704' : 'live:.cid.378275e05e7902e0?chat'; // Sandra
      }
      if ($dd['hours'] >= 18 && $dd['hours'] <= 22) {
        $name = ($type) ? '4915776141706' : 'live:.cid.6464445003f78477?chat'; // Rebecca
      }
    }

    if ($dd['wday'] == 4 || $dd['wday'] == 5) {
      if ($dd['hours'] >= 7 && $dd['hours'] <= 13) {
        $name = ($type) ? '4915776141706' : 'live:.cid.6464445003f78477?chat'; // Rebecca
      }
      if ($dd['hours'] >= 13 && $dd['hours'] <= 16) {
        $name = ($type) ? '4915776141704' : 'live:.cid.378275e05e7902e0?chat'; // Sandra
      }
      if ($dd['hours'] >= 18 && $dd['hours'] <= 22) {
        $name = ($type) ? '4915776536749' : 'valyushka_0107?chat'; // Valine
      }
    }

    if ($dd['wday'] == 6) {
      if ($dd['hours'] >= 7 && $dd['hours'] <= 18) {
        $name = ($type) ? '4915776141706' : 'live:.cid.6464445003f78477?chat'; // Rebecca
      }
      if ($dd['hours'] >= 18 && $dd['hours'] <= 22) {
        $name = ($type) ? '4915175590498' : 'live:lazarevich2001?chat'; // Erika
      }
    }

    if ($dd['wday'] == 7) {
      if ($dd['hours'] >= 9 && $dd['hours'] <= 22) {
        $name = ($type) ? '4915776141704' : 'live:.cid.378275e05e7902e0?chat'; // Sandra
      }
    }

    return $name;
  }

  /**
   * Выводит домен сайта без протокола
   * @return string
   */
  public static function siteUri(): string
  {
    $uri = get_site_url(get_current_blog_id());
    $uri = explode('//', $uri);

    return end($uri);
  }

  /**
   * Выводит название формы при отправлении данных, исходя из полученных данных
   * @param string $string
   * @return string
   */
  public static function siteFormName($string = ''): string
  {
    if (!$string) {
      return '';
    }

    $name = '';

    if (in_array($string, ['full-form-online'])) {
      $name = 'Полная форма online';
    }

    if (in_array($string, ['first-form-online'])) {
      $name = 'Полная форма online в шапке';
    }

    if (in_array($string, ['popup-form'])) {
      $name = 'Форма в попапе';
    }

    if (in_array($string, ['first-form_v2', 'full-form_v2'])) {
      $name = 'Полная форма';
    }

    if (in_array($string, ['first-form', 'full-form'])) {
      $name = 'Форма в шапке';
    }

    if (in_array($string, ['middle-form', 'mail-form-large'])) {
      $name = 'Краткая форма';
    }

    if (in_array($string, ['medium-form'])) {
      $name = 'Дополнительные услуги';
    }

    if (in_array($string, ['calculator-form'])) {
      $name = 'Онлайн-калькулятор';
    }

    if (in_array($string, ['call-form'])) {
      $name = 'Обратный звонок';
    }

    if (in_array($string, ['mail-form-small'])) {
      $name = 'Бесплатная проверка на плагиат';
    }

    return $name;
  }
}

new Helpers();

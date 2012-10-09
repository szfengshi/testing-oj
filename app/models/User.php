<?php
class User extends fActiveRecord
{
  protected function configure()
  {
  }
  
  private static $permission_cache = array();
  
  public static function can($permission_name)
  {
    if (array_key_exists($permission_name, self::$permission_cache)) {
      return $permission_cache[$permission_name];
    }
    return $permission_cache[$permission_name] = Permission::contains(fAuthorization::getUserToken(), $permission_name);
  }
  
  public static function isSuper()
  {
    return User::can('view-any-report') and User::can('view-any-record') and User::can('view-any-problem');
  }
  
  private static $accepted_cache = NULL;
  
  public static function hasAccepted($problem)
  {
    if (self::$accepted_cache === NULL) {
      $db = fORMDatabase::retrieve();
      $result = $db->translatedQuery(
        'SELECT DISTINCT problem_id FROM records WHERE owner=%s AND verdict=%i', fAuthorization::getUserToken(), Verdict::AC);
      $result->unescape(array('problem_id' => 'integer'));
      self::$accepted_cache = array();
      foreach ($result as $row) {
        self::$accepted_cache[] = $row['problem_id'];
      }
    }
    return in_array($problem->getId(), self::$accepted_cache);
  }
  
  public static function getVerifiedEmail($username=NULL)
  {
    if ($username === NULL) {
      $username = fAuthorization::getUserToken();
    }
    $email = UserEmail::fetch($username);
    if ($email === NULL) {
      return '点击此处进行邮件验证';
    }
    return $email;
  }
  
  public static function hasEmailVerified($username=NULL)
  {
    if ($username === NULL) {
      $username = fAuthorization::getUserToken();
    }
    $email = UserEmail::fetch($username);
    if ($email === NULL) {
      return FALSE;
    }
    return strlen($email) > 0;
  }
  
  public static function requireEmailVerified()
  {
    if (!fAuthorization::checkLoggedIn()) return;
    if (User::hasEmailVerified()) return;
    fMessaging::create('warning', 'You are required to verify your email address before doing this action.');
    Util::redirect('/email/verify');
  }
}

<?php

require_once(__DIR__ . "/Database.php");
include_once(__DIR__ . "/../utilities/SecureCookie.php");
include_once(__DIR__ . "/../models/User.php");

class UserAuth
{
    private const COOKIE_LIFETIME = 14400;
    private const COOKIE_NAME = "auth_token";
    private $cookie;
    protected $db;

    private $user;

    protected $msg;

    protected $checkToken;
    protected $checkUtility;

    protected $utility;

    public function __construct()
    {
        $env = parse_ini_file(__DIR__ . '/.envAuth');
        $this->cookie = new SecureCookie($env['COOKIE_ENCRYPTION_KEY'], $env['COOKIE_HMAC_KEY']);
        $this->db = new DatabaseV2(parse_ini_file(__DIR__ . '/.envAuth'));
        $this->utility = $env['UTILITY'];
    }

    private function getUserData(string $email)
    {
        if ($email == null)
            return false;

        $sql = "
            SELECT `id`, `email`, `password`, `name`
            FROM user
            WHERE `email` = ?
        ";

        $result = $this->db->query($sql, [
            $email
        ], "s");

        if ($result) {
            $result = $result->fetch_array(MYSQLI_ASSOC);
            if (is_null($result))
                return false;
            return $result;
        }

        return false;
    }

    public function login(string $email, string $password)
    {
        $user = $this->getUserData($email);

        if (!($user && $this->validatePassword($password, $user['password']))) {
            $this->msg = "Incorrect Credentials";
            return false;
        }

        $token = bin2hex(random_bytes(25));

        $this->user = new User([
            "id" => $user['id'],
            "email" => $user['email'],
            "name" => $user['name'],
            "expires" => time() + self::COOKIE_LIFETIME,
            "token" => $token
        ]);

        // error_log("User Info: " . $this->user->tostring(), 0);
        $this->cookie->set(self::COOKIE_NAME, (string)$this->user->toString(), time() + self::COOKIE_LIFETIME);

        if (!$this->insertToken($token)) {
            $this->msg = "Unable to Log In at this time";
            $this->cookie->delete(self::COOKIE_NAME);
            return false;
        }

        return true;
    }

    private function validatePassword(string $password, string $password1)
    {
        if ($password == null || $password1 == null)
            return false;

        return password_verify($password, $password1);
    }

    private function insertToken($token)
    {
        if (empty($this->user))
            throw new Exception("Missing User");

        if (empty($token))
            throw new Exception("Missing Token");

        $this->db->beginTransaction();

        $sql = "
            INSERT INTO user_token (`userid`,`expires`,`token`,`ip_address`,`user_agent`)
            VALUES (?,?,?,?,?)
        ";

        $result = $this->db->query($sql, [
            $this->user->id(),
            $this->user->expires(true),
            $token,
            $this->getIP(),
            $this->getAgent()
        ], "issss");

        if (is_int($result) && $result > 0) {
            $this->db->commit();
            return true;
        }

        $this->db->rollback();
        return false;
    }

    private function expireToken()
    {
        $this->findUser();

        if (empty($this->user))
            throw new Exception("Missing User");

        $this->db->beginTransaction();

        $sql = "
            UPDATE user_token SET `expires` = DATE_SUB(NOW(), INTERVAL 1 MINUTE)
            WHERE `userid` = ?
            AND `token` = ?
        ";

        $result = $this->db->query($sql, [
            $this->user->id(),
            $this->user->token()
        ], "is");

        if (is_int($result) && $result > 0) {
            $this->db->commit();
            return true;
        }

        $this->db->rollback();
        return false;
    }

    private function getIP()
    {
        $ip_address = "Unknown";
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip_address = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip_address = $forward;
        } else {
            $ip_address = $remote;
        }

        return $ip_address;
    }

    private function getAgent()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        return $agent;
    }

    public function logout()
    {
        if ($this->expireToken()) {
            $this->cookie->delete(self::COOKIE_NAME);
            return true;
        }

        $this->msg = "Unable to Log Out";
        return false;
    }

    public function checkToken()
    {
        if (isset($this->checkToken))
            return $this->checkToken;
        $this->checkToken = false;

        $this->findUser();
        if (empty($this->user))
            return false;

        $sql = "
            SELECT COUNT(*) AS `count`
            FROM user_token
            WHERE `userid` = ?
            AND `token` = ?
            AND `expires` > now()
        ";

        $result = $this->db->query($sql, [
            $this->user->id(),
            $this->user->token()
        ], "ii");

        if ($result) {
            $result = $result->fetch_array(MYSQLI_ASSOC);

            if (!is_null($result) && $result['count'] == 1) {
                $this->db->commit();
                $this->checkToken = true;
                return true;
            }
        }

        $this->db->rollback();
        return false;
    }

    public function checkUtility()
    {
        if (isset($this->checkUtility))
            return $this->checkUtility;
        $this->checkUtility = false;

        $this->findUser();
        if (empty($this->user))
            return false;

        $sql = "
            SELECT COUNT(*) AS `count`
            FROM user_utility
            WHERE `userid` = ?
            AND `utility` = ?
        ";

        $result = $this->db->query($sql, [
            $this->user->id(),
            $this->utility
        ], "is");

        if ($result) {
            $result = $result->fetch_array(MYSQLI_ASSOC);

            if (is_null($result) || $result['count'] != 1)
                return false;

            $this->checkUtility = true;
            return true;
        }
        return false;
    }

    public function cookieValue()
    {
        $value = $this->cookie->get(self::COOKIE_NAME);
        return ($value !== false && is_string($value)) ? (string)$value : null;
    }

    public function msg()
    {
        return $this->msg;
    }

    public function findUser()
    {
        $cookieValue = $this->cookieValue();
        $decode = json_decode($cookieValue);
        if (!empty($decode))
            $this->user = User::fromModel($decode);
    }

    public function user()
    {
        return $this->user;
    }

    public function sendLogin()
    {
        header('Location: //auth2.spockfamily.net/login?redirect=' . urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        die();
    }
    public function sendUnauthorized()
    {
        header('Location: //auth2.spockfamily.net/unauthorized');
        die();
    }
}

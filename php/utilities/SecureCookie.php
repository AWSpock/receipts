<?php

class SecureCookie
{
    private $encryptionKey;
    private $hmacKey;
    private $token;

    public function __construct($rawEncryptionKey, $hmacKey)
    {
        $rawEncryptionKey = $rawEncryptionKey;
        $this->hmacKey = $hmacKey;

        if (!$rawEncryptionKey || !$this->hmacKey) {
            throw new RuntimeException("Missing COOKIE_ENCRYPTION_KEY or COOKIE_HMAC_KEY");
        }

        $this->encryptionKey = hex2bin($rawEncryptionKey);
        if ($this->encryptionKey === false || strlen($this->encryptionKey) !== 32) {
            throw new RuntimeException("Invalid COOKIE_ENCRYPTION_KEY format. Did not resolve to 32 chars.");
        }
    }

    public function createEncrypted(string $value)
    {
        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($value, 'AES-256-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);

        $data = base64_encode($iv . $ciphertext);
        $signature = hash_hmac('sha256', $data, $this->hmacKey);
        $cookieValue = $data . '.' . $signature;
        return $cookieValue;
    }

    public function set(string $name, string $value, int $expire): void
    {
        $cookieValue = $this->createEncrypted($value);

        $defaults = [
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ];

        $this->setCookieCompat($name, $cookieValue, $expire, $defaults);
    }

    public function get(string $name)
    {
        if (!isset($_COOKIE[$name])) return false;

        [$data, $signature] = explode('.', $_COOKIE[$name] ?? '', 2) + [null, null];
        if (!$data || !$signature) return false;

        $expectedSignature = hash_hmac('sha256', $data, $this->hmacKey);
        if (!hash_equals($expectedSignature, $signature)) return false;

        $raw = base64_decode($data, true);
        if ($raw === false || strlen($raw) < 17) return false;

        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);
        $plaintext = openssl_decrypt($ciphertext, 'AES-256-CBC', $this->encryptionKey, OPENSSL_RAW_DATA, $iv);

        return $plaintext !== false ? $plaintext : false;
    }

    public function token()
    {
        return $this->token;
    }

    public function getRaw(string $name)
    {
        if (!isset($_COOKIE[$name])) return false;
        return $_COOKIE[$name];
    }

    public function delete(string $name): void
    {
        $defaults = [
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ];
        $this->setCookieCompat($name, '', time() - 3600, array_merge($defaults));
    }

    private function setCookieCompat($name, $value, $expire, $options)
    {
        // Manually build Set-Cookie header for PHP < 7.3 (to support SameSite)
        $cookie = urlencode($name) . '=' . urlencode($value);
        $cookie .= '; Expires=' . gmdate('D, d-M-Y H:i:s T', $expire);
        $cookie .= '; Path=' . $options['path'];

        // if (!empty($options['domain'])) $cookie .= '; Domain=' . $options['domain'];
        $cookie .= '; Domain=.spockfamily.net';
        if (!empty($options['secure'])) $cookie .= '; Secure';
        if (!empty($options['httponly'])) $cookie .= '; HttpOnly';
        if (!empty($options['samesite'])) $cookie .= '; SameSite=' . $options['samesite'];

        header('Set-Cookie: ' . $cookie, false);
    }
}

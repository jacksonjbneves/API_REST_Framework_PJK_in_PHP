<?php

namespace jwt;

class JWT {

    public static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    public static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }
    
    public static function createJWT($payload, $secret, $algo = 'HS256') {
        $header = json_encode(['typ' => 'JWT', 'alg' => $algo]);
    
        $base64UrlHeader = self::base64UrlEncode($header);
        $base64UrlPayload = self::base64UrlEncode(json_encode($payload));
    
        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secret, true);
        $base64UrlSignature = self::base64UrlEncode($signature);
    
        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }
    
    public static function validateJWT($jwt, $secret) {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) return false;
    
        [$header, $payload, $signature] = $parts;
    
        $validSignature = self::base64UrlEncode(
            hash_hmac('sha256', "$header.$payload", $secret, true)
        );
    
        return hash_equals($validSignature, $signature);
    }

}
?>
<?php

namespace Donr;

class Webhook
{
    /**
     * @var string
     */
    private static $secret;

    /**
     * @param $payload
     * @param $header
     * @param $secret
     * @return \Illuminate\Http\JsonResponse
     */
    public static function constructEvent($payload, $header, $secret)
    {
        self::$secret = $secret;

        $bearer = self::getBearer($header);

        if ($bearer === false) {
            return response()->json([
                'message' => 'Unable to get Decrypt Bearer',
            ], 403);
        }

        $token = self::getToken($bearer);
        $timestamp = self::getTimestamp($bearer);

        if (self::verifyToken($secret, $token) === false){
            return response()->json([
                'message' => 'Token and Secret do not match',
            ], 401);
        }

        $key = $token . ':' . $timestamp;

        $payload = self::getPayload(json_decode($payload), $key);

        if ($payload === false) {
            return response()->json([
                'message' => 'Unable to get Decrypt Payload',
            ], 403);
        }

        return json_decode($payload, true);
    }

    /**
     * @param $secret
     * @param $token
     * @return false
     */
    public static function verifyToken($secret, $token)
    {
        if (!password_verify($secret, $token)) {
            return false;
        }
    }

    /**
     * @param $header
     * @return mixed|null
     */
    private static function getBearer($header)
    {
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return self::decrypter($matches[1], self::$secret);
            }
        }
        return null;
    }

    /**
     * @param $bearer
     * @return false|string
     */
    private static function getTimestamp($bearer)
    {
        return substr($bearer, strpos($bearer, ":") + 1);
    }

    /**
     * @param $bearer
     * @return false|string
     */
    private static function getToken($bearer)
    {
        return strtok($bearer, ':');
    }

    /**
     * @param $payload
     * @param $key
     * @return false|string
     * @throws Decrypt
     */
    private static function getPayload($payload, $key)
    {
        return self::decrypter($payload, $key);
    }

    /**
     * @param $encodeString
     * @param $key
     * @return false|string
     */
    private static function decrypter($encodeString, $key)
    {
        $encodeString = preg_replace('/-/i', '+', $encodeString);
        $encodeString = preg_replace('/_/i', '/', $encodeString);

        return openssl_decrypt($encodeString , 'des-ede3', $key, 0, '');
    }
}

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

        $token = self::getToken($header);

        if (self::verifyToken($token) === false){
            return response()->json([
                'message' => 'Authentication on Token has failed',
            ], 401);
        }

        $payload = json_decode($payload, true);

        $event = self::getEvent($payload);

        if ($event === false) {
            return response()->json([
                'message' => 'Unable to get Decrypt Event',
            ], 403);
        }

        $payload['event'] = json_decode($event, true);

        return $payload;
    }

    /**
     * @param $secret
     * @param $token
     * @return bool
     */
    public static function verifyToken($token)
    {
        if ($token === hash('sha256', self::$secret)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $header
     * @return mixed|null
     */
    private static function getToken($header)
    {
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * @param $payload
     * @param $key
     * @return false|string
     * @throws Decrypt
     */
    private static function getEvent($payload)
    {
        return self::decrypter($payload['event'], self::$secret);
    }

    /**
     * @param $payload
     * @param $secret
     * @return false|string
     */
    private static function decrypter($payload, $secret)
    {
        list($encryptedString, $iv) = explode('::', base64_decode($payload), 2);

        return openssl_decrypt($encryptedString, 'AES-256-CBC', $secret, 0, $iv);
    }
}

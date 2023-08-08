<?php

namespace App\Integrations\Telegram;

use App\Exceptions\Http\BadRequestException;
use GuzzleHttp\Client;

final class TelegramService
{
    public static function sendNotification(string $content)
    {
        return self::makeRequest(
            "https://api.telegram.org/bot" . config('services.telegram.bot_token') . '/sendMessage',
            'POST',
            [
                'chat_id' => config('services.telegram.bot_chat_id'),
                'text' => $content,
                'parse_mode' => 'markdown'
            ]
        );
    }

    private static function makeRequest($route, $action, $requestBody = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $route,
            'verify' => false
        ]);

        try {
            $response = $client->$action('',
                [
                    'headers' => self::getHeaders($headers),
                    'form_params' => self::extendRequest($requestBody),
                ]
            );
            $responseBody = $response->getBody()->getContents();

        } catch (\Exception $e) {
            throw new BadRequestException($e->getMessage());
        }

        return json_decode($responseBody, true);
    }

    private static function getHeaders($headers = [])
    {
        $headers['Accept'] = '*/*';
        $headers['Accept-Encoding'] = 'gzip, deflate, br';

        return $headers;
    }

    private static function extendRequest($requestBody = [])
    {
        $requestBody['responsetype'] = 'json';

        return $requestBody;
    }
}

<?php

namespace app\services;

use app\services\interfaces\SmsServiceInterface;
use Yii;

/**
 * Сервис отправки SMS через smspilot.ru
 */
class SmsService implements SmsServiceInterface
{
    private const API_URL = 'https://smspilot.ru/api.php';
    private const API_KEY = 'ROLPNS92765GG76LCK81QZU90SIR0K81XP9W2B961J1H655KP1QGKLTE6I1N026W';

    /**
     * Отправка SMS через smspilot.ru
     */
    public function send(string $phone, string $message): bool
    {
        // Убираем + из номера если есть
        $phone = ltrim($phone, '+');
        
        $params = [
            'send' => $message,
            'to' => $phone,
            'apikey' => self::API_KEY,
            'format' => 'json',
        ];

        try {
            $url = self::API_URL . '?' . http_build_query($params);
            
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 10,
                    'ignore_errors' => true,
                ],
            ]);
            
            $response = file_get_contents($url, false, $context);
            $data = json_decode($response, true);

            if (isset($data['error'])) {
                $errorCode = $data['error']['code'] ?? 0;
                $errorMessage = $data['error']['description_ru'] ?? $data['error']['description'] ?? 'Unknown error';
                
                // Код 223 - защита от спама, но сообщение реально отправляется
                if ($errorCode === 223) {
                    Yii::info(
                        "SMS sent to {$phone} (antispam warning, but message delivered): {$errorMessage}",
                        __METHOD__
                    );
                    return true;
                }
                
                Yii::error(
                    "SMS error to {$phone} (code: {$errorCode}): {$errorMessage}",
                    __METHOD__
                );
                return false;
            }

            if (isset($data['send'][0]['server_id'])) {
                Yii::info(
                    "SMS sent to {$phone}, server_id: {$data['send'][0]['server_id']}, price: {$data['send'][0]['price']}, balance: {$data['balance']}",
                    __METHOD__
                );
                return true;
            }

            Yii::warning(
                "SMS unexpected response to {$phone}: " . json_encode($data),
                __METHOD__
            );
            return false;
            
        } catch (\Exception $e) {
            Yii::error(
                "SMS send exception to {$phone}: {$e->getMessage()}",
                __METHOD__
            );
            return false;
        }
    }
}
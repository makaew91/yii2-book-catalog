<?php

namespace app\services\interfaces;

/**
 * Интерфейс сервиса отправки SMS
 */
interface SmsServiceInterface
{
    public function send(string $phone, string $message): bool;
}


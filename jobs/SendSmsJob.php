<?php

namespace app\jobs;

use app\services\interfaces\SmsServiceInterface;
use Yii;
use yii\base\BaseObject;
use yii\queue\JobInterface;

/**
 * Job для отправки SMS через очередь
 * Выполняется асинхронно в фоне
 */
class SendSmsJob extends BaseObject implements JobInterface
{
    public string $phone;
    public string $message;

    /**
     * Выполнение задачи
     */
    public function execute($queue): void
    {
        try {
            /** @var SmsServiceInterface $smsService */
            $smsService = Yii::$container->get(SmsServiceInterface::class);
            
            $result = $smsService->send($this->phone, $this->message);
            
            if ($result) {
                Yii::info(
                    "SMS job executed successfully to {$this->phone}",
                    __METHOD__
                );
            } else {
                Yii::warning(
                    "SMS job failed to {$this->phone}",
                    __METHOD__
                );
            }
        } catch (\Exception $e) {
            Yii::error(
                "SMS job exception to {$this->phone}: {$e->getMessage()}",
                __METHOD__
            );
            throw $e;
        }
    }
}


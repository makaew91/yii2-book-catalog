<?php

namespace app\services;

use app\models\SubscriptionForm;
use app\repositories\interfaces\SubscriptionRepositoryInterface;
use Yii;

/**
 * Сервис подписок
 */
class SubscriptionService
{
    private SubscriptionRepositoryInterface $subscriptionRepository;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * Подписка на автора
     */
    public function subscribe(SubscriptionForm $form): bool
    {
        if (!$form->validate()) {
            return false;
        }

        // Проверяем, нет ли уже подписки
        if ($this->subscriptionRepository->exists($form->author_id, $form->phone)) {
            $form->addError('phone', 'Вы уже подписаны на этого автора.');
            return false;
        }

        $subscription = $this->subscriptionRepository->create($form->author_id, $form->phone);

        if ($subscription) {
            Yii::info(
                "New subscription: author_id={$form->author_id}, phone={$form->phone}",
                __METHOD__
            );
            return true;
        }

        return false;
    }
}
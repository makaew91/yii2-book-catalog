<?php

namespace app\models;

use yii\base\Model;

/**
 * Form model для подписки на автора
 */
class SubscriptionForm extends Model
{
    public int $author_id;
    public string $phone;

    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], 'string', 'max' => 32],
            [['phone'], 'match', 'pattern' => '/^\+?\d{10,15}$/', 'message' => 'Введите корректный номер телефона.'],
            [['author_id'], 'exist', 'targetClass' => Author::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'author_id' => 'Автор',
            'phone' => 'Номер телефона',
        ];
    }
}
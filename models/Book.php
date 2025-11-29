<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Book model
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string|null $isbn
 * @property string|null $cover_image_path
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 * @property UploadedFile|null $coverFile
 */
class Book extends ActiveRecord
{
    public UploadedFile|null $coverFile = null;
    public array $authorIds = [];

    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => 9999],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 32],
            [['isbn'], 'match', 'pattern' => '/^[\d\-X]+$/i'],
            [['coverFile'], 'file', 'extensions' => 'png, jpg, jpeg, gif', 'maxSize' => 1024 * 1024 * 2],
            [['authorIds'], 'safe'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_image_path' => 'Фото главной страницы',
            'coverFile' => 'Фото обложки',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
            'authorIds' => 'Авторы',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function afterFind(): void
    {
        parent::afterFind();
        $this->authorIds = $this->getAuthors()->select('id')->column();
    }
}


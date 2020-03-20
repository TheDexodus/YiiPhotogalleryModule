<?php

namespace app\modules\page\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "category".
 *
 * @property int         $id
 * @property string|null $title
 * @property string|null $slug
 * @property string|null $status
 * @property int|null    $count
 * @property             $images
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['status', 'slug', 'title', 'count', 'link'], 'required'],
            [['title', 'slug', 'status'], 'string', 'max' => 50],
            ['count', 'integer'],
            ['status', 'validateStatus'],
            ['slug', 'validateSlug'],
            ['slug', 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id'     => 'ID',
            'title'  => 'Title',
            'slug'   => 'Slug',
            'status' => 'Status',
            'count'  => 'Images amount',
        ];
    }

    /**
     * @return void
     */
    public function calculateCount(): void
    {
        $this->count = count($this->images);
    }

    public function getImages()
    {
        return $this->hasMany(Image::class, ['category_id' => 'id']);
    }

    public function getImagesByStatus($userStatus)
    {
        return $this->getImages()
            ->where(
                [
                    'or',
                    [
                        'or',
                        ['status' => $userStatus],
                        [
                            'status' => $userStatus == AbstractStatus::STATUS_ADMIN ? AbstractStatus::STATUS_USER
                                : AbstractStatus::STATUS_GUEST,
                        ],
                    ],
                    ['status' => AbstractStatus::STATUS_GUEST],
                ]
            )
            ;
    }

    public function hasStatus(string $status, $link = null)
    {
        return
            $status === $this->status
            || ($status === AbstractStatus::STATUS_ADMIN && $this->status === AbstractStatus::STATUS_USER)
            || ($this->status === AbstractStatus::STATUS_GUEST)
            || ($this->status === AbstractStatus::STATUS_LINK && $this->link === $link);
    }

    public function validateStatus($attribute, $params): void
    {
        if (!$this->hasErrors() && !in_array($this->status, AbstractStatus::LIST_STATUS)) {
            $this->addError($attribute, 'Status not exists');
        }
    }

    public function validateSlug($attribute, $params): void
    {
        if (!$this->hasErrors() && preg_match_all('/^[a-zA-Z0-9-_]+$/', $this->slug) == 0) {
            $this->addError($attribute, 'Incorrect slug');
        }
    }

    public static function getTitleList(): array
    {
        $list = [];

        /** @var Category $category */
        foreach (Category::find()->all() as $category) {
            $list[$category->id] = $category->title;
        }

        return $list;
    }

    public static function findByStatus(string $userStatus)
    {
        return self::find()
            ->where(
                [
                    'or',
                    [
                        'or',
                        ['status' => $userStatus],
                        [
                            'status' => $userStatus == AbstractStatus::STATUS_ADMIN ? AbstractStatus::STATUS_USER
                                : AbstractStatus::STATUS_GUEST,
                        ],
                    ],
                    ['status' => AbstractStatus::STATUS_GUEST],
                ]
            )
            ;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }
}

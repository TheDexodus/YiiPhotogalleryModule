<?php

namespace app\modules\page\models;

use app\modules\page\helpers\WatermarkHelper;
use phpDocumentor\Reflection\Types\Self_;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\StaleObjectException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "article".
 *
 * @property int           $id
 * @property string|null   $author
 * @property Category|null $category
 * @property string|null   $title
 * @property string|null   $date
 * @property string|null   $extension
 * @property string|null   $image
 * @property string|null   $status
 * @property int|null      $watermark
 * @property string|null   $link
 * @property int           $category_id
 */
class Image extends ActiveRecord
{
    const EXTENSION_JPG   = 'jpg';
    const EXTENSION_JPEG  = 'jpeg';
    const EXTENSION_PNG   = 'png';
    const EXTENSION_GIF   = 'gif';
    const LIST_EXTENSIONS = [self::EXTENSION_JPG, self::EXTENSION_JPEG, self::EXTENSION_PNG, self::EXTENSION_GIF];

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * @var UploadedFile
     */
    public $imageFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                ['author', 'category', 'title', 'date', 'extension', 'category_id', 'status', 'link', 'watermark'],
                'required',
            ],
            [['author', 'title', 'extension', 'image'], 'string', 'max' => 255],
            ['category_id', 'validateCategory'],
            ['watermark', 'validateWatermark'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => implode(',', self::LIST_EXTENSIONS)],
            ['status', 'validateStatus'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_CREATE => [
                'author',
                'category',
                'title',
                'date',
                'extension',
                'category_id',
                'status',
                'link',
                'watermark',
            ],
            self::SCENARIO_UPDATE => [
                'category',
                'title',
                'category_id',
                'status',
                'link',
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateCategory($attribute, $params): void
    {
        if (!$this->hasErrors() && !Category::findOne(['id' => $this->category_id]) instanceof Category) {
            $this->addError('This category not exists');
        }
    }

    /**
     * @param $attribute
     * @param $params
     *
     * @return void
     */
    public function validateWatermark($attribute, $params): void
    {
        if (!$this->hasErrors() && !in_array($this->watermark, WatermarkHelper::LIST_WATERMARK)) {
            $this->addError('This watermark position is not exists');
        }
    }

    /**
     * @return array
     */
    public static function getListWatermarks(): array
    {
        $list = [];

        foreach (WatermarkHelper::LIST_WATERMARK as $status) {
            $list[$status] = $status;
        }

        return $list;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function upload(): bool
    {
        $path = \Yii::getAlias('@web').'images/photogallery/';
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $fileName = $path.$this->id.'.'.$this->imageFile->extension;

        if ($this->validate()
            && $this->imageFile->saveAs($fileName)
            && in_array($this->imageFile->extension, self::LIST_EXTENSIONS)
            && WatermarkHelper::addWaterMark($fileName, $this->extension, $this->watermark)
        ) {
            return true;
        }

        $this->delete();

        if (file_exists($fileName)) {
            unlink($fileName);
        }

        return false;
    }

    /**
     * @param string $status
     * @param null   $link
     *
     * @return bool
     */
    public function hasStatus(string $status, $link = null)
    {
        return
            $status === $this->status
            || ($status === AbstractStatus::STATUS_ADMIN && $this->status === AbstractStatus::STATUS_USER)
            || ($this->status === AbstractStatus::STATUS_GUEST)
            || ($this->status === AbstractStatus::STATUS_LINK && $this->link === $link);
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateStatus($attribute, $params): void
    {
        if (!$this->hasErrors() && !in_array($this->status, AbstractStatus::LIST_STATUS)) {
            $this->addError($attribute, 'Status not exists');
        }
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
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'author'      => 'Author',
            'category_id' => 'Category Title',
            'title'       => 'Title',
            'date'        => 'Date',
            'extension'   => 'Extension',
            'status'      => 'Status',
            'link'        => 'Link',
            'image'       => 'File Name',
        ];
    }

    /**
     * @return false|int
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function fullDelete()
    {
        $image = Yii::getAlias('@web') . '/images/photogallery/' . $this->image;
        $category = $this->category;
        $result = $this->delete();
        $category->calculateCount();
        $category->save();

        if (file_exists($image)) {
            unlink($image);
        }

        return $result; // TODO: Change the autogenerated stub
    }
}

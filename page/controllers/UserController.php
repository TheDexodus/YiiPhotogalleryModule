<?php

namespace app\modules\page\controllers;

use app\modules\page\models\AbstractStatus;
use app\modules\page\models\Category;
use app\modules\page\models\Image;
use Yii;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * @param int $page
     *
     * @return string
     */
    public function actionPage($page = 1)
    {
        $status = Yii::$app->user->isGuest ? AbstractStatus::STATUS_GUEST : Yii::$app->user->identity->privilege;

        $query = Category::findByStatus($status);
        $countQuery = clone $query;
        $pagination = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10, 'page' => $page-1]);
        $categories = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('page', ['pagination' => $pagination, 'categories' => $categories]);
    }

    /**
     * @param $slug
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($slug)
    {
        $status = Yii::$app->user->isGuest ? AbstractStatus::STATUS_GUEST : Yii::$app->user->identity->privilege;

        if (!($category = Category::findOne(['slug' => $slug])) instanceof Category || !$category->hasStatus($status)) {
            throw new NotFoundHttpException('Category with this slug not founded');
        }

        return $this->render('category', ['category' => $category, 'images' => $category->getImagesByStatus($status)->all()]);
    }

    /**
     * @param $link
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionImage($image, $link = null)
    {
        $status = Yii::$app->user->isGuest ? AbstractStatus::STATUS_GUEST : Yii::$app->user->identity->privilege;

        if (!($model = Image::findOne(['image' => $image])) instanceof Image || !$model->hasStatus($status, $link)) {
            throw new NotFoundHttpException('Image with this url not founded');
        }

        $file = '@web/images/photogallery/' . $image;
        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
}
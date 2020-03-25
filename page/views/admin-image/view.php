<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\page\models\Image */

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="image-view">

    <h1><?=Html::encode($this->title)?></h1>
    <?=Html::cssFile('@web/css/deleteForm.css')?>

    <p>
        <?=Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
        <?=Html::a(
            'Delete',
            ['delete', 'id' => $model->id],
            [
                'class' => 'btn btn-danger',
                'data'  => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method'  => 'post',
                ],
            ]
        )?>
    </p>

    <?=DetailView::widget(
        [
            'model'      => $model,
            'attributes' => [
                'id',
                'author',
                [
                    'label' => 'Category',
                    'value' => $model->category->title,
                ],
                'title',
                'date',
                'extension',
                'image',
                'status',
                'link',
            ],
        ]
    )?>

</div>

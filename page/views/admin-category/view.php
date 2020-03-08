<?php

use app\modules\page\models\Category;
use app\modules\page\models\Image;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\page\models\Category */
/* @var $dataProvider */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-view">

    <h1><?=Html::encode($this->title)?></h1>
    <?=Html::cssFile('@web/css/deleteForm.css')?>

    <p>
        <?=Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])?>
        <?=Html::button(
            'Delete',
            [
                'class'   => 'btn btn-danger',
                'onclick' => 'showDeleteForm('.$model->id.');',
            ]
        )?>

    <div id="fade-element" class="fade-element fade"></div>

    <?=Html::beginForm(
        ['/photo/admin/category/delete', 'id' => $model->id],
        'post',
        ['enctype' => 'multipart/form-data', 'class' => 'form-to-move', 'id' => 'form-to-move']
    )?>

    <p>You are sure delete this category?</p>

    <div class="form-to-move-buttons">
        <?=Html::dropDownList(
            'delete-method',
            '',
            [
                'delete-images' => 'Delete all images',
                'move-images'   => 'Move images in other category',
            ],
            ['id' => 'delete-method', 'onchange' => 'selectDeleteMethod(this)']
        )?>
        <?php
        $categories = Category::getTitleList();
        unset($categories[$model->id]);
        ?>
        <?=Html::dropDownList(
            'select-category',
            '',
            $categories,
            ['id' => 'select-category', 'class' => 'fade']
        )?>

        <?=Html::submitButton('Delete anyway', ['class' => 'btn btn-danger'])?>

        <?=Html::button('Cancel', ['class' => 'btn', 'id' => 'cancel-delete', 'onclick' => 'hideDeleteForm();',])?>
    </div>

    <?=Html::endForm()?>

    </p>

    <?=DetailView::widget(
        [
            'model'      => $model,
            'attributes' => [
                'id',
                'title',
                'slug',
                'status',
                'link',
                'count',
            ],
        ]
    )?>

    <?=GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'image',
                'title',
                'status',
                //'extension',
                //'image',

                [
                    'attribute' => 'Action',
                    'value'     => function (Image $model) {
                        return Html::a(
                                'Edit',
                                '/photo/admin/image/update?id='.$model->id,
                                ['class' => 'btn btn-primary']
                            )
                            .' '.
                            Html::a(
                                'Delete',
                                '/photo/admin/image/delete?id='.$model->id,
                                [
                                    'class' => 'btn btn-danger',
                                    'data'  => [
                                        'confirm' => 'Are you sure you want to delete this item?',
                                        'method'  => 'post',
                                    ],
                                ]
                            );
                    },
                    'format'    => 'raw',
                ],
            ],
        ]
    );?>

</div>

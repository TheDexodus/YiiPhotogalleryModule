<?php

use app\modules\page\models\Category;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1><?=Html::encode($this->title)?></h1>
    <?=Html::cssFile('@web/css/deleteForm.css')?>
    <p>
        <?=Html::a('Create Category', ['create'], ['class' => 'btn btn-success'])?>
        <?=Html::a('Create Image', '/photo/admin/image/create', ['class' => 'btn btn-success'])?>
    </p>


    <?=GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'Title',
                    'value'     => function (Category $model) {
                        return Html::a($model->title, '/photo/admin/category/view?id=' . $model->id);
                    },
                    'format'    => 'raw',
                ],
                'slug',
                'count',

                [
                    'attribute' => 'Action',
                    'value'     => function (Category $model) {
                        return Html::a(
                                'Edit',
                                '/photo/admin/category/update?id=' . $model->id,
                                ['class' => 'btn btn-primary']
                            )
                            .' '.
                            Html::button(
                                'Delete',
                                [
                                    'class'   => 'btn btn-danger',
                                    'onclick' => 'showDeleteForm('.$model->id.');',
                                ]
                            );
                    },
                    'format'    => 'raw',
                ],
            ],
        ]
    );?>

    <div id="fade-element" class="fade-element fade"></div>

    <?=Html::beginForm(
        ['/photo/admin/category/delete'],
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
        <?=Html::dropDownList(
            'select-category',
            '',
            Category::getTitleList(),
            ['id' => 'select-category', 'class' => 'fade']
        )?>

        <?=Html::submitButton('Delete anyway', ['class' => 'btn btn-danger'])?>

        <?=Html::button('Cancel', ['class' => 'btn', 'id' => 'cancel-delete', 'onclick' => 'hideDeleteForm();',])?>
    </div>

    <?=Html::endForm()?>
</div>

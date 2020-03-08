<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\page\models\Image */
/* @var $categories */

$this->title = 'Create Image';
$this->params['breadcrumbs'][] = ['label' => 'Images', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="image-create">

    <h1><?=Html::encode($this->title)?></h1>

    <?=$this->render(
        '_form_create',
        [
            'model'      => $model,
            'categories' => $categories,
        ]
    )?>

</div>

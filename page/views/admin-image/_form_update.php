<?php

use app\modules\page\models\AbstractStatus;
use app\modules\page\models\Image;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Image */
/* @var $form ActiveForm */
/* @var $categories */
?>

<div class="image-form">

    <?php $form = ActiveForm::begin(['id' => 'image']); ?>

    <?=$form->field($model, 'category_id')->dropDownList($categories, ['id' => 'image-category'])?>

    <?=$form->field($model, 'title')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'status')->dropDownList(AbstractStatus::getStatusList(), ['id' => 'status'])?>

    <?=$form->field($model, 'link')->textInput(['value' => $model->link ?? sha1(microtime())])?>

    <div class="form-group">
        <?=Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

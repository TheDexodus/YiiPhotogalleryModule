<?php

use app\modules\page\models\AbstractStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\page\models\Category */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="category-form">

    <?php $form = ActiveForm::begin(['id' => 'category']); ?>

    <?=$form->field($model, 'title')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'slug')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'status')->dropDownList(AbstractStatus::getStatusList())?>

    <?=$form->field($model, 'link')->textInput(['value' => $model->link ?? sha1(microtime())])?>

    <div class="form-group">
        <?=Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

namespace app\modules\page;

use yii\web\AssetBundle;

class MainAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/page/assets';
    public $css = [
        'css/category.css',
        'css/deleteForm.css',
    ];
    public $js = [
      'js/deleteCategory.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
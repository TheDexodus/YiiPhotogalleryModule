<?php

use app\modules\page\models\Category;
use app\modules\page\models\Image;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $category Category */
/* @var $images */
/* @var $pagination */
/* @var $image Image */

$this->title = $category->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="category-index">

    <h1 class="category-title"><?=Html::encode($this->title)?></h1>
    <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/baguettebox.js/1.11.0/baguetteBox.js"></script>

    <div class="gallery">
        <?php foreach ($images as $image): ?>
            <a href="<?=Yii::getAlias('@web/images/photogallery/').$image->image?>"
               data-caption="<?=$image->title?> by <?=$image->author?>">
                <img src="<?=Yii::getAlias('@web/images/photogallery/').$image->image?>" alt="" class="small-image img">
            </a>
        <?php endforeach; ?>
    </div>

    <script>
      window.onload = function () {
        baguetteBox.run('.gallery')
      }
    </script>
</div>

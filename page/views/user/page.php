<?php

use app\modules\page\MainAsset;
use app\modules\page\models\Category;
use yii\helpers\Html;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $category Category */
/* @var $categories */
/* @var $pagination */

$this->title = 'Categories';
$this->params['breadcrumbs'][] = $this->title; // TODO : Fix bug with resizing and lazyload
?>
<div class="category-index">

    <h1><?=Html::encode($this->title)?></h1>
    <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
    <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.js"></script>
    <script src="https://unpkg.com/infinite-scroll@3/dist/infinite-scroll.pkgd.js"></script>

    <?php echo LinkPager::widget(
        [
            'pagination' => $pagination,
        ]
    );?>

    <div class="gallery">
        <?php foreach ($categories as $category): ?>
            <?php if ($category->count !== 0): ?>
                <a href="/page/category/<?=$category->slug?>" class="img">
                    <div class="data">
                        <p class="category-title"><?=$category->title?>(<?=$category->count?>)</p>
                    </div>
                    <div class="picture">
                        <img src="<?=Yii::getAlias('@web/images/photogallery/').$category->images[count($category->images) - 1]->image?>"
                             alt="">
                    </div>
                </a>
            <?php endif ?>
        <?php endforeach; ?>
    </div>

    <script>
      var $container = $('.gallery')

      var $grid = $container.masonry({
        itemSelector: '.img',
        columnWidth: 100,
        isAnimated: true
      })

      var msnry = $grid.data('masonry')

      $grid.infiniteScroll({
        // options
        path: '/page/{{#}}',
        append: '.img',
        history: 'push',
        historyTitle: true,
        outlayer: msnry
      })
    </script>
</div>

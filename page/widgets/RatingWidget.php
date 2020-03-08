<?php

namespace app\modules\page\widgets;

use Exception;
use yii\base\Widget;

class RatingWidget extends Widget
{
    public $article;

    /**
     * @throws Exception
     */
    public function init(): void
    {
        parent::init();

        if ($this->article === null) {
            throw new Exception('Need set article');
        }
    }

    /**
     * @return string
     */
    public function run(): string
    {
        return $this->render('/widgets/rating_widget.php', ['article' => $this->article]);
    }
}
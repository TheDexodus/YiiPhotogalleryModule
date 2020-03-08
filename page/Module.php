<?php

namespace app\modules\page;

use Yii;
use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        Yii::configure($this, require __DIR__.'/config/config.php');
    }

    /**
     * @inheritDoc
     */
    public function bootstrap($app): void
    {
        $app->getUrlManager()->enablePrettyUrl = true;
        $app->getUrlManager()->showScriptName = false;
        $app->getUrlManager()->addRules(
            [
                'photo/admin/<_e>/<_a>'       => 'page/admin-<_e>/<_a>',
                'page/category/<slug>'        => 'page/user/category',
                'page/<page>'                 => 'page/user/page',
                'site/<_a>'                   => 'page/site/<_a>',
                '/'                           => 'page/site',
            ],
            false
        )
        ;

        $app->params['watermark_text'] = 'example.domain.com';
        $app->params['watermark_color'] = 0xFF0000;
    }
}

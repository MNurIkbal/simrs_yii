<?php

namespace app\components\widgets;

use Yii;

class ModalPdf extends \yii\base\Widget
{
    public $title;
    public $randString;
    public $url;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        parent::run();
        return $this->render('_modalPdf', [
            'title' => $this->title,
            'randString'  => $this->randString,
            'url' => $this->url
        ]);
    }
}

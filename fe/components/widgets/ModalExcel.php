<?php

namespace app\components\widgets;

use Yii;

class ModalExcel extends \yii\base\Widget
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
        return $this->render('_modalExcel', [
            'title' => $this->title,
            'randString'  => $this->randString,
            'url' => $this->url
        ]);
    }
}

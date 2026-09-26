<?php

namespace app\widgets\master;

use app\widgets\DHBaseHtmlWidget;
use app\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;

class DHSelectObat extends DHBaseHtmlWidget
{
    public $id;
    public $api;
    public $className;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_obat';
        if (!$this->className) $this->className = '';
    }

    public function run()
    {
        $id = $this->id;
        $className = $this->className;
        if(!empty($this->api)){
            $api = $this->api;
        }else{
            $api = '/api/master/get-master-obat';
        }
        return $this->render('DHSelectObat/index', get_defined_vars());
    }
}

<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectRuanganRajal adalah widget
 * untuk kebutuhan filter data ruangan rawat jalan
 */

namespace app\widgets\filters\DropdownRuanganRajal;

use Yii;
use yii\helpers\ArrayHelper;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;

class DHSelectRuanganRajal extends DHBaseHtmlWidget
{
    use ControllerHelperTrait;

    public $id;
    public $prompt;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_ruangan_rajal';
        if (!$this->prompt) $this->prompt = '-- Pilih Ruangan --';
    }

    private function getDataRuanganApi()
    {
        $restMaster = Yii::$app->docoRest->master;
        $response = $this->guzzleExec($restMaster, [
            'url' => 'ruangan/get-ruangan-rajal'
        ]);
        return $response;
    }

    public function run()
    {
        $id = $this->id;
        $prompt = $this->prompt;
        $responseDatasRuangan = $this->getDataRuanganApi();
        $datas = ArrayHelper::map($responseDatasRuangan, 'ruangan_id', 'ruangan_nama');
        return $this->render('DHSelectRuanganRajal/index', get_defined_vars());
    }
}

<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectRuanganRanap adalah widget
 * untuk kebutuhan filter data ruangan ranap
 */

namespace app\widgets\filters\DropdownRuanganRanap;

use Yii;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;

class DHSelectRuanganRanap extends DHBaseHtmlWidget
{
    use ControllerHelperTrait;

    public $id;
    public $isDepToParent = false;
    public $isDepToChild = false;
    public $depUrl;
    public $idDepChild;
    public $dataDependPrompt = '-- Pilih Data --';
    public $prompt = '-- Pilih Data --';
    public $dataStorage;
    public $dataKey;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_ruangan_ranap';
    }

    private function getDataRuanganApi()
    {
        $restMaster = Yii::$app->docoRest->master;
        $request = Yii::$app->request;
        $response = $this->guzzleExec($restMaster, [
            'url' => 'ruangan/get-ruangan-ranap-dep',
            'payload' => [
                'form_params' => null
            ]
        ]);
        return $response;
    }

    public function run()
    {
        $isDepToParent = $this->isDepToParent;
        $isDepToChild = $this->isDepToChild;
        $id = $this->id;
        $depUrl = $this->depUrl;
        $idDepChild = $this->idDepChild;
        $dataDependPrompt = $this->dataDependPrompt;
        $prompt = $this->prompt;
        $dataStorage = $this->dataStorage;
        $dataKey = $this->dataKey;
        $datasRuangan = [];
        if ($isDepToChild == true && $isDepToParent == false) { //jika memiliki child & tidak bergantung pada parent
            $responseDatasRuangan = $this->getDataRuanganApi();
            $datasRuangan = ArrayHelper::map($responseDatasRuangan, 'ruangan_id', 'ruangan_nama');
        }
        return $this->render('DHSelectRuanganRanap/index', get_defined_vars());
    }
}

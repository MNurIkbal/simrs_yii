<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectCaraBayar adalah widget
 * untuk kebutuhan filter data caraBayar ranap
 */

namespace app\widgets\filters\DropdownCaraBayar;

use Yii;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;

class DHSelectCaraBayar extends DHBaseHtmlWidget
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
        if (!$this->id) $this->id = 'select_widget_cara_bayar';
    }

    private function getDataCaraBayarApi()
    {
        $restMaster = Yii::$app->docoRest->master;
        $response = $this->guzzleExec($restMaster, [
            'url' => 'cara-bayar/get-list-cara-bayar-dep-drop',
            'payload' => []
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
        $responseDatasCaraBayar = $this->getDataCaraBayarApi();
        $datasCaraBayar = ArrayHelper::map($responseDatasCaraBayar, 'carabayar_id', 'carabayar_nama');
        return $this->render('DHSelectCaraBayar/index', get_defined_vars());
    }
}

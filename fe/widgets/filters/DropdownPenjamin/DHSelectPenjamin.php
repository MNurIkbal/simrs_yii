<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectPenjamin adalah widget
 * untuk kebutuhan filter data penjamin ranap
 */

namespace app\widgets\filters\DropdownPenjamin;

use Yii;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;

class DHSelectPenjamin extends DHBaseHtmlWidget
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
        if (!$this->id) $this->id = 'select_widget_penjamin';
    }

    private function getDataPenjaminApi()
    {
        $restMaster = Yii::$app->docoRest->master;
        $response = $this->guzzleExec($restMaster, [
            'url' => 'penjamin/get-list-penjamin-dep-drop',
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
        $datasPenjamin = [];
        if (!$isDepToParent && !$isDepToChild) {
            $responseDatasPenjamin = $this->getDataPenjaminApi();
            $datasPenjamin = ArrayHelper::map($responseDatasPenjamin, 'penjamin_id', 'penjamin_nama');
        }
        return $this->render('DHSelectPenjamin/index', get_defined_vars());
    }
}

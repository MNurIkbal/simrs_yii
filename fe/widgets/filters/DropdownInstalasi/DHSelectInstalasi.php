<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectInstalasi adalah widget
 * untuk kebutuhan filter data Instalasi
 */

namespace app\widgets\filters\DropdownInstalasi;

use Yii;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;

class DHSelectInstalasi extends DHBaseHtmlWidget
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
    public $instalasiPilihan = ['rajal','fisioterapi']; //default pilihan instalasi jangan diubah ya guys :D

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_ruangan_ranap';
    }

    private function getDataInstalasi()
    {
        $restMaster = Yii::$app->docoRest->master;
        $response = $this->guzzleExec($restMaster, [
            'url' => 'instalasi/get-instalasi-dep',
            'payload' => [
                'form_params' => $this->instalasiPilihan
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
        $responseDatas = $this->getDataInstalasi();
        $datas = ArrayHelper::map($responseDatas, 'instalasi_id', 'instalasi_nama');
        return $this->render('DHSelectInstalasi/index', get_defined_vars());
    }
}

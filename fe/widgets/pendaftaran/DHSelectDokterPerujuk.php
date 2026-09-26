<?php

namespace app\widgets\pendaftaran;

use Yii;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;

class DHSelectDokterPerujuk extends DHBaseHtmlWidget
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
    public $independent = false;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_dokter_perujuk';
    }

    private function getDataDokterPerujuk()
    {
        $response = [];
        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $request = Yii::$app->request;
        $response = $this->guzzleExec($restPendaftaran, [
            'url' => 'allow/get-list-dokter',
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
        $independent = $this->independent;
        $dataTemp = $this->getDataDokterPerujuk();
        $datas = [];
        if($independent == true){ // jika bergantung sendiri (bkn child & bkn parent)
            $datas = ArrayHelper::map($dataTemp, 'pegawai_id', 'nama_pegawai');
            $datas = array_unique($datas);
        }
        return $this->render('DHSelectDokterPerujuk/index', get_defined_vars());
    }
}

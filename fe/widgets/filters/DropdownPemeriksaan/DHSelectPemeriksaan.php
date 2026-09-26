<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectJenisPemeriksaan adalah widget
 * untuk kebutuhan filter data Pemeriksaan
 */

namespace app\widgets\filters\DropdownPemeriksaan;

use Yii;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;

class DHSelectPemeriksaan extends DHBaseHtmlWidget
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
    public $jenisPemeriksaan = 'fisioterapi';
    public $independent = false;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_ruangan_ranap';
    }

    private function getDataPemeriksaan()
    {
        $response = [];
        if($this->jenisPemeriksaan == 'fisioterapi'){
            $restMaster = Yii::$app->docoRest->master;
            $request = Yii::$app->request;
            $response = $this->guzzleExec($restMaster, [
                'url' => 'pemeriksaan-fisioterapi/get-pemeriksaan',
                'payload' => [
                    'form_params' => null
                ]
            ]);
        }
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
        $dataTemp = $this->getDataPemeriksaan();
        $datas = [];
        if($independent == true){ // jika bergantung sendiri (bkn child & bkn parent)
            $datas = ArrayHelper::map($dataTemp, 'daftartindakan_id', 'pemeriksaanfisio_nama');
            $datas = array_unique($datas);
        }
        return $this->render('DHSelectPemeriksaan/index', get_defined_vars());
    }
}

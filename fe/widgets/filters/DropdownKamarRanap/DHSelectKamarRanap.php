<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectKamarRanap adalah widget
 * untuk kebutuhan filter data kamar ranap
 */

namespace app\widgets\filters\DropdownKamarRanap;

use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\helpers\ArrayHelper;
use Yii;
class DHSelectKamarRanap extends DHBaseHtmlWidget
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
        if (!$this->id) $this->id = 'select_widget_kamar_ranap';
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
        $datasKamar = [];
        $dataKey = $this->dataKey;
        return $this->render('DHSelectKamarRanap/index', get_defined_vars());
    }
}

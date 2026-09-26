<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectTempatTidurRanap adalah widget
 * untuk kebutuhan filter data tempat tidur ranap
 */

namespace app\widgets\filters\DropdownTempatTidurRanap;

use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;

class DHSelectTempatTidurRanap extends DHBaseHtmlWidget
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
        if (!$this->id) $this->id = 'select_widget_tempat_tidur_ranap';
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
        $datasTempatTidur = [];
        $dataKey = $this->dataKey;
        return $this->render('DHSelectTempatTidurRanap/index', get_defined_vars());
    }
}

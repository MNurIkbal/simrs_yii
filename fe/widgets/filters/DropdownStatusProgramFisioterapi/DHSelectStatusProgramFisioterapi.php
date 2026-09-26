<?php

/**
 * @author Chacha Nurholis <chacha@sirs.co.id>
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectStatusProgramFisioterapi adalah widget/component
 * untuk kebutuhan filter/option data status Program fisioterapi.
 */

namespace app\widgets\filters\DropdownStatusProgramFisioterapi;

use Yii;
use yii\helpers\ArrayHelper;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;

class DHSelectStatusProgramFisioterapi extends DHBaseHtmlWidget
{
    use ControllerHelperTrait;

    public $id;
    public $className;
    public $prompt;
    public $chosen;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_status_program_fisioterapi';
        if (!$this->prompt) $this->prompt = '-- Pilih Status Program --';
        if (!$this->chosen) $this->chosen = null;
    }

    private function getStatusProgramFisio()
    {
        $restMaster = Yii::$app->docoRest->master;
        $query = null;
        if (!$this->chosen) $query = ['chosen' => $this->chosen];
        $response = $this->guzzleExec($restMaster, [
            'method' => 'GET',
            'url' => 'lookup/get-status-program-fisio',
            'payload' => [
                'query' => $query
            ]
        ]);
        return $response;
    }

    public function run()
    {
        $id = $this->id;
        $className = $this->className;
        $prompt = $this->prompt;
        $responseDatas = $this->getStatusProgramFisio();
        $datas = ArrayHelper::map($responseDatas, 'lookup_id', 'lookup_name');
        return $this->render('DHSelectStatusProgramFisioterapi/index', get_defined_vars());
    }
}

<?php

/**
 * @author Chacha Nurholis <chacha@sirs.co.id>
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectStatusKunjunganFisioterapi adalah widget/component
 * untuk kebutuhan filter/option data status kunjungan fisioterapi.
 */

namespace app\widgets\filters\DropdownStatusKunjunganFisioterapi;

use app\components\DocoConstants;
use Yii;
use yii\helpers\ArrayHelper;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;

class DHSelectStatusKunjunganFisioterapi extends DHBaseHtmlWidget
{
    use ControllerHelperTrait;

    public $id;
    public $className;
    public $prompt;
    public $chosen;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_status_kunjungan_fisioterapi';
        if (!$this->prompt) $this->prompt = '-- Pilih Status Kunjungan --';
        if (!$this->chosen) $this->chosen = [DocoConstants::STATUS_KUNJUNGAN_FISIO_KETIDAKHADIRAN, DocoConstants::STATUS_KUNJUNGAN_FISIO_DROP_OUT];
    }

    private function getStatusKunjunganFisio()
    {
        $restMaster = Yii::$app->docoRest->master;
        $response = $this->guzzleExec($restMaster, [
            'method' => 'GET',
            'url' => 'lookup/get-status-kunjungan-fisio',
            'payload' => [
                'query' => ['chosen' => $this->chosen]
            ]
        ]);
        return $response;
    }

    public function run()
    {
        $id = $this->id;
        $className = $this->className;
        $prompt = $this->prompt;
        $responseDatas = $this->getStatusKunjunganFisio();
        $datas = ArrayHelper::map($responseDatas, 'lookup_id', 'lookup_name');
        return $this->render('DHSelectStatusKunjunganFisioterapi/index', get_defined_vars());
    }
}

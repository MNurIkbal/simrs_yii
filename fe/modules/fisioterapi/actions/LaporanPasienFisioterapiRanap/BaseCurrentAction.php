<?php

namespace Doco\fisioterapi\actions\LaporanPasienFisioterapiRanap;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\components\DocoDatatableHelper;
use app\components\Traits\ControllerHelperTrait;

class BaseCurrentAction extends Action
{
    use ControllerHelperTrait;

    protected function getParamsFiltered()
    {
        $request = Yii::$app->request;
        $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
        $advancedFilter = ArrayHelper::getValue($filter, 'advanced-filter');
        if ($advancedFilter) {
            $dokterPerujukId = ArrayHelper::getValue($advancedFilter, 'dokterperujuk_id');
            if (strtolower($dokterPerujukId) == 'semua') {
                unset($filter['advanced-filter']['dokterperujuk_id']);
            }
            $dokterDpjpId = ArrayHelper::getValue($advancedFilter, 'dokterdpjp_id');
            if (strtolower($dokterDpjpId) == 'semua') {
                unset($filter['advanced-filter']['dokterdpjp_id']);
            }
            $statusProgramFisioId = ArrayHelper::getValue($advancedFilter, 'status_program_fisio_id');
            if (strtolower($statusProgramFisioId) == 'semua') {
                unset($filter['advanced-filter']['status_program_fisio_id']);
            }
            $terapisId = ArrayHelper::getValue($advancedFilter, 'terapis_id');
            if (strtolower($terapisId) == 'semua') {
                unset($filter['advanced-filter']['terapis_id']);
            }
            $jenisPemeriksaanId = ArrayHelper::getValue($advancedFilter, 'jenispemeriksaanfisio_id');
            if (strtolower($jenisPemeriksaanId) == 'semua') {
                unset($filter['advanced-filter']['jenispemeriksaanfisio_id']);
            }
        }
        if (empty($filter['advanced-filter'])) {
            unset($filter['advanced-filter']);
        }
        return $filter;
    }
}

?>
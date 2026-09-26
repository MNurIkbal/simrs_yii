<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\helpers\HandlingValueHelper;
use GuzzleHttp\Exception\RequestException;

class ViewAction extends Action {
    public function run($id, $no_adjusmen = null) {
        $title = 'Detail Adjustment Obat Alkes';
        $adjustment_id = DocoHelpers::decrypt($id);

        $data_adjustment = [];
        $_requestDetailAdjustment = Yii::$app->docoRest->gudang->get('informasi-adjustment-obat-alkes/info-adjustment-detail',[
                                        'query' => [
                                            'no_adjustment' => $no_adjusmen,
                                        ]
                                    ]);
        $_responseDetailAdjustment = json_decode($_requestDetailAdjustment->getBody(),true);
        $data_adjustment = $_responseDetailAdjustment['response']['data'];
        $no_adjustment = HandlingValueHelper::nullValue($data_adjustment['no_adjusmen']);
        $jenis_adjustment = $data_adjustment['jenis_adjusmen'];
        $pegawai_mengetahui = HandlingValueHelper::nullValue($data_adjustment['pegawai_mengetahui']);
        $pegawai_menyetujui = HandlingValueHelper::nullValue($data_adjustment['pegawai_menyetujui']);
        $pegawai_input = HandlingValueHelper::nullValue($data_adjustment['pegawai_adjusmen']);
        $tgl_adjustment = !empty($data_adjustment['tgl_adjusmen']) ? date("d M Y", strtotime($data_adjustment['tgl_adjusmen'])) : '-';

        return $this->controller->render('detail', get_defined_vars());
    }
}
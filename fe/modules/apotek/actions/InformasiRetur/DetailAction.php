<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiRetur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class DetailAction extends Action {
	public $type;
	public $title;

    public function run($id) {
        $request = Yii::$app->request;
        $title = $this->title;
        $type = $this->type;
        $dataObat = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'method' => 'get',
            'url' => 'inf-retur/get-pendaftaran-obat',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => DocoHelpers::decrypt($id),
                    'retur_id' => !empty(DocoHelpers::decrypt($request->get('returresep_id'))) ? DocoHelpers::decrypt($request->get('returresep_id')) : null,
                    'type' => $this->type
                ]
            ]
        ]);

        $isReturPendaftaran = ArrayHelper::getValue($dataObat, 'is_retur_pendaftaran');
        $dataPasien = [
            'id' => $id,
            'no_pendaftaran' => ArrayHelper::getValue($dataObat, 'data.0.no_pendaftaran'),
            'no_rekam_medik' => ArrayHelper::getValue($dataObat, 'data.0.no_rekam_medik'),
            'nama_pasien' => ArrayHelper::getValue($dataObat, 'data.0.nama_pasien'),
            'dokter_dpjp' => ArrayHelper::getValue($dataObat, 'data.0.dokter_dpjp'),
            'ruangan_akhir' => ArrayHelper::getValue($dataObat, 'data.0.instalasi_nama') .' - '. ArrayHelper::getValue($dataObat, 'data.0.ruangan_nama'),
        ];
        $statusRetur = ArrayHelper::getValue($dataObat, 'data.0.status_retur');
        $tglRetur = !empty(ArrayHelper::getValue($dataObat, 'data.0.tgl_retur')) ? date('d-m-Y H:i', strtotime(ArrayHelper::getValue($dataObat, 'data.0.tgl_retur')))  : 0;
        
        $tmptransaksikosong = [];
        $transaksikosong = [];
        $tmpData = [];
        if($type != 'tambah'){  
            foreach ($dataObat['data'] as $obat => $value){
                if($value['status_retur'] == DocoConstants::RETUR_BELUM_VERIFIKASI) {
                    $value['qty_retur'] = !empty($value['qty_retur']) ? $value['qty_retur'] : 0;
                    $data[$obat] = $value;
                } else if($value['status_retur'] == DocoConstants::RETUR_VERIFIKASI) {
                    if(!is_null($value['qty_retur'])) {
                        $value['qty_retur'] = !empty($value['qty_retur']) ? $value['qty_retur'] : 0;
                        $data[$obat] = $value;
                    }
                }else if($value['status_retur'] == DocoConstants::BATAL_RETUR) {
                    $value['qty_retur'] = !empty($value['qty_retur']) ? $value['qty_retur'] : 0;
                    $data[$obat] = $value;
                }
            }
            $dataObat['data'] = $data;
        }
        
        $retur = !empty($request->get('returresep_id')) ? $request->get('returresep_id') : null;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

        $ruanganFarmasi = $this->controller->guzzleExec(Yii::$app->docoRest->apotek, [
            'method' => 'get',
            'url' => 'allow/get-ruangan-by',
            'payload' => [
                'query' => [
                    'id' => DocoConstants::INSTALASI_FARMASI
                ]
            ]
        ]);
        
        $ruanganFarmasi = ArrayHelper::map($ruanganFarmasi, 'ruangan_id', 'ruangan_nama');
        
        $dataObat = ArrayHelper::index(ArrayHelper::getValue($dataObat, 'data'), null, 'transaksi');
        $dataResep = ArrayHelper::getValue($dataObat, 'resep');
        $dataBmhp = ArrayHelper::getValue($dataObat, 'bmhp');
        $hasAccessVerif = DocoHelpers::checkButtonAccess('/apotek/informasi-retur', 'verifikasi');

        return $this->controller->render('detail', get_defined_vars());
    }
}
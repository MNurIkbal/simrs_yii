<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseOrderManual;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\Pajak;
use app\modules\v1\models\Supplier;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoConstants;

class GenerateApiAction extends Action {
    public function run($pegawai_id) {
        $instalasi = Instalasi::find()
                        ->where([
                            'is_active' => true, 
                            'instalasi_id' => [DocoConstants::INSTALASI_GUDANG_UMUM,DocoConstants::INSTALASI_GUDANG_FARMASI]
                        ])->all();
        $pegawai = Pegawai::find()->where(['is_active' => true])->all();
        $payterm = Payterm::find()->where(['is_active' => true])->all();
        $pajak = Pajak::find()->where(['is_active' => true])->all();
        $supplier = Supplier::find()->where(['is_active' => true])->all();
        $pegawaiLogin = Pegawai::findOne($pegawai_id);

        return [
            'instalasi' => ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'),
            'pegawai' => ArrayHelper::map($pegawai, 'pegawai_id', 'nama_pegawai'),
            'payterm' => ArrayHelper::map($payterm, 'payterm_id', 'payterm_nama'),
            'pajak' => ArrayHelper::map($pajak, 'pajak_id', 'pajak_name'),
            'supplier' => ArrayHelper::map($supplier, 'supplier_id', 'supplier_nama'),
            'pegawaiLogin' => ($pegawaiLogin) ? $pegawaiLogin->nama_pegawai : '',
            'mapValue' => ArrayHelper::map($pajak, 'pajak_id', 'pajak_persen'),
        ];
    }
}
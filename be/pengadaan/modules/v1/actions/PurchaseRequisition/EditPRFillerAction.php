<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\InfoPurchaseRequisition;
use app\modules\v1\models\InfoPurchaseRequisitionBarang;
use app\modules\v1\models\InfoPurchaseReqDetailView;
use app\modules\v1\models\InfoPurchaseReqBarangDetailView;
use app\modules\v1\models\InfoSatuanKonversi;
use app\modules\v1\models\LookupTransaksi;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class EditPRFillerAction extends Action {

    public $rid;

    public function run($id, $type, $rid = null) {
        $this->rid = $rid;
        try{
            if($type == 'obat') {
                $data = $this->medis($id);
            } else {
                $data = $this->nonMedis($id);
            }

            return [
                'data' => [
                    'header' => $data['header'],
                    'detail' => $data['detail'],
                    'konversi' => $data['konversi'],
                    'satuan' => $data['satuan'],
                    'hasil_konversi' => $data['hasil_konversi']
                ]
            ];
        }catch(\Exception $e){
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function medis($id) {
        $connection = Yii::$app->db;
        $header = InfoPurchaseRequisition::find()
                    ->where(['purchasereq_id'=>$id])
                    ->asArray()->one();
        if(is_null($header))
            throw new \Exception("Data dengan id:{$id} tidak ditemukan", 1);

        $detail = InfoPurchaseReqDetailView::find()
                    ->where(['purchasereq_id'=>$id])
                    ->orderBy([
                      'obatalkes_nama' => SORT_ASC
                    ])
                    ->asArray()->all();

        $arr_obatalkes_id = ArrayHelper::getColumn($detail, 'obatalkes_id');

        $getLookupTransaksi = LookupTransaksi::find()
                                ->select(['kode_transaksi', 'kode_id'])
                                ->where(['in', 'kode_transaksi', ['farmasi_utama', 'gudang_farmasi']])
                                ->asArray()
                                ->all();

        if(empty($getLookupTransaksi)) {
            throw new \Exception("Data ruangan farmasi dan gudang farmasi tidak terdaftar.", 1);
        }

        $arrLookupTransaksi = ArrayHelper::index($getLookupTransaksi, 'kode_transaksi');
        $farmasi_utama = $arrLookupTransaksi['farmasi_utama']['kode_id'];
        $gudang_farmasi = $arrLookupTransaksi['gudang_farmasi']['kode_id'];

        
        $temp = [];
        foreach ($arr_obatalkes_id as $ko => $vo) {
            $query = "  SELECT
                            SUM(CASE WHEN sr.ruangan_id = {$this->rid} THEN qty_stok ELSE 0 END) AS qty_tersedia,
                            SUM(CASE WHEN sr.ruangan_id = {$farmasi_utama} THEN qty_stok ELSE 0 END) AS stok_farmasi,
                            SUM(CASE WHEN sr.ruangan_id = {$gudang_farmasi} THEN qty_stok ELSE 0 END) AS stok_gudang,
                            SUM(CASE WHEN sr.ruangan_id not in ({$farmasi_utama}, {$gudang_farmasi}) THEN qty_stok ELSE 0 END) AS stok_lain
                        FROM ketersediaanobat_v sr
                        WHERE obatalkes_id = {$vo}";
            $temp[$vo] = $connection->createCommand($query)->queryOne();
        }


        foreach ($detail as $key => $value) {
            // $detail[$key]['stok_saatini'] = is_null($value['stok']) ? '-' : $value['stok'] . ' ' . $value['satuan'];
            $detail[$key]['doi'] = is_null($value['doi']) ? '-' : $value['doi'];
            $detail[$key]['ssmin'] = is_null($value['ssmin']) ? '-' : $value['ssmin'] . ' ' . $value['satuan'];

            //ambil stok dari view ketersediaanobat_v
            $detail[$key]['stok_saatini'] = is_null($temp[$value['obatalkes_id']]['qty_tersedia']) ? '-' : $temp[$value['obatalkes_id']]['qty_tersedia'];
            // $detail[$key]['stok_farmasi'] = is_null($temp[$value['obatalkes_id']]['stok_farmasi']) ? '-' : $temp[$value['obatalkes_id']]['stok_farmasi'] . ' ' . $value['satuan'];
            // $detail[$key]['stok_gudang'] = is_null($temp[$value['obatalkes_id']]['stok_gudang']) ? '-' : $temp[$value['obatalkes_id']]['stok_gudang'] . ' ' . $value['satuan'];
            // $detail[$key]['stok_ruanganlain'] = is_null($temp[$value['obatalkes_id']]['stok_lain']) ? '-' : $temp[$value['obatalkes_id']]['stok_lain'] . ' ' . $value['satuan'];

        }

        $satuanKonversi = InfoSatuanKonversi::find()->where([
            'jenis' => 'obat',
            'obatalkes_id' => $arr_obatalkes_id
        ])->all();

        $konversi = $hasilKonversi = [];
        $nilaiDefault = $satuan = [];
        foreach ($satuanKonversi as $value) {
            $konversi[$value['obatalkes_id']][$value['satuanbesar_id']] = '1 ' . $value['satuan_besar'] . ' = ' . $value['nilai_konversi'] . ' '
                . $value['satuan_kecil'];
            if ($value['satuanbesar_id'] == $value['satuankecil_id']) {
                $nilaiDefault[$value['obatalkes_id']] = $value['satuanbesar_id'];
            }
            $satuan[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['satuan_besar'];
            $hasilKonversi[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
        }

        return [
            'header' => $header,
            'detail' => $detail,
            'konversi' => $konversi,
            'satuan' => $satuan,
            'hasil_konversi' => $hasilKonversi
        ];
    }

    private function nonMedis($id) {
        $header = InfoPurchaseRequisitionBarang::find()
                    ->where(['purchasereqbrg_id'=>$id])
                    ->asArray()->one();
        if(is_null($header))
            throw new \Exception("Data dengan id:{$id} tidak ditemukan", 1);

        $detail = InfoPurchaseReqBarangDetailView::find()
                    ->where(['purchasereqbrg_id'=>$id])
                    ->orderBy([
                      'barang_nama' => SORT_ASC
                    ])
                    ->asArray()->all();

        $arr_obatalkes_id = ArrayHelper::getColumn($detail, 'barang_id');

        foreach ($detail as $key => $value) {
            $detail[$key]['stok_saatini'] = is_null($value['stok']) ? '-' : $value['stok'] . ' ' . $value['satuan'];
            $detail[$key]['doi'] = is_null($value['doi']) ? '-' : $value['doi'] . ' ' . $value['doi'];
            $detail[$key]['ssmin'] = is_null($value['ssmin']) ? '-' : $value['ssmin'] . ' ' . $value['ssmin'];
            $detail[$key]['qty_sugesstion'] = is_null($value['qty_sugesstion']) ? '-' : $value['qty_sugesstion'] . ' ' . $value['qty_sugesstion'];
        }

        $satuanKonversi = InfoSatuanKonversi::find()->where([
            'jenis' => 'barang',
            'obatalkes_id' => $arr_obatalkes_id
        ])->all();

        $konversi = $hasilKonversi = [];
        $nilaiDefault = $satuan = [];
        foreach ($satuanKonversi as $value) {
            $konversi[$value['obatalkes_id']][$value['satuanbesar_id']] = '1 ' . $value['satuan_besar'] . ' = ' . $value['nilai_konversi'] . ' '
                . $value['satuan_kecil'];
            if ($value['satuanbesar_id'] == $value['satuankecil_id']) {
                $nilaiDefault[$value['obatalkes_id']] = $value['satuanbesar_id'];
            }
            $satuan[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['satuan_besar'];
            $hasilKonversi[$value['obatalkes_id']][$value['satuanbesar_id']] = $value['nilai_konversi'];
        }

        return [
            'header' => $header,
            'detail' => $detail,
            'konversi' => $konversi,
            'satuan' => $satuan,
            'hasil_konversi' => $hasilKonversi
        ];
    }
}

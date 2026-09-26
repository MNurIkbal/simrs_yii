<?php

namespace app\modules\v1\businessLogic;

/**
** @author yaya
**/

use Yii;
use app\modules\v1\models\FormSoBarang;
use app\modules\v1\models\FormSoBarangDetail;

class FormulirStokOpname
{

    /**
    * @var $data harus array [data asal dari infostokobatdetail_v yang sudah di filter]
    * @return array
    **/
    public static function excecute(array $data)
    {
        $request = Yii::$app->request;
        $totalVolume = $totalHarga = 0;
        $insertDetail = [];
        $namaRuangan = '';
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($data as $value) {
                $stokSistem = $value['stok_sistem'] > 0 ? $value['stok_sistem'] : 0;
                $hargaNetto = $value['barang_harganetto'] ? $value['barang_harganetto'] : 0;
                $totalVolume += $stokSistem;
                $totalHarga += $stokSistem * $hargaNetto;
                $namaRuangan = $value['ruangan_nama'];
                $row = [
                    'barang_id' => $value['barang_id'],
                    'formsobarang_id' => '',
                    'stok' => $value['stok_sistem'],
                    'harganetto' => $value['barang_harganetto'],
                    'periodestok_id' => $value['periodestokbarang_id'],
                    'ruangan_id' => $value['ruangan_id'],
                    'nobatch' => $value['nobatch'],
                    'stokbarang_id' => $value['id_stok'],
                    'ruangan_id' => $request->get('ruangan_id')
                ];
                $insertDetail[] = $row;
            }
            if (!$insertDetail) {
                return [
                    'text' => 'Data tidak ada',
                    'status' => 422
                ];
            }
            $parent = [
                'tglformulir' => date('Y-m-d'),
                'total_harganetto' => $totalHarga,
                'noformulir' => 'by-trigered',
                'ruangan_id' => $request->get('ruangan_id')
            ];

            $stokOpname = new FormSoBarang;
            $stokOpname->attributes = $parent;
            
            if ($stokOpname->save()) {
                $idParent = $stokOpname->formsobarang_id;
                foreach ($insertDetail as $key => $value) {
                    $insertDetail[$key]['formsobarang_id'] = $idParent;
                }
                $result = FormSoBarangDetail::batchInsert($insertDetail, false);
                $transaction->commit();
                $getModel = FormSoBarang::find()->where([
                    'formsobarang_id' => $idParent
                ])->one();
                $periode = isset($_GET['periode_stok']) ? $_GET['periode_stok'] : '';
                return [
                    'id_parent' => $idParent,
                    'periode' => $periode,
                    'ruangan_nama' => $namaRuangan,
                    'no_formulir' => $getModel->noformulir,
                    'messages' => 'success',
                    'data' => $data
                ];
            } else {
                return [
                    'data' => $stokOpname->errors,
                    'status' => 422
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'messages' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

}
<?php

/**
 * @author : ilham
 * Powered by Sirs
 */

namespace Extensions\gizi;

use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoPermintaanMakanView;
use app\modules\v1\models\InfoPermintaanMakanDetailView;
use Doco\models\LookupTransaksi;


class PrintLabelMakananMhbg extends \Doco\components\DocoBaseProcessExtension

{
	protected function processFlow() 
    {
        try {
                $request = Yii::$app->request;
                $permintaaanmakan_id = $request->post('permintaaanmakan_id', null);
                $jumlah = $request->post('jumlah', 1);
                $jumlah = (empty($jumlah) || $jumlah == 0) ? 1 : $jumlah;
                if(!$permintaaanmakan_id) {
                    throw new \yii\base\ErrorException("ID Permintaan Makan Tidak Ditemukan", 500);
                }
    
                $data = $this->actionGetPermintaanMakanDetail($permintaaanmakan_id);
                if(!$data){
                    throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
                }
                $permintaan_makan        = $data['permintaan_makan'];
                $permintaan_makan_detail = [];
                $permintaan_makan_detail = !empty($data['permintaan_makan_detail'][0]) ? $data['permintaan_makan_detail'][0] : '';
    
                $lookup_font_makanan = LookupTransaksi::find()
                ->select(['additional_value'])
                ->where(['kode_transaksi' => 'ukuran_font_label_makanan_mhbg'])->asArray()->one();
                $setup = json_decode($lookup_font_makanan['additional_value']);;
    
                $print = new DocoPrint('label-makanan-mhbg');
                $print->attributes = [
                    '#dataLabel#' => Yii::$app->controller->renderPartial('_print_makanan_label_mhbg', [
                        'kamar'  => isset($permintaan_makan['kamarruangan_nokamar']) ? $permintaan_makan['kamarruangan_nokamar'] : ' - ',
                        'nama_pasien'  => isset($permintaan_makan['nama_pasien']) ? $permintaan_makan['nama_pasien'] : ' - ',
                        'tgl'  => isset($permintaan_makan['tanggal_lahir']) ? date('m-Y-d', strtotime($permintaan_makan['tanggal_lahir'])) : ' - ',
                        'norm'  => isset($permintaan_makan['no_rekam_medik']) ? $permintaan_makan['no_rekam_medik'] : ' - ',
                        'jk'  => isset($permintaan_makan['jenis_kelamin']) ? $permintaan_makan['jenis_kelamin'] : ' - ',
                        'umur'  => isset($permintaan_makan['umur']) ? trim(str_replace('Hari', 'Hr',str_replace('Bulan', 'Bln',str_replace('Tahun', 'Thn', $permintaan_makan['umur'])))) : ' - ',
                        'catatan'  =>  isset($permintaan_makan_detail['keterangan']) ? $permintaan_makan_detail['keterangan'] : ' - ',
                        'jumlah' => $jumlah,
                        'tgl_cetak' =>  date('d F Y'),
                        'detail'  => $permintaan_makan_detail,
                        'body_font' => $setup,
                    ])
                ];
                
                return $print->Output();
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetPermintaanMakanDetail($id)
    {
        $model = InfoPermintaanMakanView::find()->where(['permintaaanmakan_id' => $id])->one();
        $model_detail = InfoPermintaanMakanDetailView::find()->where(['permintaaanmakan_id' => $id])->asArray()->all();

        if ($model->tgl_permintaanmakan != '') {
            $model->tgl_permintaanmakan = DocoHelpers::convDateTime($model->tgl_permintaanmakan, false, true);
        }

        if ($model->tgl_pendaftaran != '') {
            $model->tgl_pendaftaran = DocoHelpers::convDateTime($model->tgl_pendaftaran, false, true);
        }

        if ($model->waktu_pembatalan != '') {
            $model->waktu_pembatalan = DocoHelpers::convDateTime($model->waktu_pembatalan, false, true);
        }

        return [
            'permintaan_makan' => $model,
            'permintaan_makan_detail' => $model_detail,
        ];
    }

}
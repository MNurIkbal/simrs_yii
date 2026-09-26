<?php

/**
 * @author : ilham.pramono
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoPermintaanMakanView;
use app\modules\v1\models\InfoPermintaanMakanDetailView;
use Doco\models\LookupTransaksi;

class PrintLabelMakananProcess extends \Doco\components\DocoBaseProcessExtension
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
            $permintaan_makan        = $data['permintaan_makan'];
            $permintaan_makan_detail = [];
            $permintaan_makan_detail = !empty($data['permintaan_makan_detail'][0]) ? $data['permintaan_makan_detail'][0] : '';

            $ary = [];
            $count = 0;
            $multiple = [];


            if(!$data){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }


            $lookup_font_makanan = LookupTransaksi::find()
            ->select(['additional_value'])
            ->where(['kode_transaksi' => 'ukuran_font_label_makanan'])->asArray()->one();
            $setup = json_decode($lookup_font_makanan['additional_value']);;
            $sum = $jumlah * $count;
            $print = new DocoPrint();
            $print->attributes = [
                '#dataLabel#' => Yii::$app->controller->renderPartial('_print_makanan_label', [
                    'header'  => $permintaan_makan,
                    'detail'  => $permintaan_makan_detail,
                    'jumlah' => $jumlah,
                    'tgl_cetak' =>  date('d F Y'),
                    'header_font' => $setup[0],
                    'body_font' => $setup[1],
                    'margin_top_1' => $setup[2],
                    'margin_top_2' => $setup[3],
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
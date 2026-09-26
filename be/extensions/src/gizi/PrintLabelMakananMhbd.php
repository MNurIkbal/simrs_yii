<?php

/**
 * @author : Budi
 * Powered by Sirs
 */

namespace Extensions\gizi;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfoPermintaanMakanView;
use app\modules\v1\models\InfoPermintaanMakanDetailView;
use Doco\models\LookupTransaksi;

class PrintLabelMakananMhbd extends \Doco\components\DocoBaseProcessExtension
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

         $permintaanMakan       = ArrayHelper::getValue($data, 'permintaan_makan', []);
         $permintaanMakanDetail = ArrayHelper::getValue($data, 'permintaan_makan_detail', []);
         $permintaanMakanDetail = ArrayHelper::getValue($permintaanMakanDetail, 0);
         $tanggalLahir = ArrayHelper::getValue($permintaanMakan, 'tanggal_lahir');
         $umur = ArrayHelper::getValue($permintaanMakan, 'umur');
         if(!empty($tanggalLahir)) {
            $tanggalLahir = date('d-m-Y', strtotime($tanggalLahir));
         }
         if(!empty($umur)) {
            $umur = trim(str_replace('Hari', 'Hr',str_replace('Bulan', 'Bln',str_replace('Tahun', 'Thn', $umur))));
         }

         $lookupFontMakanan = LookupTransaksi::find()
            ->select(['additional_value'])
            ->where(['kode_transaksi' => 'ukuran_font_label_makanan_mhbg'])->asArray()->one();
         
         $additionalValue = ArrayHelper::getValue($lookupFontMakanan, 'additional_value');
         $setup = [];
         if(!empty($additionalValue)) {
            $setup = json_decode($additionalValue, true);
         }
         
         $ruanganNama = '-';
         $ruangan = ArrayHelper::getValue($permintaanMakan, 'ruangan_nama');
         $kamar = ArrayHelper::getValue($permintaanMakan, 'kamarruangan_nokamar');
         if(!empty($ruangan) && !empty($kamar)) {
            $ruanganNama = $ruangan.'/'.$kamar;
         }

         $waktuDiet = ArrayHelper::getValue($permintaanMakanDetail, 'waktu'); 
         $labelWaktuDiet = $this->getWaktuPemberianLabel($waktuDiet);
         $catatanDiet = ArrayHelper::getValue($permintaanMakanDetail, 'keterangan');
         $print = new DocoPrint('label-makanan-mhbd');
         $print->attributes = [
            '#dataLabel#' => Yii::$app->controller->renderPartial('_print_makanan_label_mhbd', [
               'ruangan'  => $ruanganNama,
               'nama_pasien'  => ArrayHelper::getValue($permintaanMakan, 'nama_pasien'),
               'tgl'  => !empty($tanggalLahir) ? $tanggalLahir : ' - ',
               'norm'  => ArrayHelper::getValue($permintaanMakan, 'no_rekam_medik'),
               'jk'  => ArrayHelper::getValue($permintaanMakan, 'jenis_kelamin'),
               'umur'  => !empty($umur) ? $umur : ' - ',
               'catatan'  => $catatanDiet,
               'jumlah' => $jumlah,
               'tgl_cetak' =>  date('d F Y'),
               'detail'  => $permintaanMakanDetail,
               'diet' => ArrayHelper::getValue($permintaanMakanDetail, 'jenisdiet_nama'),
               'agama' => ArrayHelper::getValue($permintaanMakan, 'agama'),
               'header_font' => ArrayHelper::getValue($setup, 0),
               'body_font' => ArrayHelper::getValue($setup, 1),
               'margin_top_1' => ArrayHelper::getValue($setup, 2),
               'margin_top_2' => ArrayHelper::getValue($setup, 3),
               'labelWaktuDiet' => $labelWaktuDiet
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

   private function getWaktuPemberianLabel($waktuDiet)
   {
      if(!empty($waktuDiet)){
         switch ($waktuDiet) {
            case 'Pagi':
               return 'Breakfast';
               break;
            case 'Siang':
               return 'Lunch';
               break;
            case 'Sore':
               return 'Dinner';
               break;
            case 'Malam':
               return 'Dinner';
               break;
            default:
               return 'Breakfast/Lunch/Dinner';
               break;
         }
      }
      return 'Breakfast/Lunch/Dinner';
   }
}
<?php

/**
 * @author : Budi
 * Powered by Sirs
 */

namespace Extensions\bedah;

use Yii;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use SirsCore\models\InfoPasienOperasiView;
use SirsCore\models\TimOperasiView;
use SirsCore\models\TarifBedah;
use SirsCore\models\DaftarTindakan;

class TarifAkomodasiOtMhbg extends \Doco\processes\TarifAkomodasiOtProcess
{
   const TIPE_TINDAKAN = 'tindakan';
   protected function getTarifMhbg()
   {
      $pasienpenunjangId = $this->_requestData->get('pasienpenunjangId', null);
      if($this->_requestData->isPost) {
         $pasienpenunjangId = $this->_requestData->post('pasienpenunjangId', null);
      }
      $pasienOperasi = $this->getPasienOperasi($pasienpenunjangId);
      $kelasPelayananId = $pasienOperasi['kelaspelayanan_id'];
      $timOperasi = $this->getTimOperasi($pasienpenunjangId);
      $daftarTindakanId = $timOperasi['daftarTindakanId'];
      return $this->getAkomodasi($kelasPelayananId, $daftarTindakanId);
   }

   protected function getAkomodasi($kelasPelayananId, $daftarTindakanId)
   {
      $result = [];
      $tarif = 0;
      $conn = Yii::$app->db;
      $arr = [];
      if(!empty($daftarTindakanId)) {
         foreach ($daftarTindakanId as $key => $value) {
            $arr[] = $value;
         }
         $listTindakanId = "(" . implode(",", $arr) . ")";
         $sql = "SELECT tarifbedah_m.tarifbedah_id, tarifbedah_m.kelaspelayanan_id, tarifbedah_m.tarif, 
            tarifbedah_m.persen_cyto, tarifbedah_m.kegiatanoperasi_id, operasi_m.daftartindakan_id, 
            daftartindakan_m.daftartindakan_nama
         FROM tarifbedah_m
         INNER JOIN operasi_m ON tarifbedah_m.kegiatanoperasi_id = operasi_m.kegiatanoperasi_id
         INNER JOIN daftartindakan_m ON operasi_m.daftartindakan_id = daftartindakan_m.daftartindakan_id 
         WHERE tarifbedah_m.kelaspelayanan_id = {$kelasPelayananId} AND tarifbedah_m.is_deleted = FALSE AND operasi_m.daftartindakan_id IN {$listTindakanId}";
         $dataTarif = $conn->createCommand($sql)->queryAll();
         $tindakanAkomodasi = DaftarTindakan::find()->where(['is_akomodasi' => true])->one();
         if(!empty($dataTarif)) {
            $maxTarif = $this->getMaxTarif($dataTarif);
            foreach ($dataTarif as $key => $value) {
               if($maxTarif[0] == $value['tarifbedah_id']) {
                  $value['daftartindakan_id'] = $tindakanAkomodasi['daftartindakan_id'];
                  $value['daftartindakan_nama'] = $tindakanAkomodasi['daftartindakan_nama'];
                  $value['perhitungan_durasi'] = false;
                  $result = $value;
               }
            }
         }
      }
      return $result;
   }

   protected function getTimOperasi($pasienpenunjangId)
   {
      $isCyto = false;
      $isPenyulit = false;
      $daftarTindakanId = $tindakanPegawai = $rawdata = [];
      $posisiDokterOperator = DocoConstants::TIM_OPERASI_DOKTER_BEDAH;
      $data = TimOperasiView::find()
         ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
         ->asArray()->all();

      foreach($data as $tim){
         $is_cyto = isset($tim['is_cyto']) ? $tim['is_cyto'] : false;
         $is_penyulit = isset($tim['is_penyulit']) ? $tim['is_penyulit'] : false;
         if( $is_cyto ){
            $isCyto = $is_cyto;
         }
         if($is_penyulit){
            $isPenyulit = $is_penyulit;
         }
         if(!in_array($tim['daftartindakan_id'], $daftarTindakanId) ){
            $daftarTindakanId[] = $tim['daftartindakan_id'];
         }
         $tindakanPegawai[$tim['dokter_id']][] = $tim['daftartindakan_id'];
         if($tim['posisi_operasi'] == $posisiDokterOperator) {
            $rawdata[self::TIPE_TINDAKAN][] = [
               'pasienmasukpenunjang_id' => $pasienpenunjangId,
               'operasi_nama' => $tim['operasi_nama'],
               'posisi_operasi' => $tim['posisi_tim'],
               'golonganoperasi_nama' => $tim['jenisoperasi_nama'],
               'nama_pegawai' => $tim['nama_pegawai'],
               'pegawai_input' => $tim['created_by'],
               'dokter_id' => $tim['dokter_id'],
               'perawat_id' => null,
               'tipepaket_id' => null,
               'daftartindakan_id' => $tim['daftartindakan_id'],
               'is_cyto' => $is_cyto,
               'is_penyulit' => $is_penyulit,
               'qty' => 1,
               'harga' => $tim['harga_operasi'],
               'daftartindakan_nama' => $tim['daftartindakan_nama'],
               'timoperasi_id' => $tim['timoperasi_id'],
               'persentase' => $tim['persentase'],
               'posisi_tim' => $tim['posisi_tim'],
               'useprice' => true,
               'kegiatanoperasi_nama' => isset($tim['kegiatanoperasi_nama']) ? $tim['kegiatanoperasi_nama'] : '-',
            ];
         }
      }
      return [
         'rawdata' => $rawdata,
         'tindakanPegawai' => $tindakanPegawai,
         'daftarTindakanId' => $daftarTindakanId,
      ];
   }

   protected function getPasienOperasi($pasienpenunjangId)
   {
      return InfoPasienOperasiView::find()
         ->select([
                  'ruangan_id',
                  'penjamin_id',
                  'kelaspelayanan_id',
                  'kamarruangan_id',
                  'kamarruangan_nama',
         ])
         ->where(['pasienmasukpenunjang_id' => $pasienpenunjangId])
         ->asArray()
         ->one();
   }

   protected function getMaxTarif($dataTarif)
   {
      $listArr = [];
      if(!empty($dataTarif)) {
         foreach ($dataTarif as $key => $value) {
            $listArr[$value['tarifbedah_id']] = $value['tarif'];
         }
      }
      return array_keys($listArr, max($listArr));
   }

   protected function processFlow()
   {
      return $this->getTarifMhbg();
   }
}

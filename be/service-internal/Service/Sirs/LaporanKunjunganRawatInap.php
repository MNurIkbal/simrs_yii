<?php

namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Service\Sirs\Cache\Cache;
use Integrasi\Service\Sirs\Models\LapKunjunganRawatInap;
use yii\helpers\ArrayHelper;

class LaporanKunjunganRawatInap extends \Integrasi\Contracts\DocoImplement
{

   const TGL_PENDAFTARAN = 'tgl_pendaftaran';
   const INFO_KUNJUNGAN = 'info_kunjungan';
   const STATUS_PASIEN = 'status_pasien';
   const JENIS_KELAMIN = 'jenis_kelamin';
   const UMUR = 'umur';
   const GOLONGAN_UMUR = 'golonganumur_nama';
   const AGAMA = 'agama';
   const STATUS_PERKAWINAN = 'statusperkawinan';
   const PEKERJAAN = 'pekerjaan_nama';
   const ALAMAT = 'alamat_pasien';
   const KOTA_KAB = 'kabupaten_nama';
   const KUNJUNGAN = 'kunjungan';
   const JENIS_KASUS_PENYAKIT = 'jeniskasuspenyakit_nama';
   const CARA_BAYAR = 'carabayar_penjamin';
   const RUJUKAN = 'nama_perujuk';
   const RUANGAN = 'ruangan_nama';
   const KAMAR_BED = 'kamar_bed';
   const KAMAR = 'kamarruangan_id';
   const TEMPAT_TIDUR = 'kamartempattidur_id';
   const DOKTER = 'nama_pegawai';
   const KELAS_PELAYANAN = 'kelaspelayanan_nama';
   const NO_SEP = 'nosep';
   const STATUS_DIPERIKSA = 'status_ranap_nama';
   const STATUS_PULANG = 'carakeluar_nama';
   const DIAGNOSA = 'diagnosa';
   const TANGGAL_KELUAR = 'tgl_keluar';
   const STRTOLOWER = 'strtolower';
   const STRTOUPPER = 'strtoupper';
   const UCFIRST = 'ucfirst';
   const UCWORD = 'ucword';
   const NOSEP = 'nosep';

    public function execute()
    {
        $date = $this->advance_filter['tgl_pendaftaran'];
        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:00');
        
        if(!empty($date)) {
            $explode = explode(" - ", $date);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
        }
        $data = $this->generateData($start,$end);
        $pdata = [
            'status' => 'finish',
            'data' => $data['data'],
            'header' => $this->generateHeaderColumns()
        ];
        
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'kunjungan-rawat-inap:'.$this->unique_str,
            'message' => json_encode($pdata),
        ]);

        return json_encode([
            'service' => 'Sirs-LaporanKunjunganRawatInap',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function generateData($start = null, $end = null)
    {
      $advancedFilters = $this->advance_filter;

      $model = new LapKunjunganRawatInap;
      $query = $model::find()
        ->select([
            'tgl_pendaftaran',
            'no_pendaftaran',
            'no_rekam_medik',
            'nama_pasien',
            'jenis_kelamin',
            'umur',
            'golonganumur_id',
            'golonganumur_nama',
            'agama',
            'statusperkawinan',
            'pekerjaan_nama',
            'alamat_pasien',
            'kabupaten_nama',
            'kunjungan',
            'jeniskasuspenyakit_nama',
            'carabayar_id',
            'carabayar_nama',
            'penjamin_nama',
            'nama_perujuk',
            'ruangan_nama',
            'kamarruangan_nokamar',
            'no_tempattidur',
            'nama_pegawai',
            'kelaspelayanan_nama',
            'kelas_ditagihkan_nama',
            'status_ranap_nama',
            'carakeluar_nama',
            'diagnosa',
            'tgl_keluar',
            'is_pasientitipan',
            'is_pasientitipan_pk',
            'nosep',
            'status_pasien'

        ]);
      // $start = date('Y-m-d 00:00:00');
      // $end = date('Y-m-d 23:59:59');

      $filterDate = 'tgl_pendaftaran';
      $orderBy = 'pendaftaran_id';

      if(!empty($advancedFilters)) {

          if(isset($advancedFilters['carabayar_id']) &&  $advancedFilters['carabayar_id']!='') {
              $query->andWhere(['carabayar_id' => $advancedFilters['carabayar_id']]);
          }

          if(isset($advancedFilters['penjamin_id']) &&  $advancedFilters['penjamin_id']!='') {
              $term = $advancedFilters['penjamin_id'];
              $query->andWhere(['penjamin_id' => $term]);
          }

          if(isset($advancedFilters['ruangan_id']) &&  $advancedFilters['ruangan_id']!='') {
              $term = $advancedFilters['ruangan_id'];
              $query->andWhere(['ruangan_id' => $term]);
          }

          if(isset($advancedFilters['kamar_id']) &&  $advancedFilters['kamar_id']!='') {
              $term = $advancedFilters['kamar_id'];
              $query->andWhere(['kamarruangan_id' => $term]);
          }

          if(isset($advancedFilters['tempattidur_id']) &&  $advancedFilters['tempattidur_id']!='') {
              $term = $advancedFilters['tempattidur_id'];
              $query->andWhere(['kamartempattidur_id' => $term]);
          }

          if(isset($advancedFilters['pegawai_id']) &&  $advancedFilters['pegawai_id']!='') {
              $term = $advancedFilters['pegawai_id'];
              $query->andWhere(['pegawai_id' => $term]);
          }
          if(isset($advancedFilters['nosep']) &&  $advancedFilters['nosep']!='') {
              $term = $advancedFilters['nosep'];
              $query->andWhere(['nosep' => $term]);
          }

      }

      $query->andWhere(['between', new \yii\db\Expression('(tgl_pendaftaran::date)'), $start, $end]);
      $query->orderBy([$orderBy => SORT_DESC]);
      $model = $query->asArray()->all();
      $no = 0;
      $result = [];
      foreach($model as $key => $value) {
             $no++;
             $value['no'] = $no;
             $no_pendaftaran = !empty(ArrayHelper::getValue($value,'no_pendaftaran')) ? ArrayHelper::getValue($value,'no_pendaftaran') : '-';
             $no_rekam_medik = !empty(ArrayHelper::getValue($value,'no_rekam_medik')) ? ArrayHelper::getValue($value,'no_rekam_medik') : '-';
             $nama_pasien = !empty(ArrayHelper::getValue($value,'nama_pasien')) ? ArrayHelper::getValue($value,'nama_pasien') : '-';
             $info_kunjungan = $no_pendaftaran .' - '.$no_rekam_medik.' - '.$nama_pasien;
             $value['info_kunjungan'] = $info_kunjungan;
             $status_titipan = ' - ';
             if($value['carabayar_id'] == 6){
                 $status_titipan = ' - ';
             } else if (!empty(ArrayHelper::getValue($value,'is_pasientitipan_pk'))) {
                 if(ArrayHelper::getValue($value,'is_pasientitipan_pk') == true && ArrayHelper::getValue($value,'is_stoppasientitipan') == false){
                     $status_titipan = ArrayHelper::getValue($value,'kelas_ditagihkan_nama');
                 }
             } else if (empty(ArrayHelper::getValue($value,'is_pasientitipan_pk'))) {
                 if($value['is_pasientitipan'] == true && ArrayHelper::getValue($value,'is_stoppasientitipan') == false){
                     $status_titipan = ArrayHelper::getValue($value,'kelas_ditagihkan_nama');
                 }
             }
             $kelaspelayanan_nama =  ArrayHelper::getValue($value,'kelaspelayanan_nama').' / '.$status_titipan;
             $value['kelaspelayanan_nama'] = $kelaspelayanan_nama;
             $value['carabayar_penjamin'] = ArrayHelper::getValue($value,'carabayar_nama').' / '.ArrayHelper::getValue($value,'penjamin_nama');
             $value['kamar_bed'] = ArrayHelper::getValue($value,'kamarruangan_nokamar').' / '.ArrayHelper::getValue($value,'no_tempattidur');

         $result[] = $value;
     }

      return ['data'=>$result];
  }

  private static function formatToReadable($string , $format = null)
  {
      switch ($string) {
          case self::TGL_PENDAFTARAN:
              $string = 'Tanggal Pendaftaran';
              break;
          case self::INFO_KUNJUNGAN:
              $string = 'Info Kunjungan';
              break;
          case self::STATUS_PASIEN:
              $string = 'Status Pasien';
              break;
          case self::JENIS_KELAMIN:
              $string = 'Jenis Kelamin';
              break;
          case self::UMUR:
              $string = 'Umur';
              break;
          case self::GOLONGAN_UMUR:
              $string = 'Golongan Umur';
              break;
          case self::AGAMA:
              $string = 'Agama';
              break;
          case self::STATUS_PERKAWINAN:
              $string = 'Status Perkawinan';
              break;
          case self::PEKERJAAN:
              $string = 'Pekerjaan';
              break;
          case self::ALAMAT:
              $string = 'Alamat';
              break;
          case self::KOTA_KAB:
              $string = 'Kota/Kab';
              break;
          case self::KUNJUNGAN:
              $string = 'Kunjungan';
              break;
          case self::JENIS_KASUS_PENYAKIT:
              $string = 'Jenis kasus penyakit';
              break;
          case self::CARA_BAYAR:
              $string = 'Cara Bayar/ Penjamin';
              break;
          case self::RUJUKAN:
              $string = 'Rujukan';
              break;
          case self::RUANGAN:
              $string = 'Ruangan';
              break;
          case self::KAMAR_BED:
              $string = 'Kamar Bed';
              break;
          case self::KAMAR:
              $string = 'Kamar';
              break;
          case self::TEMPAT_TIDUR:
              $string = 'Tempat Tidur';
              break;
          case self::DOKTER:
              $string = 'Dokter';
              break;
          case self::KELAS_PELAYANAN:
              $string = 'Kelas Pelayanan / Kelas Tagihan';
              break;
          case self::NO_SEP:
              $string = 'No SEP';
              break;
          case self::STATUS_PULANG:
              $string = 'Status Pulang/Kondisi';
              break;
          case self::STATUS_DIPERIKSA:
              $string = 'Status Diperiksa';
              break;
          case self::DIAGNOSA:
              $string = 'Diagnosa';
              break;
          case self::TANGGAL_KELUAR:
              $string = 'Tanggal Keluar';
              break;
          case self::NOSEP:
              $string = 'No SEP';
              break;
          default:
              $string = $string;
              break;
      }

      switch ($format) {
          case self::UCFIRST:
              $string = ucfirst($string);
              break;
          case self::UCWORD:
              $string = ucwords($string);
              break;
          case self::STRTOUPPER:
              $string = strtoupper($string);
              break;
          case self::STRTOLOWER:
              $string = strtolower($string);
              break;
          default:
              $string = $string;
              break;
      }

      return $string;
  }

    private function generateHeaderColumns() 
    {
      $header = $columns = $tmpCarabayar = $tmpBaru = $tmpLama = $tmpHeader = $newCabar = [];

      $staticHeaderAwal = [
          'no' => 'no',
          self::TGL_PENDAFTARAN => self::TGL_PENDAFTARAN,
          self::INFO_KUNJUNGAN => self::INFO_KUNJUNGAN,
          self::STATUS_PASIEN => self::STATUS_PASIEN,
          self::JENIS_KELAMIN => self::JENIS_KELAMIN,
          self::UMUR => self::UMUR,
          self::GOLONGAN_UMUR => self::GOLONGAN_UMUR,
          self::AGAMA => self::AGAMA,
          self::STATUS_PERKAWINAN => self::STATUS_PERKAWINAN,
          self::PEKERJAAN => self::PEKERJAAN,
          self::ALAMAT => self::ALAMAT,
          self::KOTA_KAB => self::KOTA_KAB,
          self::KUNJUNGAN => self::KUNJUNGAN,
          self::JENIS_KASUS_PENYAKIT => self::JENIS_KASUS_PENYAKIT,
          self::CARA_BAYAR => self::CARA_BAYAR,
          self::RUJUKAN => self::RUJUKAN,
          self::RUANGAN => self::RUANGAN,
          self::KAMAR_BED => self::KAMAR_BED,
          self::DOKTER => self::DOKTER,
          self::KELAS_PELAYANAN => self::KELAS_PELAYANAN,
          self::NO_SEP => self::NO_SEP,
          self::STATUS_DIPERIKSA => self::STATUS_DIPERIKSA,
          self::STATUS_PULANG => self::STATUS_PULANG,
          self::NOSEP => self::NOSEP,
          self::DIAGNOSA => self::DIAGNOSA,
          self::TANGGAL_KELUAR => self::TANGGAL_KELUAR
      ];



      $tmpHeader = array_merge($staticHeaderAwal, $tmpBaru);

      foreach($tmpHeader as $k => $v) {
          $visible = true;
          $search = false;
          $title = $k;


          $columns[] =[
              'title' => self::formatToReadable($title, self::UCWORD),
              'data' => $k,
              'searchable' => $search,
              'orderable' => false,
              'visible' => $visible,
          ];
      }


      return [
          'header' => $tmpHeader,
          'columns' => $columns,
      ];
    }
}
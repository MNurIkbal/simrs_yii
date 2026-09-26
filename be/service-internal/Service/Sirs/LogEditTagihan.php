<?php

namespace Integrasi\Service\Sirs;

use Yii;
use Integrasi\Service\Sirs\Models\LogEditTagihan AS LogTagihan;
use phpDocumentor\Reflection\Types\Array_;
use yii\helpers\ArrayHelper;

class LogEditTagihan extends \Integrasi\Contracts\DocoImplement
{

   protected $payload = [];

   public function execute()
   {
      $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
      $loginPemakaiId = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1';
      $time = date('Y-m-d H:i:s', time());
      $log = $this->log;
      $params = $this->params;
      $listPelayananId = $this->listPelayananId;
      $pendaftaranId = ArrayHelper::getValue($log[0],'pendaftaran_id');
      // $cekDataExist = LogTagihan::find()->andWhere($params)->exists();
      $condUpdate = ['is_deleted' => true, 'is_active' => false, 'deleted_by' => $loginPemakaiId, 'deleted_date' => $time];

      // 1. Pertama cari data yang berubah dari payload yang dikirim 
      // 2. Kalo misalnya ada nya udah ada di db compare dulu datanya
      $hasil = [];
      $newAttributes = [];
      if(!empty($log)) {
         foreach ($log as $key => $value) {
            $pelayananId = ArrayHelper::getValue($value, 'pelayanan_id');
            $harga = ArrayHelper::getValue($value, 'harga');
            $qtyBarang = ArrayHelper::getValue($value, 'qty');
            $hargaOrigin = ArrayHelper::getValue($value, 'harga_origin');
            $nominalDiskon = ArrayHelper::getValue($value, 'nominal_diskon');
            $nominalDijamin = ArrayHelper::getValue($value, 'dijamin');
            $plafonPayer = ArrayHelper::getValue($value, 'plafon_payer');
            $ditagihkanPasien = ArrayHelper::getValue($value, 'totalDibayar');
            $existingData = LogTagihan::find(true)->Where(['pelayanan_id' => $pelayananId])
            ->orderby(['logedittagihan_id' => SORT_DESC])
            ->asArray()
            ->one();
         
            if(! empty($existingData)) {
               $existingHarga = ArrayHelper::getValue($existingData, 'harga');
               $existingDiskon = ArrayHelper::getValue($existingData, 'nominal_diskon');
               $existingPersenDiskon = ArrayHelper::getValue($existingData, 'persen_diskon');
               $existingDijamin = ArrayHelper::getValue($existingData, 'dijamin');
               $existingPlafon = ArrayHelper::getValue($existingData, 'plafon_payer');
               $existingDitagihkan = ArrayHelper::getValue($existingData, 'totalDibayar');
               $hasil[] = ['harga' => $harga, 'exis' => $existingData];
               $textKeterangan = "";
               // Apabila kondisi harga tidak sama atau ada diskon pada salah satunya
               if($harga != $existingHarga || $nominalDiskon != 0 || $nominalDijamin != 0 || $plafonPayer != 0) {
                  if($harga != $existingHarga) {
                     $textKeterangan .= self::mappingKeterangan('harga', $value);
                  } 

                  if($nominalDiskon != $existingDiskon) {
                     $textKeterangan .= self::mappingKeterangan('diskon', $value, ['existing_diskon' => $existingDiskon, 'persen_diskon' => $existingPersenDiskon]);
                  }

                  if($nominalDijamin != $existingDijamin) {

                     $optionalData = [
                        'existing_dijamin' => $existingDijamin,
                        'mainpayer_dijamin' => $nominalDijamin
                     ];

                     $textKeterangan .= self::mappingKeterangan('mainpayer', $value, $optionalData);
                  }

                  if($plafonPayer != 0 && $plafonPayer != $existingPlafon) {
                     $textKeterangan .= self::mappingKeterangan('plafon', $value,['plafon' => $plafonPayer, 'existing_plafon' => $existingPlafon] );
                  }

                  $value['created_by'] = $loginPemakaiId;
                  $value['created_date'] = $time;
                  $value['keterangan'] = $textKeterangan;
                  self::mappingAttribute($value);
               }

               if($nominalDijamin == 0) {
                  // Text ketika nominal dijamin kosong / ditagihkan kepasien
                  if($nominalDijamin == 0 && $ditagihkanPasien != $existingDitagihkan) {
                     $textKeterangan .= self::mappingKeterangan('dibayar_pasien', $value);
                     $value['created_by'] = $loginPemakaiId;
                     $value['created_date'] = $time;
                     $value['keterangan'] = $textKeterangan;
                     self::mappingAttribute($value);
                  }
               }
            } else {
               $textKeterangan = "";
               // Apabila kondisi data kosong.
               if($harga != $hargaOrigin || $nominalDiskon != 0 || $nominalDijamin != 0 || $plafonPayer != 0) {
                  if($harga != $hargaOrigin) {
                     $textKeterangan .= self::mappingKeterangan('harga', $value);
                  }

                  if($nominalDiskon != 0) {
                     $textKeterangan .= self::mappingKeterangan('diskon', $value, [
                        'diskon'
                     ]);
                  }

                  if($nominalDijamin != 0 && $nominalDijamin != ($qtyBarang * $harga)) {
                     $mainPayerFirst = $qtyBarang * $harga;
                     $optionalData = [
                        'mainpayer_first' => $mainPayerFirst,
                        'mainpayer_dijamin' => $nominalDijamin
                     ];
                     $textKeterangan .= self::mappingKeterangan('mainpayer', $value, $optionalData);
                  }

                  if(intval($plafonPayer) != 0) {
                     $textKeterangan .= self::mappingKeterangan('plafon', $value);
                  }

                  $value['created_by'] = $loginPemakaiId;
                  $value['created_date'] = $time;
                  $value['keterangan'] = $textKeterangan;
                  self::mappingAttribute($value);
               }

               if($nominalDijamin == 0) {
                  // Text ketika nominal dijamin kosong / ditagihkan kepasien
                  if($nominalDijamin == 0 && $ditagihkanPasien != 0) {
                     $textKeterangan .= self::mappingKeterangan('dibayar_pasien', $value);
                     $value['created_by'] = $loginPemakaiId;
                     $value['created_date'] = $time;
                     $value['keterangan'] = $textKeterangan;
                     self::mappingAttribute($value);
                  }
               }
            }
         }
      }

      // Purifier payload data 
      $editedPayload = [];
      foreach ($this->payload as $key => $value) {
         $keterangan = ArrayHelper::getValue($value, 'keterangan');
         if($keterangan != "" || $keterangan != null) {
            $editedPayload[] = $value;
         }
      }

      if(! empty($editedPayload)) {
         LogTagihan::batchInsert($editedPayload);
      } else {
         $editedPayload = [
            [
               'pendaftaran_id' => $pendaftaranId,
               'keterangan' => "Hanya Simpan.",
               'is_deleted' => false,
               'is_active' => true,
               'kelompoktindakan_nama' => '-',
               'created_by' => $loginPemakaiId,
               'created_date' => $time,
            ]
         ];
         LogTagihan::batchInsert($editedPayload);
      }

      return json_encode([
         'service' => 'Sirs-LogEditTagihan',
         'payload' => $editedPayload,
         'attributes' => $this->attributes,
         'timestamp' => date('Y-m-d H:i:s'),
      ]);
   }
   
   /**
    * @author Maulana Muhammad Rizky
    * @param $value
    */
   protected function mappingAttribute($value)
   {
      $value['is_deleted'] = false;
      $value['is_active'] = true;
      $this->payload[] = $value;
   }

   /**
    * @author Maulana Muhammad Rizky
    * @param 
    */
   protected function mappingKeterangan($jenisKeterangan, $value, $options = [])
   {
      if($jenisKeterangan == 'harga') {

         return '<li>Perubahan Harga '.$value['tindakan'] . ' <b>Rp.'.number_format($value['harga_origin']).'</b> Ke <b>Rp.'. number_format($value['harga']). '</b> </li>';
      }

      if($jenisKeterangan == 'diskon') {
         $diskonExsiting = isset($options['existing_diskon']) ? $options['existing_diskon'] : 0; 
         $persenExsiting = isset($options['persen_diskon']) ? $options['persen_diskon'] : 0; 
         if($value['persen_diskon'] != 0) {
            return '<li>Perubahan diskon '.$value['tindakan'] .' dari <b>'.$persenExsiting.'%</b> Ke <b>'.$value['persen_diskon'].'%</b></li>';
         } else {
            return '<li>Perubahan diskon '.$value['tindakan'] .' dari <b>Rp.'.number_format($diskonExsiting).'</b> Ke <b>Rp.'. number_format($value['nominal_diskon']).'</b></li>';
         }
      }

      if($jenisKeterangan == 'mainpayer') {
         $mainPayerFirst = isset($options['mainpayer_first']) ? $options['mainpayer_first'] : 0; 
         $nominalDijamin = isset($options['mainpayer_dijamin']) ? $options['mainpayer_dijamin'] : 0; 
         $existingDijamin = isset($options['existing_dijamin']) ? $options['existing_dijamin'] : 0; 
         if($mainPayerFirst != 0) {
            return '<li>Perubahan Main payer '.$value['tindakan'] .' dari <b>Rp.'.number_format($mainPayerFirst).'</b> Ke <b>Rp.'.number_format($nominalDijamin) .'</b></li>';
         } else {
            return '<li>Perubahan Main payer '.$value['tindakan'] .' dari <b>Rp.'.number_format($existingDijamin).'</b> Ke <b>Rp.'.number_format($nominalDijamin) .'</b></li>';
         }
      }

      if($jenisKeterangan == 'plafon') {
         if(! empty($options)) {
            return '<li>Perubahan Plafon Payer '.$value['tindakan'] .' Ke <b>Rp.'. number_format($value['plafon_payer']).'</b> </li>';
         } 

         if(empty($options)) {
            return '<li>Perubahan Plafon Payer '.$value['tindakan'] .' Ke <b>Rp.'. number_format($value['plafon_payer']).'<b/> </li>';
         }
      }

      if($jenisKeterangan == 'dibayar_pasien') {
         return '<li>Perubahan '.$value['tindakan'] .' Ditagihkan ke Pasien</li>';
      }
   }
}  
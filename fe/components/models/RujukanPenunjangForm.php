<?php

namespace app\components\models;

use Yii;

class RujukanPenunjangForm extends \app\components\DocoBaseModel
{
   protected $xssProtected = [
      'alasan_rujukan'
   ];
   public $pasienkirimkeunitlain_id;
   public $tanggal_rujukan;
   public $rs_tujuan;
   public $pegawai_menyetujui;
   public $alasan_rujukan;
   public $detail_tindakan;

   public function rules()
   {
      return [
         [['pasienkirimkeunitlain_id', 'tanggal_rujukan', 'rs_tujuan', 'pegawai_menyetujui', 'alasan_rujukan'], 'required'],
         [[
            'tanggal_rujukan', 'rs_tujuan', 'pegawai_menyetujui', 'alasan_rujukan',
            'detail_tindakan'
         ], 'safe']
      ];
   }

   /**
    * @todo for attribute label form
    */
   public function attributeLabels()
   {
      return [
         'rs_tujuan' => \Yii::t('fe', 'RS Tujuan / Klinik Rujukan'),
         'pegawai_menyetujui' => \Yii::t('fe', 'Disetujui Oleh'),
      ];
   }
}

<?php

namespace app\modules\v1\models;

use Yii;

class LaporanOperasi extends \Doco\components\DocoActiveRecord
{
   protected $xssProtected = [
      'uraian',
      'komplikasi',
      'perdarahan',
      'asal_jaringan',
      'ukuran_implant',
      'jumlah_darah_masuk',
      'posisi_pasien',
      'diagnosis_prabedah',
      'diagnosis_paskabedah',
      'intruksi_post_operasi'
   ];

   /**
    * @inheritdoc
    */
   public static function tableName()
   {
      return 'laporanoperasi_r';
   }

   /**
    * @inheritdoc
    */
   public function rules()
   {
      return [
         [['pasienmasukpenunjang_id', 'dokter_id', 'mulai_operasi', 'selesai_operasi'], 'required'],
         [['pasienmasukpenunjang_id', 'laporanoperasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
         [['is_kirimkepatologi', 'is_deleted', 'is_active'], 'boolean'],
         [['additional_data'], 'string'],
         [['mulai_operasi', 'selesai_operasi', 'mulai_pembiusan', 'selesai_pembiusan'], 'date', 'format' => 'php:Y-m-d H:i'],
         [['ukuran_implant', 'jumlah_darah_masuk'], 'string', 'max' => 255],
         [[
            'diagnosis_prabedah', 'kategori_operasi', 'cara_pembiusan', 'posisi_pasien', 'mulai_pembiusan', 'selesai_pembiusan',
            'komplikasi', 'perdarahan', 'asal_jaringan', 'ukuran_implant', 'jumlah_darah_masuk',
            'created_date', 'last_modified_date', 'deleted_date', 'dokter_id', 'intruksi_post_operasi'
         ], 'safe'],
      ];
   }

   /**
    * @inheritdoc
    */
   public function attributeLabels()
   {
      return [
         'pasienmasukpenunjang_id' => Yii::t('app', 'Pasien Masuk Penunjang ID'),
         'laporanoperasi_id' => Yii::t('app', 'Laporan Operasi ID'),
         'mulai_operasi' => Yii::t('app', 'Tanggal Mulai Operasi (Surgery Start)'),
         'selesai_operasi' => Yii::t('app', 'Tanggal Selesai Operasi  (Surgery End)'),
         'lama_pembedahan' => Yii::t('app', 'Lama Pembedahan'),
         'dokter_bedah' => Yii::t('app', 'Dokter Bedah (Surgeon)'),
         'asisten' => Yii::t('app', 'Asisten (Assistant)'),
         'asisten_instrumen' => Yii::t('app', 'Asisten Instrumen (Instruments Assistant)'),
         'kategori_operasi' => Yii::t('app', 'Kategori Operasi (Surgery Category)'),
         'diagnosis_prabedah' => Yii::t('app', 'Diagnosis Pra Bedah (Pra Surgery Diagnosis)'),
         'nama_prosedur' => Yii::t('app', 'Nama Prosedur Bedah (Procedure)'),
         'diagnosis_paskabedah' => Yii::t('app', 'Diagnosis Paska Bedah (Post Surgery Diagnosis)'),
         'dokte_anastesi' => Yii::t('app', 'Dokter Anastesi (Anesthesiologist)'),
         'cara_pembiusan' => Yii::t('app', 'Cara Pembiusan'),
         'posisi_pasien' => Yii::t('app', 'Posisi Pasien'),
         'mulai_pembiusan' => Yii::t('app', 'Tanggal Mulai Pembiusan (Anesthesi Start)'),
         'selesai_pembiusan' => Yii::t('app', 'Tanggal Selesai Pembiusan (Anesthesi End)'),
         'uraian' => Yii::t('app', 'Uraian Pembedahan (Procedure Description)'),
         'komplikasi' => Yii::t('app', 'Komplikasi (Complications)'),
         'perdarahan' => Yii::t('app', 'Perdarahan (Bleeding)'),
         'is_kirimkepatologi' => Yii::t('app', 'Jaringan dikirim ke Patologi (Pathology Tissues)'),
         'asal_jaringan' => Yii::t('app', 'Asal Jaringan (Origins of Pathology Tissues)'),
         'ukuran_implant' => Yii::t('app', 'Ukuran Implant'),
         'jumlah_darah_masuk' => Yii::t('app', 'Jumlah Darah Masuk'),
         'additional_data' => Yii::t('app', 'Additional Data'),
         'created_date' => Yii::t('app', 'Created Date'),
         'created_by' => Yii::t('app', 'Created By'),
         'modified_count' => Yii::t('app', 'Modified Count'),
         'last_modified_date' => Yii::t('app', 'Last Modified Date'),
         'last_modified_by' => Yii::t('app', 'Last Modified By'),
         'is_deleted' => Yii::t('app', 'Is Deleted'),
         'is_active' => Yii::t('app', 'Is Active'),
         'deleted_date' => Yii::t('app', 'Deleted Date'),
         'deleted_by' => Yii::t('app', 'Deleted By'),
         'intruksi_post_operasi' => Yii::t('app', 'Intruksi Post Operasi'),
      ];
   }

   public function getInpost()
   {
      return $this->hasOne(InpostOperasi::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
   }

   public function getDetailOperasi()
   {
      return $this->hasMany(InfoInpostOperasiDetailView::className(), ['pasienmasukpenunjang_id' => 'pasienmasukpenunjang_id']);
   }
}

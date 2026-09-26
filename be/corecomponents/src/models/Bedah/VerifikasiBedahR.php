<?php

namespace Doco\models\Bedah;

use Yii;

class VerifikasiBedahR extends \Doco\components\DocoActiveRecord
{

   public $parent_tim;

   /**
    * @inheritdoc
    */
   public static function tableName()
   {
      return 'verifikasibedah_r';
   }

   /**
    * @inheritdoc
    */
   public function rules()
   {
      return [
         [['pasienmasukpenunjang_id'], 'required'],
         [['created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'is_deleted', 'is_active'], 'default', 'value' => null],
         [['dokter_id','perawat_id','daftartindakan_id','qty', 'timoperasi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
         [['is_cyto', 'is_penyulit', 'useprice'], 'boolean'],
         [['posisi_operasi', 'harga', 'persentase', 'posisi_tim', 'kode_posisi', 'persencyto_tindakan', 'persen_penyulit', 
            'harga_cyto', 'harga_penyulit', 'total_harga', 'persentase_harga', 'total_harga_real',
            'created_date', 'last_modified_date', 'deleted_date', 'parent_tim', 'timoperasi_id'], 'safe'],
         [['operasi_nama', 'golonganoperasi_nama', 'nama_pegawai', 'pegawai_input', 'daftartindakan_nama', 'kegiatanoperasi_nama'], 'string'],
      ];
   }

   /**
    * @inheritdoc
    */
    
//    public function attributeLabels()
//    {
//       return [
//          'pasienmasukpenunjang_id' => Yii::t('app', 'Pasien Masuk Penunjang ID'),
//          'dokter_id' => Yii::t('app', 'Laporan Operasi ID'),
//          'mulai_operasi' => Yii::t('app', 'Mulai Operasi (Surgery Start)'),
//          'selesai_operasi' => Yii::t('app', 'Selesai Operasi  (Surgery End)'),
//          'lama_pembedahan' => Yii::t('app', 'Lama Pembedahan'),
//          'dokter_bedah' => Yii::t('app', 'Dokter Bedah (Surgeon)'),
//          'asisten' => Yii::t('app', 'Asisten (Assistant)'),
//          'asisten_instrumen' => Yii::t('app', 'Asisten Instrumen (Instruments Assistant)'),
//          'kategori_operasi' => Yii::t('app', 'Kategori Operasi (Surgery Category)'),
//          'diagnosis_prabedah' => Yii::t('app', 'Diagnosis Pra Bedah (Pra Surgery Diagnosis)'),
//          'nama_prosedur' => Yii::t('app', 'Nama Prosedur Bedah (Procedure)'),
//          'diagnosis_paskabedah' => Yii::t('app', 'Diagnosis Paska Bedah (Post Surgery Diagnosis)'),
//          'dokte_anastesi' => Yii::t('app', 'Dokter Anastesi (Anesthesiologist)'),
//          'cara_pembiusan' => Yii::t('app', 'Cara Pembiusan'),
//          'posisi_pasien' => Yii::t('app', 'Posisi Pasien'),
//          'mulai_pembiusan' => Yii::t('app', 'Mulai Pembiusan (Anesthesi Start)'),
//          'selesai_pembiusan' => Yii::t('app', 'Selesai Pembiusan (Anesthesi End)'),
//          'uraian' => Yii::t('app', 'Uraian Pembedahan (Procedure Description)'),
//          'komplikasi' => Yii::t('app', 'Komplikasi (Complications)'),
//          'perdarahan' => Yii::t('app', 'Perdarahan (Bleeding)'),
//          'is_kirimkepatologi' => Yii::t('app', 'Jaringan dikirim ke Patologi (Pathology Tissues)'),
//          'asal_jaringan' => Yii::t('app', 'Asal Jaringan (Origins of Pathology Tissues)'),
//          'additional_data' => Yii::t('app', 'Additional Data'),
//          'created_date' => Yii::t('app', 'Created Date'),
//          'created_by' => Yii::t('app', 'Created By'),
//          'modified_count' => Yii::t('app', 'Modified Count'),
//          'last_modified_date' => Yii::t('app', 'Last Modified Date'),
//          'last_modified_by' => Yii::t('app', 'Last Modified By'),
//          'is_deleted' => Yii::t('app', 'Is Deleted'),
//          'is_active' => Yii::t('app', 'Is Active'),
//          'deleted_date' => Yii::t('app', 'Deleted Date'),
//          'deleted_by' => Yii::t('app', 'Deleted By'),
//       ];
//    }
 
}

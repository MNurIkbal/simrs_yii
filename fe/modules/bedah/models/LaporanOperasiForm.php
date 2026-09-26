<?php

namespace Doco\bedah\models;

class LaporanOperasiForm extends \yii\base\Model
{
   public $jam_masuk_rec;
   public $jam_keluar_rec;
   public $lama_pembedahan;
   public $dokter_bedah;
   public $asisten;
   public $asisten_instrumen;
   public $kategori_operasi;
   public $diagnosis_prabedah;
   public $prosedur_bedah;
   public $diagnosis_paska_bedah;
   public $dokter_anestesi;
   public $cara_pembiusan;
   public $posisi_pasien;
   public $mulai_pembiusan;
   public $selesai_pembiusan;
   public $uraian_pembedahan;
   public $komplikasi;
   public $perdarahan;
   public $is_jaringan_dikirim;
   public $asal_jaringan;
   public $ukuran_implant;
   public $jumlah_darah_masuk;
   public $nama_dokter_bedah;
   public $pasienmasukpenunjang_id;
   public $laporanoperasi_id;
   public $intruksi_post_operasi;
   
   public function rules()
   {
      return [
         [['jam_masuk_rec', 'jam_keluar_rec', 'dokter_bedah', 'pasienmasukpenunjang_id'], 'required'],
         [['ukuran_implant', 'jumlah_darah_masuk'], 'string', 'max' => 255],
         [
            [
               'jam_masuk_rec', 'jam_keluar_rec', 'lama_pembedahan', 'dokter_bedah', 'asisten', 'asisten_instrumen',
               'kategori_operasi', 'diagnosis_prabedah', 'prosedur_bedah', 'diagnosis_paska_bedah', 'dokter_anestesi',
               'cara_pembiusan', 'posisi_pasien', 'mulai_pembiusan', 'selesai_pembiusan', 'uraian_pembedahan', 'komplikasi',
               'perdarahan', 'is_jaringan_dikirim', 'asal_jaringan', 'nama_dokter_bedah', 'laporanoperasi_id', 'intruksi_post_operasi'
            ],
            'safe'
         ],
      ];
   }
   public function attributeLabels()
   {
      return [
         'jam_masuk_rec' => \Yii::t('fe', 'Mulai Operasi (Surgery Start)'),
         'jam_keluar_rec' => \Yii::t('fe', 'Selesai Operasi  (Surgery End)'),
         'dokter_bedah' => \Yii::t('fe', 'Dokter Bedah (Surgeon)'),
         'asisten' => \Yii::t('fe', 'Asisten (Assistant)'),
         'asisten_instrumen' => \Yii::t('fe', 'Asisten Instrumen (Instruments Assistant)'),
         'kategori_operasi' => \Yii::t('fe', 'Kategori Operasi (Surgery Category)'),
         'diagnosis_prabedah' => \Yii::t('fe', 'Diagnosis Pra Bedah (Pra Surgery Diagnosis)'),
         'prosedur_bedah' => \Yii::t('fe', 'Nama Prosedur Bedah (Procedure)'),
         'diagnosis_paska_bedah' => \Yii::t('fe', 'Diagnosis Paska Bedah (Post Surgery Diagnosis)'),
         'dokter_anestesi' => \Yii::t('fe', 'Dokter Anestesi (Anesthesiologist)'),
         'mulai_pembiusan' => \Yii::t('fe', 'Mulai Pembiusan (Anesthesi Start)'),
         'selesai_pembiusan' => \Yii::t('fe', 'Selesai Pembiusan (Anesthesi End)'),
         'uraian_pembedahan' => \Yii::t('fe', 'Uraian Pembedahan (Procedure Description)'),
         'komplikasi' => \Yii::t('fe', 'Komplikasi (Complications)'),
         'perdarahan' => \Yii::t('fe', 'Perdarahan (Bleeding)'),
         'is_jaringan_dikirim' => \Yii::t('fe', 'Jaringan dikirim ke Patologi (Pathology Tissues)'),
         'asal_jaringan' => \Yii::t('fe', 'Asal Jaringan (Origins of Pathology Tissues)'),
         'ukuran_implant' => \Yii::t('fe', 'No. Seri Implant'),
         'jumlah_darah_masuk' => \Yii::t('fe', 'Jumlah Darah Masuk'),
         'intruksi_post_operasi' => \Yii::t('fe', 'Intruksi Post Operasi'),
      ];
   }
}

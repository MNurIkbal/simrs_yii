<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class SyKunjunganPasien extends \Integrasi\Components\ActiveRepositories
{
   public static function tableName()
   {
      return 'sy_kunjungan';
   }

   /**
    * @inheritdoc
    */
   public function rules()
   {
      return [
         [['tgl_lahir', 'tgl_pendaftaran', 'tgl_pulang', 'created_date', 'last_modified_date', 'deleted_date', 'is_verifikasi', 'identitas_id', 'identitas_nama', 'identitas_value', 'no_klaimcovid', 'pasien_id', 'nosep','jeniskasuspenyakit_id','jeniskasuspenyakit_nama', 'instalasi_id','ruangan_id'], 'safe'],
         [['status_kunjungan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'hak_kelasbpjs', 'total_verifikasi'], 'default', 'value' => null],
         [['status_kunjungan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'hak_kelasbpjs'], 'integer'],
         [['additional_data'], 'string'],
         [['is_deleted', 'is_active' , 'is_verifikasi'], 'boolean'],
         [['no_pendaftaran', 'no_rekammedik', 'no_sep', 'no_asuransi'], 'string', 'max' => 150],
         [['nama_pasien'], 'string', 'max' => 255],
         [['jenis_kelamin', 'umur', 'instalasi_nama', 'ruangan_nama', 'carabayar_nama', 'penjamin_nama', 'kelas_nama', 'dokter_nama', 'no_kamar', 'no_tempattidur'], 'string', 'max' => 100],
         [['instalasi_kode', 'ruangan_kode', 'carabayar_kode', 'penjamin_kode', 'kelas_kode', 'dokter_kode', 'carakeluar_kode', 'lama_rawat'], 'string', 'max' => 50],
      ];
   }
}

<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class Antrian extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antrian_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_antrian', 'no_antrian'], 'required'],
            [['konfigantrian_id', 'ruangan_id', 'carabayar_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id', 'penjamin_id', 'pegawai_id','jadwaldokter_id', 'status_antrian', 'status_pasien', 'groupcarabayar_id', 'jenisantrian_id','fungsiantrian_id', 'klasifikasipasien_id', 'jadwalbukapoli_id'], 'default', 'value' => null],
            [['konfigantrian_id', 'ruangan_id', 'carabayar_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pasien_id', 'penjamin_id', 'pegawai_id','jadwaldokter_id', 'status_antrian', 'status_pasien', 'groupcarabayar_id', 'jenisantrian_id','fungsiantrian_id', 'klasifikasipasien_id', 'jadwalbukapoli_id'], 'integer'],
            [['instalasi_id','tgl_antrian', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['panggil_flag', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['panggilan_ke'], 'number'],
            [['no_antrian'], 'string', 'max' => 6],
            [['carabayar_loket'], 'string', 'max' => 50],
        ];
    }

}

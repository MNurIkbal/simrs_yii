<?php

namespace Integrasi\Service\Sirs\Models\Remunerasi;

use Yii;

class RemunPegawai extends \Integrasi\Components\ActiveRepositories
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'remunpegawai_t';
    }

    public function rules()
    {
        return [
            [[
                'remunpegawai_id',
                'pegawai_kode',
                'nama_pegawai',
                'nik',
                'jabatan_kode',
                'jabatan_nama',
                'estimasi_remun',
                'additional_data',
                'created_date',
                'periode_bulan',
                'periode_tahun',
                'grading',
                'posisi',
                'detail_tindakan',
                'detail_absen',
                'ronde_besar',
                'koord_lap_jaga_bangsal',
                'tidak_apel',
                'audit_medik',
                'rapat_koord_pelayanan',
                'penelitian',
                'presentasi',
                'rapat_direksi',
                'kwalitas',
                'kepatuhan',
                'last_modified_date',
                'deleted_date'
            ], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_active', 'is_deleted'], 'boolean'],
        ];
    }
}

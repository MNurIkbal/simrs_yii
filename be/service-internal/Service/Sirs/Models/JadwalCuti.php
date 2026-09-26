<?php


namespace Integrasi\Service\Sirs\Models;

use Yii;
use \DateTime;
use \DateInterval;
use \DatePeriod;

class JadwalCuti extends \Doco\components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jadwalcuti_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pegawai_id', 
                'ruangan_id',
                'tgl_cuti_awal',
                'tgl_cuti_akhir',
            ], 'required'],
            [[
                'pegawai_id', 
                'spesialis_id',
                'ruangan_id',
                'tgl_cuti_awal',
                'tgl_cuti_akhir',
                'created_by',
                'created_date',
                'is_deleted',
                'is_active',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jadwalcuti_id' => 'Jadwal Cuti ID',
            'pegawai_id' => 'Dokter',
            'ruangan_id' => 'Ruangan',
            'spesialis_id' => 'Spesialis ID',
            'spesialis_nama' => 'Spesialis',
            'tgl_cuti_awal' => 'Tanggal Cuti Awal',
            'tgl_cuti_akhir' => 'Tanggal Cuti Akhir',
        ];
    }
}

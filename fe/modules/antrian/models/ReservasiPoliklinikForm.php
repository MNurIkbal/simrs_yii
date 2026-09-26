<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-29 10:15:35
 */

 namespace app\modules\antrian\models;

use Yii;
class ReservasiPoliklinikForm extends \yii\base\Model
{
    const SCENARIO_DOKTER = 'sc_dokter';

    public $pendaftaranol_id;
    public $pendaftaran_id;
    public $no_pendaftaranol;
    public $tgl_pendaftaranol;
    public $jam_kunjungan;
    public $pasien_id;
    public $carabayar_id;
    public $penjamin_id;
    public $ruangan_id;
    public $pegawai_id;
    public $shift_id;
    public $no_asuransi;
    public $no_rujukan;
    public $status_pasien;
    public $status_daftar_ol;
    public $antrian_id;
    public $klasifikasipasien_id;
    public $jadwaldokter_id;
    public $jam_mulai;
    public $jam_tutup;
    public $jenis_reservasi;
    public $keterangan;
    public $jadwalbukapoli_id;
    public $user_id;
    public $nomor_urut;
    public $is_nomor_urut;
    public $no_bpjs;
    public $jeniskunjungan;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'pegawai_id',
                    'nomor_urut',
                ],
                'required', 'on' => 'scenario_nomor_urut_manual',
                'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [['pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'shift_id', 'status_pasien', 'status_daftar_ol', 'antrian_id', 'klasifikasipasien_id', 'jadwaldokter_id', 'jenis_reservasi', 'jadwalbukapoli_id'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'carabayar_id', 'penjamin_id', 'ruangan_id', 'pegawai_id', 'shift_id', 'status_pasien', 'status_daftar_ol', 'antrian_id', 'klasifikasipasien_id', 'jadwaldokter_id', 'jenis_reservasi', 'jadwalbukapoli_id', 'user_id'], 'integer'],
            [['tgl_pendaftaranol', 'jam_mulai', 'jam_tutup', 'nomor_urut', 'is_nomor_urut', 'no_bpjs', 'no_rujukan', 'jeniskunjungan'], 'safe'],
            [['tgl_pendaftaranol', 'ruangan_id', /*'pegawai_id',*/ 'jam_kunjungan'], 'required'],
            [['keterangan'], 'string'],
            [['no_pendaftaranol', 'jam_kunjungan'], 'string', 'max' => 100],
            [['no_asuransi', 'no_rujukan'], 'string', 'max' => 255],
            [['pegawai_id'], 'required', 'on' => self::SCENARIO_DOKTER],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pendaftaranol_id' => \Yii::t('fe', 'ID'),
            'pendaftaran_id' => \Yii::t('fe', 'Pendaftaran'),
            'no_pendaftaranol' => \Yii::t('fe', 'No. Pendaftaran'),
            'tgl_pendaftaranol' => \Yii::t('fe', 'Tanggal Pendaftaran'),
            'jam_kunjungan' => \Yii::t('fe', 'Jam Kunjungan'),
            'pasien_id' => \Yii::t('fe', 'Pasien'),
            'carabayar_id' => \Yii::t('fe', 'Cara Bayar'),
            'penjamin_id' => \Yii::t('fe', 'Penjamin'),
            'ruangan_id' => \Yii::t('fe', 'Ruangan'),
            'pegawai_id' => \Yii::t('fe', 'Dokter'),
            'shift_id' => \Yii::t('fe', 'Shift'),
            'no_asuransi' => \Yii::t('fe', 'No. Asuransi'),
            'no_rujukan' => \Yii::t('fe', 'No. Rujukan'),
            'status_pasien' => \Yii::t('fe', 'Status Pasien'),
            'status_daftar_ol' => \Yii::t('fe', 'Status'),
            'antrian_id' => \Yii::t('fe', 'Antrian'),
            'klasifikasipasien_id' => \Yii::t('fe', 'Klasifikasi Pasien'),
            'jadwaldokter_id' => \Yii::t('fe', 'Jadwal Dokter'),
            'jam_mulai' => \Yii::t('fe', 'Jam Mulai'),
            'jam_tutup' => \Yii::t('fe', 'Jam Tutup'),
            'jenis_reservasi' => \Yii::t('fe', 'Jenis Reservasi'),
            'keterangan' => \Yii::t('fe', 'Keterangan'),
            'jadwalbukapoli_id' => \Yii::t('fe', 'Jadwal Poliklinik'),
            'user_id' => \Yii::t('fe', 'User'),
            'nomor_urut'=> \Yii::t('fe', 'Nomor Urut'),
            'is_nomor_urut'=> \Yii::t('fe', 'Konfig Nomor Urut'),
            'no_bpjs' => \Yii::t('fe', 'No. Kartu BPJS'),
            'jeniskunjungan' => \Yii::t('fe', 'Asal Rujukan'),
        ];
    }
}
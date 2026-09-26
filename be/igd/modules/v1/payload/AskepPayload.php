<?php

namespace app\modules\v1\payload;

use Yii;


class AskepPayload extends \yii\base\Model
{
    public $asesmenperawatrd_id;
    public $pendaftaran_id;
    public $ruangan_id;
    public $tgl_asesmen;
    public $tgl_pendaftaran;
    public $tgl_datang;
    public $perawat_id;
    public $prioritas_triage;
    public $pasien_datang;
    public $jenis_asmenperawat;
    public $keadaan_umum;
    public $skala_nyeri;
    public $lama_sakit;
    public $gcseye_id;
    public $gcsverbal_id;
    public $gcsmotorik_id;
    public $td_systolic;
    public $td_diastolic;
    public $detak_nadi;
    public $pernapasan;
    public $suhu_tubuh;
    public $spo2;
    public $formulir_triage;
    public $formulir_fisik;
    public $is_alergi;
    public $is_nyeri;
    public $is_resikojatuh;
    public $is_kapitis;
    public $alasan_kunjungan;
    public $keluhan;
    public $r_penyakitkeluarga;
    public $catatan_asesmen;
    public $kelaianan_tubuh;
    public $tinggi_badan;
    public $berat_badan;
    public $bb_ideal;
    public $imt;
    public $ket_imt;
    public $metode_nyeri;
    public $alergi_obat;
    public $is_alergiobat;
    public $alergi_lainnya;
    public $is_alergilainnya;
    public $tekanan_darah;
    public $lokasi_nyeri;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'ruangan_id', 'perawat_id', 'tgl_pendaftaran', 'tgl_asesmen', 'tgl_datang', 'prioritas_triage', 'pasien_datang', 'jenis_asmenperawat', 'is_nyeri', 'alasan_kunjungan', 'is_alergi', 'keadaan_umum', 'is_nyeri', 'is_resikojatuh', 'keluhan', 'gcsmotorik_id', 'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'tinggi_badan', 'berat_badan', 'spo2'], 'required'],
            ['alergi_obat', 'required', 'when' => function($model) {
                return $model->is_alergiobat && $model->is_alergi == 1;
            }],
            ['alergi_lainnya', 'required', 'when' => function($model) {
                return $model->is_alergilainnya && $model->is_alergi == 1;
            }],
            ['lokasi_nyeri', 'required', 'when' => function($model) {
                return $model->is_nyeri == 1;
            }],
            ['skala_nyeri', 'required', 'when' => function($model) {
                return $model->is_nyeri == 1;
            }],
            ['metode_nyeri', 'required', 'when' => function($model) {
                return $model->is_nyeri == 1;
            }],
            [['asesmenperawatrd_id', 'pendaftaran_id', 'ruangan_id', 'perawat_id', 'prioritas_triage', 'pasien_datang', 'jenis_asmenperawat', 'skala_nyeri', 'lama_sakit', 'gcseye_id', 'gcsverbal_id', 'gcsmotorik_id', 'keadaan_umum', 'metode_nyeri'], 'integer'],
            [['tinggi_badan', 'berat_badan', 'bb_ideal', 'imt',  'td_systolic', 'td_diastolic', 'detak_nadi', 'pernapasan', 'suhu_tubuh', 'spo2'], 'number'],
            [['tgl_asesmen', 'tgl_pendaftaran'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['formulir_triage', 'formulir_fisik', 'is_alergiobat', 'is_alergilainnya', 'lokasi_nyeri', 'is_nyeri', 'tgl_datang'], 'safe'],
            [['is_alergi', 'is_nyeri', 'is_resikojatuh', 'is_kapitis'], 'boolean'],
            [['alasan_kunjungan', 'keluhan', 'r_penyakitkeluarga', 'catatan_asesmen', 'kelaianan_tubuh', 'formulir_triage', 'formulir_fisik'], 'string'],
            [['ket_imt'], 'string', 'max' => 100]
        ];
    }

    public function attributeLabels()
    {
        return [
            'jenis_asmenperawat' => 'Assesment Keperawatan',
            'is_alergi' => 'Riwayat Alergi',
            'is_nyeri' => 'Nyeri',
            'is_resikojatuh' => 'Resiko Jatuh',
            'gcsmotorik_id' => 'GCS Motorik',
            'td_systolic' => 'Tekanan Darah Systolic',
            'td_diastolic' => 'Tekanan Darah Diastolic',
            'tgl_datang' => 'Pasien Datang Pukul'
        ];
    }

}

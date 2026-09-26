<?php

namespace app\modules\fisioterapi\models;

class FormulirRajalForm extends BaseFormModel
{
	public $pendaftaran_id;
    public $programterapi_id;
    public $pegawai_id;
    public $anamnesa;
    public $pemeriksaanfisik_dan_ujifungsi;
    public $diag_medis;
    public $diag_fungsi;
    public $diag_kfr;
    public $hasil_pemeriksaan_penunjang;
    public $anjuran;
    public $goal;
    public $evaluasi;
    public $suspek_penyakit;
    public $is_suspek;
    public $additional_data;
    public $pasien_id;
    public $ruangan_id;
    public $program_terapi_ids;

	public function rules()
	{
		return [
			[
				[
					'pendaftaran_id',
                    'anamnesa',
                    'diag_medis',
                    'diag_fungsi',
					'pemeriksaanfisik_dan_ujifungsi',
				], 'required',
				'message' => '{attribute} tidak boleh kosong!'
			],
			[
				[
					'pendaftaran_id',
                    'programterapi_id',
                    'pegawai_id',
                    'anamnesa',
                    'pemeriksaanfisik_dan_ujifungsi',
                    'diag_medis',
                    'diag_fungsi',
                    'diag_kfr',
                    'hasil_pemeriksaan_penunjang',
                    'anjuran',
                    'goal',
                    'evaluasi',
                    'suspek_penyakit',
                    'is_suspek',
                    'additional_data',
                    'pasien_id',
                    'ruangan_id',
                    'program_terapi_ids'
				], 'safe'
			],
            [['evaluasi', 'suspek_penyakit'], 'string', 'max' => 255],
            [['anamnesa', 'pemeriksaanfisik_dan_ujifungsi', 'hasil_pemeriksaan_penunjang', 'anjuran', 'goal', 'additional_data'], 'string'],
            [['diag_medis', 'diag_fungsi', 'diag_kfr'], 'safe'],
            [['is_suspek'], 'boolean'],
		];
	}

	public function attributeLabels()
	{
		return [
            'pendaftaran_id' => 'Pendaftaran ID',
            'programterapi_id' => 'Program Terapi ID',
            'pegawai_id' => 'Pegawai ID',
            'anamnesa' => 'Anamnesa',
            'pemeriksaanfisik_dan_ujifungsi' => 'Pemeriksaan Fisik dan Uji Fungsi',
            'diag_medis' => 'Diagnosa Medis',
            'diag_fungsi' => 'Diagnosa Fungsi',
            'diag_kfr' => 'Tata Laksana KFR',
            'hasil_pemeriksaan_penunjang' => 'Hasil Pemeriksaan Penunjang',
            'anjuran' => 'Anjuran',
            'goal' => 'Goal',
            'evaluasi' => 'Evaluasi',
            'suspek_penyakit' => 'Suspek Penyakit Akibat Kerja',
            'is_suspek' => 'Is Suspek',
            'additional_data' => 'Additional Data'
		];
	}
}

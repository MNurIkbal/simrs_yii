<?php

namespace app\modules\fisioterapi\models;

class ResumeMedisFisioForm extends BaseFormModel
{
	public $pendaftaran_id;
    public $pegawai_id;
    public $tgl_masuk;
    public $tgl_keluar;
    public $diag_utama;
    public $diag_penyerta;
    public $berat_badan;
    public $tinggi_badan;
    public $tekanan_darah;
    public $nadi;
    public $suhu;
    public $respirasi;
    public $prosedur_diag;
    public $subjective;
    public $objective;
    public $assesment;
    public $planning;
    public $pasien_id;
    public $ruangan_id;
    public $program_terapi_ids;

	public function rules()
	{
		return [
			[
				[
					'pendaftaran_id',
                    'tgl_masuk',
                    'diag_utama',
                    'prosedur_diag',
                    'subjective',
                    'objective',
                    'assesment',
                    'planning',
				], 'required',
				'message' => '{attribute} Harus Diisi'
			],
			[
				[
					'pendaftaran_id',
                    'tgl_masuk',
                    'tgl_keluar',
                    'diag_utama',
                    'diag_penyerta',
                    'berat_badan',
                    'tinggi_badan',
                    'tekanan_darah',
                    'nadi',
                    'suhu',
                    'respirasi',
                    'prosedur_diag',
                    'subjective',
                    'objective',
                    'assesment',
                    'planning',
                    'pasien_id',
                    'ruangan_id',
                    'program_terapi_ids'
				], 'safe'
			]
		];
	}

	public function attributeLabels()
	{
		return [
			'tgl_masuk' => 'Tanggal Masuk',
            'tgl_keluar' => 'Tanggal Keluar',
            'pegawai_id' => 'Dokter',
            'diag_utama' => 'Diagnosa Utama',
            'diag_penyerta' => 'Diagnosa Penyerta',
            'prosedur_diag' => 'Prosedur/Tindakan Kerja'
		];
	}
}
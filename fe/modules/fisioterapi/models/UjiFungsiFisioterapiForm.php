<?php

namespace app\modules\fisioterapi\models;

class UjiFungsiFisioterapiForm extends BaseFormModel
{
	public $pendaftaran_id;
    public $programterapi_id;
    public $diag_utama;
    public $diag_medis;
    public $diag_fungsi;
    public $tindakan_prosedur;
    public $hasil_yang_didapat;
    public $anjuran_dan_goal;
    public $pasien_id;
    public $ruangan_id;
    public $program_terapi_ids;

	public function rules()
	{
		return [
			[
				[
					'pendaftaran_id',
					'diag_utama',
					'hasil_yang_didapat',
				], 'required',
				'message' => '{attribute} tidak boleh kosong!'
			],
			[
				[
					'pendaftaran_id',
                    'programterapi_id',
                    'diag_utama',
                    'diag_medis',
                    'diag_fungsi',
                    'tindakan_prosedur',
                    'hasil_yang_didapat',
                    'anjuran_dan_goal',
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
            'diag_utama' => 'Kesimpulan',
            'diag_medis' => 'Diagnosa Medis',
            'diag_fungsi' => 'Diagnosa Fungsi',
            'tindakan_prosedur' => 'Tindakan/Prosedur',
            'hasil_yang_didapat' => 'Hasil Yang Didapat',
            'anjuran_dan_goal' => 'Anjuran dan Goal'
		];
	}
}

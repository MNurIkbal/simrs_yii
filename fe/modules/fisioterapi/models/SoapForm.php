<?php

namespace app\modules\fisioterapi\models;

class SoapForm extends BaseFormModel
{
	public $tgl_soap;
	public $subject;
	public $object;
	public $assesment;
	public $planning;
	public $terapis;
	public $soapfisioterapi_id;
    public $a_diag_utama;
    public $a_diag_penyerta;
    public $instruksi;
    public $catatan_dokter;
    public $diagnosa_fungsi;
    public $prosedur;
    public $goal;

	public function rules()
	{
		return [
			[
				[
					'subject',
					'object',
					'planning',
					'terapis',
					'a_diag_utama'
				], 'required', // REQUIRED
				'message' => '{attribute} Harus Diisi'
			],
			[
				[
					'subject',
					'object',
					'planning',
					'terapis',
					'soapfisioterapi_id',
					'a_diag_utama',
					'a_diag_penyerta',
					'instruksi',
					'catatan_dokter',
                    'diagnosa_fungsi',
                    'prosedur',
                    'goal'
				], 'safe' // SAFE
			]
		];
	}

	public function attributeLabels()
	{
		return [
			'terapis' => 'Terapis',
			'a_diag_utama' => 'Diagnosa Utama',
			'a_diag_penyerta' => 'Diagnosa Penyerta',
            'prosedur' => 'Tindakan/Prosedur'
		];
	}
}
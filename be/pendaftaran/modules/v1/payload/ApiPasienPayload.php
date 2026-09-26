<?php 

namespace app\modules\v1\payload;

use Yii;

class ApiPasienPayload extends \yii\base\Model
{
	public $nama_pasien;
	public $norm;
	public $tgl_lahir;
	public $jeniskelamin;
	public $tgl_awal;
	public $tgl_akhir;
	public $no_identitas_pasien;

	public function rules()
	{
		return [
            [['jeniskelamin'], 'integer'],
            [['nama_pasien', 'norm', 'tgl_lahir'], 'string'],
            [['nama_pasien', 'norm', 'tgl_lahir'], 'filter', 'filter' => function ($value) {
		         return \yii\helpers\HtmlPurifier::process($value);
		    }],
            [['nama_pasien', 'norm', 'tgl_lahir', 'jeniskelamin', 'tgl_awal', 'tgl_akhir'], 'safe'],
        ];
	}

	public function attributeLabels()
	{
		return [
			'nama_pasien' => 'Nama Pasien',
			'norm' => 'No Rekam Medik',
			'tgl_lahir' => 'Tanggal Lahir',
			'jeniskelamin' => 'Jenis Kelamin',
			'tgl_awal' => 'Tanggal Awal',
			'tgl_akhir' => 'Tanggal Akhir',
		];
	}
}
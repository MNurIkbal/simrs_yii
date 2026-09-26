<?php
namespace app\modules\mcu\models;

use Yii;

class TemplateForm extends \yii\base\Model
{
    public $template_id;
    public $jenis;
    public $type;
    public $temp_nama;
    public $pasien_id;
    public $additional_data;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[	'template_id','jenis','type','temp_nama','pasien_id','additional_data',
				],'safe'
			],
		];
	}
}

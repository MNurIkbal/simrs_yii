<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\mcu\models;

use Yii;
use app\components\DocoBaseModel;

class KesimpulanPenunjangForm extends DocoBaseModel
{
	protected $xssProtected = [
		'kesimpulan',
	];
	
	public $kesimpulanmcu_id;
	public $pendaftaran_id;
	public $tindakanpelayanan_id;
	public $kesimpulan;
	public $additional_data;

	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[
				[
                    'pendaftaran_id',
                    'tindakanpelayanan_id',
                    'kesimpulan',
                    'additional_data'
				],
				'safe'
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels()
	{
		return [
            'kesimpulan' => Yii::t('fe', 'Kesimpulan'),
		];
	}
}
?>
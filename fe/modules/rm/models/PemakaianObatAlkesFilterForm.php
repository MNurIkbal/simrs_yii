<?php

/**
 * @Author: DOCOTEL
 * @Date:   2018-01-19 10:12:34
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-19 10:19:33
 */

namespace app\modules\rm\models;

use Yii;

class PemakaianObatAlkesFilterForm extends \yii\base\Model
{
	public $tgl_mulai;
	public $tgl_selesai;
	public $obat_alkes;

	public function rules()
	{
		return [
			[['tgl_mulai','tgl_selesai','obat_alkes'], 'required'],
		];
	}

	public function attributeLabels()
	{
		return [
			'tgl_mulai'=>Yii::t('fe','tgl mulai'),
			'tgl_selesai'=>Yii::t('fe','tgl selesai'),
			'obat_alkes'=>Yii::t('fe', 'nama obat alkes'),
		];
	}
}
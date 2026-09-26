<?php

/**
 * @Author: Ayip
 * @Date:   2018-02-28 11:36:26
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-16 13:41:28
 */

namespace app\modules\master\models;

use Yii;

class TindakanRuanganForm extends \yii\base\Model
{
	public $ruangan_id;	
	public $daftartindakan_id;
	public $is_active;

	public function rules()
	{
		return [
			// [['ruangan_id','daftartindakan_id'], 'required'],
			[['ruangan_id','daftartindakan_id'], 'integer'],						
			[['is_active'],'safe']
		];
	}

	public function attributeLabels()
	{
		return [
			'ruangan_id'=>\Yii::t('fe','Ruangan'),
			'daftartindakan_id'=>\Yii::t('fe','Nama Tindakan'),
			'is_active'=>\Yii::t('fe','Aktif'),
		];
	}

}

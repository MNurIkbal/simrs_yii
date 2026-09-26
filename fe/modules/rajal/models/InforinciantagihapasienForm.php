<?php

/**
 * @Author: Ayip
 * @Date:   2018-03-06 11:36:26
 * @Last Modified by:   Ayip
 * @Last Modified time: 2018-03-06 11:36:26
 */

namespace app\modules\master\models;

use Yii;

class InforinciantagihapasienForm extends \yii\base\Model
{
	public $ruangan_id;	
	public $daftartindakan_id;
	public $is_active;

	public function rules()
	{
		return [];
	}

	public function attributeLabels()
	{
		return [];
	}

}

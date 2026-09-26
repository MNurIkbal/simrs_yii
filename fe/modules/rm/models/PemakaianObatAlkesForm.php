<?php

namespace app\modules\rm\models;

use Yii;

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 17:47:19
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-15 17:59:03
 */

class PemakaianObatAlkesForm extends \yii\base\Model
{
	public $satuan;	
	public $tanggal_pemakaian;
	public $kode_obat;
    public $qty;

	public function rules()
    {
        return [
            [['satuan','tanggal_pemakaian','kode_obat','qty'], 'required'],            
            [['qty'],'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'satuan' => \Yii::t('fe','satuan'),            
            'tanggal_pemakaian' => \Yii::t('fe','tanggal pemakaian'),
            'kode_obat' => \Yii::t('fe','Obat alkes'),
            'qty'=>\Yii::t('fe','qty pemakaian'),
        ];
    }
}
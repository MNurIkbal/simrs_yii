<?php

/**
 * @Author: Aris
 * @Date:   2020-09-15 14:00:00
 * @Description: 
 */

namespace app\modules\igd\models;

use Yii;

class TriaseGcsForm extends \yii\base\Model
{
	public $gcseye_id;
	public $gcsverbal_id;
	public $gcsmotorik_id;
	public $is_kapitis;
	public $jumlah_gcs;
	public $hasil_gcs;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
					'gcseye_id',
					'gcsverbal_id',
					'gcsmotorik_id',
					'is_kapitis',
					'jumlah_gcs',
					'hasil_gcs'
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'gcseye_id'      => Yii::t('fe', 'GCS Eye'),
			'gcsverbal_id'   => Yii::t('fe', 'GCS Verbal'),
			'gcsmotorik_id'  => Yii::t('fe', 'GCS Motorik'),
			'is_kapitis'     => Yii::t('fe', 'Kapitis'),
			'jumlah_gcs'	 => Yii::t('fe', 'Hasil Metode GCS'),
			'hasil_gcs'      => Yii::t('fe', 'Keterangan GCS'),
        ];
    }
}
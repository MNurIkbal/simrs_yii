<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace Doco\kasir\models;

use Yii;
use app;
use yii\db\Query;

/**
 * This is the model class for table "penjamin".
 *
 * @property int $penjamin_id
 * @property int $no_kartu
 * @property int $dijamin
 * @property string $nama_penjamin
 */

class MultiPenjamin extends \app\components\DocoBaseModel
{
    // public variable
    public $penjamin_id;
    public $no_kartu;
    public $dijamin;
    public $nama_penjamin;
    public $total_dijamin;

    public static function primaryKey()
	{
		return ['penjamin_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['penjamin_id', 'no_kartu', 'dijamin',], 'required'],
			[['nama_penjamin', 'total_dijamin'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'penjamin_id' => Yii::t('app', 'Penjamin'),
			'no_kartu' => Yii::t('app', 'Nomor Kartu'),
			'dijamin' => Yii::t('app', 'Dijamin'),
			'nama_penjamin' => Yii::t('app', 'Nama Penjamin'),
			'total_dijamin' => Yii::t('app', 'Total Dijamin'),
		];
    }
}
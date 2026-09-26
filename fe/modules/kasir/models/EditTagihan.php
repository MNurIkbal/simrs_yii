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
 * This is the model class for table "edit tagihan".
 *
 * @property int $penjamin_id
 * @property int $no_kartu
 * @property int $dijamin
 * @property int $harusbayar
 * @property string $nama_penjamin
 */

class EditTagihan extends \app\components\DocoBaseModel
{
    // public variable
    public $penjamin_id;
    public $no_kartu;
    public $dijamin;
    public $harusbayar;
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
            // [['penjamin_id', 'no_kartu', 'dijamin',], 'required'],
			[['penjamin_id', 'no_kartu', 'dijamin','nama_penjamin', 'total_dijamin', 'harusbayar'], 'safe'],
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
			'harusbayar' => Yii::t('app', 'Harus Bayar'),
			'nama_penjamin' => Yii::t('app', 'Nama Penjamin'),
			'total_dijamin' => Yii::t('app', 'Total Dijamin'),
		];
    }
}
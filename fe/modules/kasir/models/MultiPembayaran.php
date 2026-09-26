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
 * This is the model class for table "pembayaran".
 *
 * @property int $tunai
 * @property int $total
 * @property int $no_kartu
 * @property int $jenisnontunai_id
 * @property int $edclist_id
 * @property int $nominal
 * @property string $catatan
 */

class MultiPembayaran extends \app\components\DocoBaseModel
{
    // public variable
    public $tunai;
    public $total;
    public $no_kartu;
    public $jenisnontunai_id;
    public $edclist_id;
    public $nominal;
    public $catatan;

    public static function primaryKey()
	{
		return ['no_kartu'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_kartu', 'catatan', 'nominal',], 'required'],
			[['catatan', 'total', 'jenisnontunai_id',  'edclist_id'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'catatan' => Yii::t('app', 'Catatan'),
			'no_kartu' => Yii::t('app', 'Nomor Kartu'),
			'jenisnontunai_id' => Yii::t('app', 'Jenis Pembayaran'),
			'edclist_id' => Yii::t('app', 'Mesin EDC'),
			'nominal' => Yii::t('app', 'Nominal'),
			'tunai' => Yii::t('app', 'Tunai'),
			'total' => Yii::t('app', 'total'),
		];
    }
}
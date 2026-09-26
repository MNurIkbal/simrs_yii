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
 * This is the model class for table "dokter diskon".
 *
 * @property int $dokter_id
 * @property int $jasa_dokter
 * @property int $diskon
 * @property string $nama_dokter
 */

class DiskonDokter extends \app\components\DocoBaseModel
{
    // public variable
    public $dokter_id;
    public $jasa_dokter;
    public $diskon;
    public $nama_dokter;
    public $total_diskon;
    public $nominal;
    public $is_persentase;
    public $alasan;

    protected $xssProtected = [
        'alasan',
    ];

    public static function primaryKey()
	{
		return ['dokter_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dokter_id', 'jasa_dokter', 'diskon', 'nominal'], 'required'],
			[['nama_dokter', 'total_diskon', 'nominal', 'is_persentase', 'alasan'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'dokter_id' => Yii::t('app', 'Dokter'),
			'jasa_dokter' => Yii::t('app', 'Jasa Dokter'),
			'diskon' => Yii::t('app', 'Diskon'),
			'nama_dokter' => Yii::t('app', 'Nama Dokter'),
			'total_diskon' => Yii::t('app', 'Total Diskon'),
			'alasan' => Yii::t('app', 'Alasan'),
		];
    }
}
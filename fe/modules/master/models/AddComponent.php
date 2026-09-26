<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */


namespace app\modules\master\models;

use Yii;
use app;
use yii\db\Query;

/**
 * This is the model class for table "komponen".
 *
 * @property int $komponentarif_id
 * @property int $komponentarif_kode
 * @property int $total_komponen
 * @property int $harga_komponen
 * @property string $komponentarif_nama
 * @property double $persen_delegasi
 */

class AddComponent extends \app\components\DocoBaseModel
{
    // public variable
    public $komponentarif_id;
    public $komponentarif_kode;
    public $komponentarif_nama;
    public $persen_delegasi;
    public $total_komponen;
    public $harga_komponen;

    public static function primaryKey()
	{
		return ['komponentarif_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['komponentarif_id', 'komponentarif_kode'], 'required'],
			[['komponentarif_nama', 'total_komponen', 'persen_delegasi', 'harga_komponen'], 'safe'],
		];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
			'komponentarif_id' => Yii::t('app', 'Penjamin'),
			'komponentarif_kode' => Yii::t('app', 'Nomor Kartu'),
			'dijamin' => Yii::t('app', 'Dijamin'),
			'komponentarif_nama' => Yii::t('app', 'Nama Penjamin'),
			'persen_delegasi' => Yii::t('app', 'Persen'),
			'total_komponen' => Yii::t('app', 'Total Harga'),
			'harga_komponen' => Yii::t('app', 'Harga Komponen'),
		];
    }
}
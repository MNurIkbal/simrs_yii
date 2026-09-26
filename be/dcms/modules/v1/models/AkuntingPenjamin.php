<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "sync_penjamin".
 *
 * @property string $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $alamat_penjamin
 * @property string $email
 * @property string $npwp
 * @property string $no_telp
 * @property string $website
 * @property string $date
 * @property int $deleted
 */
class AkuntingPenjamin extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sync_penjamin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
  
            [['carabayar_id', 'carabayar_nama', 'penjamin_nama', 'alamat_penjamin', 'email', 'npwp', 'no_telp', 'website'], 'string', 'max' => 255],
            [['date'], 'safe'],
            [['penjamin_id', 'deleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carabayar_id' => Yii::t('app', 'Cara Bayar id'),
            'carabayar_nama' => Yii::t('app', 'Cara Bayar Nama'),
            'penjamin_nama' => Yii::t('app', 'Nama Penjamin'),
            'penjamin_id' => Yii::t('app', 'Penjamin id'),
            'alamat_penjamin' => Yii::t('app', 'Alamat Penjamin'),
            'npwp' => Yii::t('app', 'NPWP'),
            'email' => Yii::t('app', 'Alamat Email'),
            'no_telp' => Yii::t('app', 'Nomor Telepom'),
            'website' => Yii::t('app', 'Website'),
            'date' => Yii::t('app', 'Tanggal'),
        ];
    }
    
}

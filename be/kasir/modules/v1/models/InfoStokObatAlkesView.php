<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infostokobatalkes_v".
 *
 * @property integer $periodestok_id
 * @property string $periodestok_nama
 * @property integer $instalasi_id
 * @property string $instalasi_nama
 * @property integer $ruangan_id
 * @property string $ruangan_nama
 * @property integer $obatalkes_id
 * @property string $obatalkes_namalain
 * @property integer $qty_masuk
 * @property integer $qty_keluar
 * @property integer $qty_dipesan
 * @property integer $qty_tersedia
 * @property integer $qty_stok
 */
class InfoStokObatAlkesView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infostokobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['periodestok_id', 'instalasi_id', 'ruangan_id', 'obatalkes_id', 'qty_masuk', 'qty_keluar', 'qty_dipesan', 'qty_tersedia', 'qty_stok'], 'integer'],
            [['tglperiodestok_awal', 'tglperiodestok_akhir'], 'safe'],
            [['obatalkes_namalain'], 'string'],
            [['periodestok_nama'], 'string', 'max' => 100],
            [['instalasi_nama', 'ruangan_nama'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'periodestok_id' => Yii::t('app', 'Periode stok'),
            'periodestok_nama' => Yii::t('app', 'Nama periode stok'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'obatalkes_id' => Yii::t('app', 'Obat alkes'),
            'obatalkes_namalain' => Yii::t('app', 'Nama lainnya'),
            'qty_masuk' => Yii::t('app', 'Qty masuk'),
            'qty_keluar' => Yii::t('app', 'Qty keluar'),
            'qty_dipesan' => Yii::t('app', 'Qty dipesan'),
            'qty_tersedia' => Yii::t('app', 'Qty tersedia'),
            'qty_stok' => Yii::t('app', 'Qty stok'),
            'tglperiodestok_awal' => Yii::t('app', 'Periode awal'),
            'tglperiodestok_akhir' => Yii::t('app', 'Periode akhir'),
        ];
    }
}

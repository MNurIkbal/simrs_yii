<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "kertas_k".
 *
 * @property int $kertas_id
 * @property string $kertas_kode
 * @property string $kertas_nama
 * @property double $panjang
 * @property double $lebar
 * @property double $batas_kiri
 * @property double $batas_kanan
 * @property double $batas_atas
 * @property double $batas_bawah
 * @property double $ppi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class Kertas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kertas_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kertas_kode', 'kertas_nama'], 'required'],
            [['panjang', 'lebar', 'batas_kiri', 'batas_kanan', 'batas_atas', 'batas_bawah', 'ppi'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kertas_kode'], 'string', 'max' => 10],
            [['kertas_nama'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kertas_id' => Yii::t('fe','Kertas ID'),
            'kertas_kode' => Yii::t('fe','Kertas Kode'),
            'kertas_nama' => Yii::t('fe','Kertas Nama'),
            'panjang' => Yii::t('fe','Panjang'),
            'lebar' => Yii::t('fe','Lebar'),
            'batas_kiri' => Yii::t('fe','Batas Kiri'),
            'batas_kanan' => Yii::t('fe','Batas Kanan'),
            'batas_atas' => Yii::t('fe','Batas Atas'),
            'batas_bawah' => Yii::t('fe','Batas Bawah'),
            'ppi' => Yii::t('fe','Ppi'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
        ];
    }
}

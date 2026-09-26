<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 17:29:24
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisdiet_m".
 *
 * @property int $makanandiet_id
 * @property string $makanandiet_kode
 * @property string $makanandiet_nama
 * @property string $makanandiet_keterangan
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
class Makanan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'makanandiet_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['makanandiet_kode', 'makanandiet_nama'], 'required'],
            [['makanandiet_keterangan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['makanandiet_kode', 'makanandiet_nama'], 'string', 'max' => 50],
            [['makanandiet_kode'], 'unique', 'targetAttribute' => ['kodeLowercase' => 'lower(makanandiet_kode)']],
            [['makanandiet_nama'], 'unique', 'targetAttribute' => ['namaLowercase' => 'lower(makanandiet_nama)']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'makanandiet_id' => 'Makanan ID',
            'makanandiet_kode' => Yii::t('app', 'Kode'),
            'makanandiet_nama' => Yii::t('app', 'Nama Makanan'),
            'makanandiet_keterangan' => Yii::t('app', 'Keterangan'),
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => Yii::t('app', 'Status'),
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getKodeLowercase()
    {
        return strtolower($this->makanandiet_kode);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamaLowercase()
    {
        return strtolower($this->makanandiet_nama);
    }
}

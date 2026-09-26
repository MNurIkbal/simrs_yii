<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 09:38:55
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jenisdiet_m".
 *
 * @property int $jenisdiet_id
 * @property string $jenisdiet_kode
 * @property string $jenisdiet_nama
 * @property string $jenisdiet_keterangan
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
class JenisDiet extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'jenisdiet_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenisdiet_kode', 'jenisdiet_nama'], 'required'],
            [['jenisdiet_keterangan', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jenisdiet_kode', 'jenisdiet_nama'], 'string', 'max' => 50],
            [['jenisdiet_kode'], 'unique', 'targetAttribute' => ['kodeLowercase' => 'lower(jenisdiet_kode)']],
            [['jenisdiet_nama'], 'unique', 'targetAttribute' => ['namaLowercase' => 'lower(jenisdiet_nama)']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenisdiet_id' => 'Jenis Diet ID',
            'jenisdiet_kode' => Yii::t('app', 'Kode'),
            'jenisdiet_nama' => Yii::t('app', 'Nama Jenis Diet'),
            'jenisdiet_keterangan' => Yii::t('app', 'Keterangan'),
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
        return strtolower($this->jenisdiet_kode);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamaLowercase()
    {
        return strtolower($this->jenisdiet_nama);
    }
}

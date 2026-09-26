<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "loket_m".
 *
 * @property int $loket_id
 * @property int $carabayar_id
 * @property string $loket_nama
 * @property string $loket_namalain
 * @property string $loket_fungsi
 * @property string $loket_singkatan
 * @property int $loket_nourut
 * @property string $loket_formatnomor
 * @property int $loket_maxantrian
 * @property string $filesuara
 * @property bool $is_pendaftaran
 * @property bool $is_kasir
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
 * @property int $layarantrian_id
 * @property int $fungsiantrian_id
 *
 * @property AntrianT[] $antrianTs
 * @property CarabayarM $carabayar
 */
class Loket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loket_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_id', 'loket_nourut', 'loket_maxantrian', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'layarantrian_id', 'fungsiantrian_id'], 'default', 'value' => null],
            [['carabayar_id', 'loket_nourut', 'loket_maxantrian', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'layarantrian_id', 'fungsiantrian_id'], 'integer'],
            [['loket_nama', 'layarantrian_id'], 'required'],
            [['loket_fungsi', 'additional_data'], 'string'],
            [['is_pendaftaran', 'is_kasir', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['loket_nama', 'loket_namalain'], 'string', 'max' => 50],
            [['loket_singkatan'], 'string', 'max' => 1],
            [['loket_formatnomor'], 'string', 'max' => 5],
            [['filesuara'], 'string', 'max' => 500],
            [['carabayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarabayarM::className(), 'targetAttribute' => ['carabayar_id' => 'carabayar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loket_id' => 'Loket ID',
            'carabayar_id' => 'Carabayar ID',
            'loket_nama' => 'Loket Nama',
            'loket_namalain' => 'Loket Namalain',
            'loket_fungsi' => 'Loket Fungsi',
            'loket_singkatan' => 'Loket Singkatan',
            'loket_nourut' => 'Loket Nourut',
            'loket_formatnomor' => 'Loket Formatnomor',
            'loket_maxantrian' => 'Loket Maxantrian',
            'filesuara' => 'Filesuara',
            'is_pendaftaran' => 'Is Pendaftaran',
            'is_kasir' => 'Is Kasir',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'layarantrian_id' => 'Layarantrian ID',
            'fungsiantrian_id' => 'Fungsiantrian ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrianTs()
    {
        return $this->hasMany(AntrianT::className(), ['loket_id' => 'loket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(CarabayarM::className(), ['carabayar_id' => 'carabayar_id']);
    }
}

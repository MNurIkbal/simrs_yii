<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rencanapulangdetail_t".
 *
 * @property int $rencanapulangdetail_id
 nextval('rencanapulangdetail_t_rencanapulangdetail_id_seq'::regclass)
 * @property int $rencanapulang_id
 * @property string $edukasi_kesehatan
 * @property string $pemberi_edukasi
 * @property string $tgl_edukasi
 * @property int $ppa
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
class RencanaPulangDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rencanapulangdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [['rencanapulangdetail_id'], 'required'],
            [['rencanapulang_id', 'ppa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['rencanapulang_id', 'ppa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_edukasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['edukasi_kesehatan', 'pemberi_edukasi'], 'string', 'max' => 255],
            // [['rencanapulangdetail_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rencanapulangdetail_id' => 'Rencanapulangdetail ID',
            'rencanapulang_id' => 'Rencanapulang ID',
            'edukasi_kesehatan' => 'Edukasi Kesehatan',
            'pemberi_edukasi' => 'Pemberi Edukasi',
            'tgl_edukasi' => 'Tgl Edukasi',
            'ppa' => 'Ppa',
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
        ];
    }
}

<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "rencanapulang_t".
 *
 * @property int $rencanapulang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $info_penyakit
 * @property int $lama_perawatan
 * @property string $rencana_pulang
 * @property string $rencana_perawatan
 * @property string $rencana_transportasi
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
class RencanaPulangForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    
    public $rencanapulang_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $info_penyakit;
    public $lama_perawatan;
    public $rencana_pulang;
    public $rencana_perawatan;
    public $rencana_transportasi;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    

    public static function tableName()
    {
        return 'rencanapulang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id','info_penyakit','lama_perawatan','rencana_pulang','rencana_perawatan','rencana_transportasi'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'lama_perawatan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['info_penyakit', 'rencana_perawatan', 'rencana_transportasi', 'additional_data'], 'string'],
            [['lama_perawatan'], 'integer'],
            [['rencana_pulang', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rencanapulang_id' => 'Rencanapulang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'info_penyakit' => 'Informasi Tentang Penyakit',
            'lama_perawatan' => 'Perkiraan Lama Perawatan',
            'rencana_pulang' => 'Tanggal Rencana Pulang',
            'rencana_perawatan' => 'Rencana Perawatan di rumah dilakukan oleh',
            'rencana_transportasi' => 'Rencana Transportasi Pulang',
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

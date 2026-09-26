<?php

namespace app\modules\ranap\models;

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
class RencanaPulangDetailForm extends \yii\base\Model
{
    /**
     * @inheritdoc
     */
    
    public $rencanapulangdetail_id;
    public $rencanapulang_id;
    public $edukasi_kesehatan;
    public $pemberi_edukasi;
    public $tgl_edukasi;
    public $ppa;
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

    public $pemberi_edukasi_opsional;
    public $tgl_edukasi_opsional;
    public $ppa_opsional;

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
            [['rencanapulangdetail_id', 'rencanapulang_id', 'ppa', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['rencanapulangdetail_id', 'rencanapulang_id', /*'ppa',*/ 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_edukasi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['pemberi_edukasi'], 'string', 'max' => 255],
            [['rencanapulangdetail_id'], 'unique'],
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
            'tgl_edukasi' => 'Tanggal Edukasi',
            'ppa' => 'PPA',
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

<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-18 10:01:14
 */

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "fasilitasrs_m".
 *
 * @property int $fasilitasrsdetail_id
 * @property int $fasilitasrs_id
 * @property string $jenis_fasilitas instalasi_m, free text
 * @property string $nama_fasilitas ruangan_m berdasarkan instalasi_id, free text
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
class FasilitasRsDetailForm extends \yii\base\Model
{
	/**
     * {@inheritdoc}
     */
    public $fasilitasrsdetail_id;
    public $fasilitasrs_id;
    public $nama_fasilitas;
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fasilitasrs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['fasilitasrs_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nama_fasilitas', 'additional_data'], 'string'],
            [['nama_fasilitas'], 'required'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fasilitasrsdetail_id' => 'Fasilitas Detail Rs ID',
            'fasilitasrs_id' => 'Fasilitas Rs ID',
            'nama_fasilitas' => 'Nama Fasilitas',
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

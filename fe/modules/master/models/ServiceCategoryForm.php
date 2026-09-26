<?php

namespace app\modules\master\models;

use Yii;
use yii\base\Model;

class ServiceCategoryForm extends Model {
    /**
     * @inheritdoc
     */

    public $servicecategory_id;
    public $servicecategory_nama;
    public $is_obat;
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['servicecategory_nama'
            ], 'required',
                'message'=>'{attribute} Tidak boleh kosong'],
            [['servicecategory_id',
                'modified_count',
                'last_modified_by',
                'deleted_by'], 'integer'],
            [['servicecategory_nama', 'additional_data'], 'string'],
            [['is_obat', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'servicecategory_id' => 'Service Category ID',
            'servicecategory_nama' => 'Nama Service Category',
            'is_obat' => '',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By'
        ];
    }
}

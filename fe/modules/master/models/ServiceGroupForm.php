<?php

namespace app\modules\master\models;

use Yii;
use yii\base\Model;

class ServiceGroupForm extends Model {
    /**
     * @inheritdoc
     */

    public $servicegroup_id;
    public $servicegroup_nama;
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
            [['servicegroup_nama'
            ], 'required',
                'message'=>'{attribute} Tidak boleh kosong'],
            [['servicegroup_id',
                'modified_count',
                'last_modified_by',
                'deleted_by'], 'integer'],
            [['servicegroup_nama', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'servicegroup_id' => 'Service Group ID',
            'servicegroup_nama' => 'Nama Service Group',
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

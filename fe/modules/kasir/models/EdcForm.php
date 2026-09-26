<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "esselon_m".
 *
 * @property integer $esselon_id
 * @property string $esselon_nama
 * @property string $esselon_namalainnya
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class EdcForm extends \yii\base\Model
{
    public $edclist_kode;
    public $edclist_namamesin;
    public $edclist_bank;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['edclist_kode', 'edclist_namamesin', 'edclist_bank', 'is_active'], 'required'],
            [['edclist_kode', 'edclist_namamesin', 'edclist_bank'], 'safe'],
            [['is_active'], 'boolean'],
            [['edclist_kode'], 'string', 'max' => 50],
            [['edclist_namamesin'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'edclist_kode' => 'Kode Mesin EDC',
            'edclist_namamesin' => 'Nama Mesin EDC',
            'edclist_bank' => 'Bank',
            'is_active' => 'Status',
        ];
    }
}

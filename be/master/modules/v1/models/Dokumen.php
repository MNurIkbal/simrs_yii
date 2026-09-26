<?php

namespace app\modules\v1\models;

use Yii;

class Dokumen extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokumen_m';
    }

    protected $xssProtected = [
        'nama_dokumen',
        'nama_dokumen_lainnya',
    ];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nama_dokumen', 'jenis_dokumen_id'], 'required'],
            [['nama_dokumen', 'nama_dokumen_lainnya'], 'trim'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active', 'is_eklaim'], 'boolean'],
            [['nama_dokumen'], 'chkNama'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dokumen_id' => 'Dokumen ID',
            'nama_dokumen' => 'Nama Dokumen',
            'nama_dokumen_lainnya' => 'Nama Dokumen Lainnya',
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

    public function chkNama($params, $attributes)
    {
        $namaDokumen = $this->nama_dokumen;
        $model = self::find()->where([
            'TRIM(LOWER (nama_dokumen))' => strtolower($namaDokumen), 
            'is_deleted' => false
        ])->one();
        if(!empty($model) && $model->dokumen_id != $this->dokumen_id ){
            $this->addError("nama_dokumen","Nama Dokumen Sudah Dipakai");
            return false;
        }
    
        return true;
    }
}

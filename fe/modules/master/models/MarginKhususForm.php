<?php

namespace app\modules\master\models;

use Yii;

class MarginKhususForm extends \yii\base\Model
{
     public $perda;
     public $nama;
     public $mulai_berlaku;
     public $detail;
     public $additional_data;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama', 'perda', 'mulai_berlaku'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['nama','perda','additional_data'], 'safe'],
            [['perda'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigmargin_id' => 'Konfigmargin ID',
            'perda' => 'Perda/SK',
            'nama' => 'Nama',
            'mulai_berlaku' => 'Tanggal Berlaku',
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

<?php

namespace app\modules\v1\models;

use yii\db\ActiveRecord;

class DokumenMedisBantaran extends ActiveRecord
{
    public static function tableName()
    {
        return 'dokumenmedisbantaran_t';
    }

    public function rules()
    {
        return [
            [['rujukanbantaran_id', 'pengajuan_id', 'nama_dokumen', 'url_dokumen'], 'required'],
            [['rujukanbantaran_id', 'pengajuan_id', 'created_by', 'last_modified_by', 'deleted_by'], 'integer'],
            [['url_dokumen'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_dokumen'], 'string', 'max' => 200],
        ];
    }
}

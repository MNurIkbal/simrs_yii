<?php

namespace SirsCore\models;

class TindakanLuarOperasi extends \Doco\components\DocoActiveRecord
{

    public $_repositori = 'app\components\repositories\TindakanLuarOperasiRepositories';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tindakanluaroperasi_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'qty', 'harga', 'pasienmasukpenunjang_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'is_deleted', 'is_active'], 'safe'],
        ];
    }
}

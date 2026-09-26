<?php

namespace app\modules\v1\models;

use Yii;


class InfoTerimaMutasiBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoterimamutasibarangdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'terimamutasibarangdetail_id', 
                'terimamutasibarang_id', 
                'barang_id',
                'barang_nama',
                'satuankecil_id',
                'satuanunit_nama',
                'jmlterima',
                'tglkadaluarsa',
            ], 'safe']
        ];
    }
}

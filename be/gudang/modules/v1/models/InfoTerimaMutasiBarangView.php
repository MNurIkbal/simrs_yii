<?php

namespace app\modules\v1\models;

use Yii;


class InfoTerimaMutasiBarangView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoterimamutasibarang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'terimamutasibarang_id', 
                'tglterima', 
                'noterimamutasi',
                'ruanganasalmutasi_id',
                'ruangan_pengirim',
                'instalasi_id',
                'instalasi_pengirim',
                'pegawaimengetahui_id',
                'pegawaimenyetujui_id',
                'pegawai_mengetahui',
                'pegawai_menyetujui',
                'ruanganpenerima_id',
                'ruangan_penerima'
            ], 'safe']
        ];
    }
}

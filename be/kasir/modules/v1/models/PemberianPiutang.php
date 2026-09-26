<?php

/**
* @author Budi
*/

namespace app\modules\v1\models;

use Yii;

class PemberianPiutang extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\PemberianPiutangRepositories';
    protected $xssProtected = [
        'alasan_batal'
    ];

    public static function tableName()
    {
        return 'pemberianpiutang_t';
    }

    public function rules()
    {
        return [
            [['tgl_pemberianpiutang', 'pegawai_id', 'total_piutang'], 'required'],
            [[
                'pendaftaran_id', 
                'tgl_pemberianpiutang', 
                'pegawai_id', 
                'catatan', 
                'total_sisapiutang', 
                'no_pemberianpiutang',
                'penjualanresep_id',
                'pegawaimengetahui_id',
                'alasan_batal'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pegawai_id' => 'Di Approve Oleh',
            'total_bayarpiutang' => 'Sudah Bayar',
            'total_sisapiutang' => 'Balance Piutang',
            'total_piutang' => 'Jumlah Piutang',
            'pegawaimengetahui_id' => 'Pegawai',
            'penjualanresep_id' => 'Penjualan Resep',
        ];
    }
}
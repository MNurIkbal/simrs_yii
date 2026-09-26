<?php

/**
* @author Budi
*/

namespace app\modules\v1\models;

use Yii;

class PemberianPiutang extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'pemberianpiutang_t';
    }

    public function rules()
    {
        return [
            [['pendaftaran_id', 'tgl_pemberianpiutang', 'pegawai_id', 'total_piutang'], 'required'],
            [['pendaftaran_id', 'tgl_pemberianpiutang', 'no_pendaftaran', 'pegawai_id', 'catatan', 'total_sisapiutang', 'no_pemberianpiutang'], 'safe'],
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
        ];
    }
}
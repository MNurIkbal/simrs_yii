<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "approvaldiskon_t".
 *
 * @property int $diskon
 * @property int $limit_diskon
 * @property int $pembayaran_id
 * @property int $jumlah_limit_diskon
 * @property int $jumlah_diskon
 * @property int $additional_data
 * @property int $pendaftaran_id
 * @property int $status_approve
 * @property int $pegawai_approve_id
 * @property int $tgl_approve
 */
class ApprovalDiskonT extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'approvaldiskon_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['limit_diskon', 'diskon', "jumlah_limit_diskon", "pendaftaran_id"], 'required'],
            [['additional_data', 'status_approve', "pembayaran_id", "pegawai_approve_id", "tgl_approve"], 'safe']
        ];
    }
}

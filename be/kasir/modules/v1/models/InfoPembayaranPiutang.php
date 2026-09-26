<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopembayaranpiutang_v".
 *
 * @property integer $pembayaranpiutang_id
 * @property string $no_pembayaranpiutang
 * @property string $tgl_pembayaranpiutang
 * @property integer $pendaftaran_id
 * @property string $no_pendaftaran
 * @property integer $pasien_id
 * @property integer $no_rekam_medik
 * @property string $nama_pasien
 * @property integer $total_bayarpiutang
 */
class InfoPembayaranPiutang extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopembayaranpiutang_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpiutang_id', 'no_pembayaranpiutang', 'tgl_pembayaranpiutang', 'pendaftaran_id', 'no_pendaftaran', 'pasien_id', 'no_rekam_medik', 'nama_pasien', 'total_bayarpiutang'], 'safe'],
        ];
    }
}

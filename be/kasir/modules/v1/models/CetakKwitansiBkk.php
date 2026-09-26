<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "cetakkwitansibkm_v".
 *
 * @property int $pembayaranpelayanan_id
 * @property int $pendaftaran_id
 * @property int $instalasi_id
 * @property string $no_kwitansi
 * @property string $no_bkm
 * @property string $no_pendaftaran
 * @property string $tgl_pendaftaran
 * @property string $tgl_pembayaran
 * @property string $tglpulang_pendaftaran
 * @property string $tglpulang_ranap
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property double $total_terbayar
 * @property string $kasir
 */
class CetakKwitansiBkk extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cetakkwitansibkk_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'pendaftaran_id', 'instalasi_id'], 'default', 'value' => null],
            [['pembayaranpelayanan_id', 'pendaftaran_id', 'instalasi_id'], 'integer'],
            [['tgl_pendaftaran', 'tgl_pembayaran', 'tglpulang_pendaftaran', 'tglpulang_ranap'], 'safe'],
            [['total_terbayar'], 'number'],
            [['no_kwitansi', 'no_bkm', 'nama_pasien', 'kasir'], 'string', 'max' => 50],
            [['no_pendaftaran'], 'string', 'max' => 20],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

}

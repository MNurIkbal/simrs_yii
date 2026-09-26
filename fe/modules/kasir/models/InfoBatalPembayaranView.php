<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for view "infobatalpembayaran_v".
 *
 * @property date $tanggal_batal
 * @property string $dibatalkan_oleh
 * @property string $no_pembayaran
 * @property date $tanggal_pembayaran
 * @property string $nama_pasien
 * @property string $no_rm
 * @property string $no_pendaftaran
 * @property int $jumlah_tagihan
 * @property string $alasan_batal
 *
 */

class InfBatalPembayaranView extends \yii\base\Model
{
    public $tanggal_batal;
    public $dibatalkan_oleh;
    public $no_pembayaran;
    public $tanggal_pembayaran;
    public $nama_pasien;
    public $no_rm;
    public $no_pendaftaran;
    public $jumlah_tagihan;
    public $alasan_batal;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'tanggal_batal',
                'dibatalkan_oleh',
                'no_pembayaran',
                'tanggal_pembayaran',
                'nama_pasien',
                'no_rm',
                'no_pendaftaran',
                'jumlah_tagihan',
                'alasan_batal',
            ], 'safe']
        ];
    }

}

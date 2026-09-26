<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopemesananbarang_v".
 *
 * @property int pesanbarang_id
 * @property date tgl_pesanbarang
 * @property int instalasipemesan_id
 * @property string instalasi_pemesan
 * @property int ruanganpemesan_id
 * @property string ruangan_pemesan
 * @property int instalasi_id
 * @property string instalasi_tujuan
 * @property int ruangan_id
 * @property string ruangan_tujuan
 * @property string no_pemesanan
 * @property string status_pesan
 * @property date tgl_mintadikirim
 * @property text keterangan_pesan
 */
class InfoPemesananBarangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopemesananbarangdetail_v';
    }
}

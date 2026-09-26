<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infopengajuanklaim_v".
 *
 * @property int $pengajuanklaim_id
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property string $tgl_pengajuanklaim
 * @property string $no_pengajuanklaim
 * @property string $tgl_jatuhtempo
 * @property string $tgl_pelayanansampai
 * @property string $tgl_pelayanandari
 * @property double $total_piutang
 * @property double $total_terbayar
 * @property double $total_sisapiutang
 * @property string $alamat_penjamin
 * @property string $npwp
 * @property double $totalbiaya_obat
 * @property double $totalbiaya_tindakan
 * @property int $pegawaimengetahui_id
 * @property string $catatan
 * @property int $status_pengajuanklaim
 * @property string $s_pengajuanklaim
 * @property bool $is_deleted
 * @property bool $is_active
 */
class InfoPengajuanKlaimView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopengajuanklaim_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pengajuanklaim_id', 'carabayar_id', 'penjamin_id', 'pegawaimengetahui_id', 'status_pengajuanklaim'], 'default', 'value' => null],
            [['pengajuanklaim_id', 'carabayar_id', 'penjamin_id', 'pegawaimengetahui_id', 'status_pengajuanklaim'], 'integer'],
            [['tgl_pengajuanklaim', 'tgl_jatuhtempo', 'tgl_pelayanansampai', 'tgl_pelayanandari'], 'safe'],
            [['total_piutang', 'total_terbayar', 'total_sisapiutang', 'totalbiaya_obat', 'totalbiaya_tindakan'], 'number'],
            [['alamat_penjamin', 'catatan'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['no_pengajuanklaim'], 'string', 'max' => 255],
            [['npwp'], 'string', 'max' => 100],
            [['s_pengajuanklaim'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pengajuanklaim_id' => 'Pengajuanklaim ID',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'tgl_pengajuanklaim' => 'Tgl Pengajuanklaim',
            'no_pengajuanklaim' => 'No Pengajuanklaim',
            'tgl_jatuhtempo' => 'Tgl Jatuhtempo',
            'tgl_pelayanansampai' => 'Tgl Pelayanansampai',
            'tgl_pelayanandari' => 'Tgl Pelayanandari',
            'total_piutang' => 'Total Piutang',
            'total_terbayar' => 'Total Terbayar',
            'total_sisapiutang' => 'Total Sisapiutang',
            'alamat_penjamin' => 'Alamat Penjamin',
            'npwp' => 'Npwp',
            'totalbiaya_obat' => 'Totalbiaya Obat',
            'totalbiaya_tindakan' => 'Totalbiaya Tindakan',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'catatan' => 'Catatan',
            'status_pengajuanklaim' => 'Status Pengajuanklaim',
            's_pengajuanklaim' => 'S Pengajuanklaim',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }
}

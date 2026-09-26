<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\models;

use Yii;
use app;
use yii\db\Query;

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
class InfoPembayaranPiutang extends \yii\base\Model
{
    public $pembayaranpiutang_id;
    public $no_pembayaranpiutang;
    public $tgl_pembayaranpiutang;
    public $pendaftaran_id;
    public $no_pendaftaran;
    public $pasien_id;
    public $no_rekam_medik;
    public $nama_pasien;
    public $total_bayarpiutang;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pembayaranpiutang_id', 'no_pembayaranpiutang', 'tgl_pembayaranpiutang', 'pendaftaran_id', 'no_pendaftaran', 'pasien_id', 'no_rekam_medik', 'nama_pasien', 'total_bayarpiutang'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembayaranpiutang_id' => Yii::t('fe', 'Pembayaran Piutang ID'),
            'no_pembayaranpiutang' => Yii::t('fe', 'Nomor Pembayaran Piutang'),
            'tgl_pembayaranpiutang' => Yii::t('fe', 'Tanggal Pembayaran Piutang'),
            'pendaftaran_id' => Yii::t('fe', 'Pendaftaran ID'),
            'no_pendaftaran' => Yii::t('fe', 'Nomor Pendaftaran'),
            'pasien_id' => Yii::t('fe', 'Pasien ID'),
            'no_rekam_medik' => Yii::t('fe', 'Nomor Rekam Medik'),
            'nama_pasien' => Yii::t('fe', 'Nama Pasien'),
            'total_bayarpiutang' => Yii::t('fe', 'Total Piutang'),
        ];
    }
}

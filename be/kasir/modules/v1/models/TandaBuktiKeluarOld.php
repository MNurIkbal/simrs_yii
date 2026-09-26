<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tandabuktikeluar_t".
 *
 * @property int $tandabuktikeluar_id
 * @property int $returbayarpelayanan_id
 * @property int $pembatalanuangmuka_id
 * @property int $shift_id
 * @property int $pengeluaranumum_id
 * @property int $batalbayarsupplier_id
 * @property int $uangmukabeli_id
 * @property int $batalkeluarumum_id
 * @property int $bayarkesupplier_id
 * @property int $ruangan_id
 * @property int $returpenerimaanumum_id
 * @property int $bank_id
 * @property int $pegawai1_id
 * @property int $pegawai2_id
 * @property int $pegawai3_id
 * @property int $pegawai4_id
 * @property string $tahun
 * @property string $tgl_kaskeluar
 * @property string $no_kaskeluar
 * @property string $carabayar_keluar
 * @property string $melalui_bank
 * @property string $dengan_rekening
 * @property string $atasnama_rekening
 * @property string $nama_penerima
 * @property string $alamat_penerima
 * @property string $untuk_pembayaran
 * @property double $jmlkas_keluar
 * @property double $biaya_administrasi
 * @property string $keterangan_pengeluaran
 * @property string $norekening_penerima
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class TandaBuktiKeluar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tandabuktikeluar_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['returbayarpelayanan_id', 'pembatalanuangmuka_id', 'shift_id', 'ruangan_id', 'bank_id', 'pegawai1_id', 'pegawai2_id', 'pegawai3_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['returbayarpelayanan_id', 'pembatalanuangmuka_id', 'shift_id', 'ruangan_id', 'bank_id', 'pegawai1_id', 'pegawai2_id', 'pegawai3_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['shift_id', 'ruangan_id', 'tahun', 'tgl_kaskeluar', 'no_kaskeluar', 'nama_penerima', 'jmlkas_keluar'], 'required'],
            [['tgl_kaskeluar', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alamat_penerima', 'keterangan_pengeluaran', 'additional_data'], 'string'],
            [['jmlkas_keluar', 'biaya_administrasi'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['tahun'], 'string', 'max' => 4],
            [['no_kaskeluar', 'carabayar_keluar'], 'string', 'max' => 50],
            [['melalui_bank', 'dengan_rekening', 'atasnama_rekening', 'nama_penerima', 'untuk_pembayaran', 'norekening_penerima'], 'string', 'max' => 100],
            // [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankM::className(), 'targetAttribute' => ['bank_id' => 'bank_id']],
            // [['batalbayarsupplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => BatalbayarsupplierT::className(), 'targetAttribute' => ['batalbayarsupplier_id' => 'batalbayarsupplier_id']],
            // [['batalkeluarumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => BatalkeluarumumT::className(), 'targetAttribute' => ['batalkeluarumum_id' => 'batalkeluarumum_id']],
            // [['bayarkesupplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => BayarkesupplierT::className(), 'targetAttribute' => ['bayarkesupplier_id' => 'bayarkesupplier_id']],
            // [['pegawai1_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai1_id' => 'pegawai_id']],
            // [['pegawai2_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai2_id' => 'pegawai_id']],
            // [['pegawai3_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai3_id' => 'pegawai_id']],
            // [['pegawai4_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai4_id' => 'pegawai_id']],
            // [['pembatalanuangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembatalanuangmukaT::className(), 'targetAttribute' => ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']],
            // [['pengeluaranumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => PengeluaranumumT::className(), 'targetAttribute' => ['pengeluaranumum_id' => 'pengeluaranumum_id']],
            [['returbayarpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturBayarPelayanan::className(), 'targetAttribute' => ['returbayarpelayanan_id' => 'returbayarpelayanan_id']],
            // [['returpenerimaanumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturpenerimaanumumT::className(), 'targetAttribute' => ['returpenerimaanumum_id' => 'returpenerimaanumum_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftM::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
            // [['uangmukabeli_id'], 'exist', 'skipOnError' => true, 'targetClass' => UangmukabeliT::className(), 'targetAttribute' => ['uangmukabeli_id' => 'uangmukabeli_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tandabuktikeluar_id' => Yii::t('app', 'Tanda bukti keluar'),
            'returbayarpelayanan_id' => Yii::t('app', 'Retur bayar pelayanan'),
            'pembatalanuangmuka_id' => Yii::t('app', 'Pembatalan uang muka'),
            'shift_id' => Yii::t('app', 'Shift'),
            'pengeluaranumum_id' => Yii::t('app', 'Pengeluaran umum'),
            'batalbayarsupplier_id' => Yii::t('app', 'Batal bayar supplier'),
            'uangmukabeli_id' => Yii::t('app', 'Uang muka beli'),
            'batalkeluarumum_id' => Yii::t('app', 'Batal keluar umum'),
            'bayarkesupplier_id' => Yii::t('app', 'Bayar ke supplier'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'returpenerimaanumum_id' => Yii::t('app', 'Retur penerimaan umum'),
            'bank_id' => Yii::t('app', 'Bank'),
            'pegawai1_id' => Yii::t('app', 'Pegawai 1'),
            'pegawai2_id' => Yii::t('app', 'Pegawai 2'),
            'pegawai3_id' => Yii::t('app', 'Pegawai 3'),
            'pegawai4_id' => Yii::t('app', 'Pegawai 4'),
            'tahun' => Yii::t('app', 'Tahun'),
            'tgl_kaskeluar' => Yii::t('app', 'Tanggal kas keluar'),
            'no_kaskeluar' => Yii::t('app', 'No kas keluar'),
            'carabayar_keluar' => Yii::t('app', 'Cara bayar keluar'),
            'melalui_bank' => Yii::t('app', 'Melalui bank'),
            'dengan_rekening' => Yii::t('app', 'Dengan rekening'),
            'atasnama_rekening' => Yii::t('app', 'Atas nama rekening'),
            'nama_penerima' => Yii::t('app', 'Nama penerima'),
            'alamat_penerima' => Yii::t('app', 'Alamat penerima'),
            'untuk_pembayaran' => Yii::t('app', 'Untuk pembayaran'),
            'jmlkas_keluar' => Yii::t('app', 'Jumlah kas keluar'),
            'biaya_administrasi' => Yii::t('app', 'Biaya administrasi'),
            'keterangan_pengeluaran' => Yii::t('app', 'Keterangan pengeluaran'),
            'norekening_penerima' => Yii::t('app', 'Norekening penerima'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }
}

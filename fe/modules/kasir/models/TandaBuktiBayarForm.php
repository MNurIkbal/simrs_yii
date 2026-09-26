<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "tandabuktibayar_t".
 *
 * @property int $tandabuktibayar_id
 * @property int $ruangan_id
 * @property int $pembatalanuangmuka_id
 * @property int $bayaruangmuka_id
 * @property int $closingkasir_id
 * @property int $returpenerimaanumum_id
 * @property int $pembayaranpelayanan_id
 * @property int $returbayarpelayanan_id
 * @property int $bank_id
 * @property int $shift_id
 * @property int $pembayarankapitasidetail_id
 * @property int $pembayarklaim_id
 * @property int $penerimaanumum_id
 * @property int $nourutkasir
 * @property string $nobuktibayar
 * @property string $tglbuktibayar
 * @property string $carapembayaran
 * @property string $dengankartu
 * @property string $bankkartu
 * @property string $nokartu
 * @property string $nostrukkartu
 * @property string $darinama_bkm
 * @property string $alamat_bkm
 * @property string $sebagaipembayaran_bkm
 * @property double $jmlpembulatan
 * @property double $jmlpembayaran
 * @property double $biayaadministrasi
 * @property double $biayamaterai
 * @property double $uangditerima
 * @property double $uangkembalian
 * @property double $keterangan_pembayaran
 * @property bool $is_print
 * @property int $pegawai1_id
 * @property int $pegawai2_id
 * @property int $pegawai3_id
 * @property int $pegawai4_id
 * @property bool $pjs_tax
 * @property bool $pjs_controller
 * @property string $namapenerima
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
class TandaBuktiBayarForm extends \yii\base\Model
{
    
    public $tandabuktibayar_id;
    public $ruangan_id;
    public $pembatalanuangmuka_id;
    public $bayaruangmuka_id;
    public $closingkasir_id;
    public $returpenerimaanumum_id;
    public $pembayaranpelayanan_id;
    public $returbayarpelayanan_id;
    public $bank_id;
    public $shift_id;
    public $pembayarankapitasidetail_id;
    public $pembayarklaim_id;
    public $penerimaanumum_id;
    public $nourutkasir;
    public $nobuktibayar;
    public $tglbuktibayar;
    public $carapembayaran;
    public $dengankartu;
    public $bankkartu;
    public $nokartu;
    public $nostrukkartu;
    public $darinama_bkm;
    public $alamat_bkm;
    public $sebagaipembayaran_bkm;
    public $jmlpembulatan;
    public $jmlpembayaran;
    public $biayaadministrasi;
    public $biayamaterai;
    public $uangditerima;
    public $uangkembalian;
    public $keterangan_pembayaran;
    public $is_print;
    public $pegawai1_id;
    public $pegawai2_id;
    public $pegawai3_id;
    public $pegawai4_id;
    public $pjs_tax;
    public $pjs_controller;
    public $namapenerima;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tandabuktibayar_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'shift_id', 'nourutkasir', 'nobuktibayar', 'tglbuktibayar', 'carapembayaran', 'darinama_bkm', 'alamat_bkm', 'sebagaipembayaran_bkm', 'jmlpembulatan', 'jmlpembayaran', 'biayamaterai', 'uangditerima', 'uangkembalian', 'pegawai1_id'], 'required'],
            [['ruangan_id', 'pembatalanuangmuka_id', 'bayaruangmuka_id', 'closingkasir_id', 'returpenerimaanumum_id', 'pembayaranpelayanan_id', 'returbayarpelayanan_id', 'bank_id', 'shift_id', 'pembayarankapitasidetail_id', 'pembayarklaim_id', 'penerimaanumum_id', 'nourutkasir', 'pegawai1_id', 'pegawai2_id', 'pegawai3_id', 'pegawai4_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'pembatalanuangmuka_id', 'bayaruangmuka_id', 'closingkasir_id', 'returpenerimaanumum_id', 'pembayaranpelayanan_id', 'returbayarpelayanan_id', 'bank_id', 'shift_id', 'pembayarankapitasidetail_id', 'pembayarklaim_id', 'penerimaanumum_id', 'nourutkasir', 'pegawai1_id', 'pegawai2_id', 'pegawai3_id', 'pegawai4_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglbuktibayar', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['alamat_bkm', 'additional_data'], 'string'],
            [['jmlpembulatan', 'jmlpembayaran', 'biayaadministrasi', 'biayamaterai', 'uangditerima', 'uangkembalian', 'keterangan_pembayaran'], 'number'],
            [['is_print', 'pjs_tax', 'pjs_controller', 'is_deleted', 'is_active'], 'boolean'],
            [['nobuktibayar', 'carapembayaran', 'dengankartu'], 'string', 'max' => 50],
            [['bankkartu', 'nokartu', 'nostrukkartu', 'darinama_bkm', 'sebagaipembayaran_bkm', 'namapenerima'], 'string', 'max' => 100],
            // [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => BankM::className(), 'targetAttribute' => ['bank_id' => 'bank_id']],
            // [['bayaruangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => BayaruangmukaT::className(), 'targetAttribute' => ['bayaruangmuka_id' => 'bayaruangmuka_id']],
            // [['closingkasir_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClosingkasirT::className(), 'targetAttribute' => ['closingkasir_id' => 'closingkasir_id']],
            // [['pegawai1_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai1_id' => 'pegawai_id']],
            // [['pegawai2_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai2_id' => 'pegawai_id']],
            // [['pegawai3_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai3_id' => 'pegawai_id']],
            // [['pegawai4_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai4_id' => 'pegawai_id']],
            // [['pembatalanuangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembatalanuangmukaT::className(), 'targetAttribute' => ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']],
            // [['pembayarankapitasidetail_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayarankapitasiT::className(), 'targetAttribute' => ['pembayarankapitasidetail_id' => 'pembayarankapitasi_id']],
            // [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranpelayananT::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            // [['pembayarklaim_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayarklaimT::className(), 'targetAttribute' => ['pembayarklaim_id' => 'pembayarklaim_id']],
            // [['penerimaanumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenerimaanumumT::className(), 'targetAttribute' => ['penerimaanumum_id' => 'penerimaanumum_id']],
            // [['returbayarpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturBayarPelayanan::className(), 'targetAttribute' => ['returbayarpelayanan_id' => 'returbayarpelayanan_id']],
            // [['returpenerimaanumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturpenerimaanumumT::className(), 'targetAttribute' => ['returpenerimaanumum_id' => 'returpenerimaanumum_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            // [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => ShiftM::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tandabuktibayar_id' => Yii::t('fe', 'Tanda bukti bayar'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'pembatalanuangmuka_id' => Yii::t('fe', 'Pembatalan uang muka'),
            'bayaruangmuka_id' => Yii::t('fe', 'Bayar uang muka'),
            'closingkasir_id' => Yii::t('fe', 'Closing kasir'),
            'returpenerimaanumum_id' => Yii::t('fe', 'Retur penerimaan umum'),
            'pembayaranpelayanan_id' => Yii::t('fe', 'Pembayaran pelayanan'),
            'returbayarpelayanan_id' => Yii::t('fe', 'Retur bayar pelayanan'),
            'bank_id' => Yii::t('fe', 'Bank'),
            'shift_id' => Yii::t('fe', 'Shift'),
            'pembayarankapitasidetail_id' => Yii::t('fe', 'Pembayaran kapitasi detail'),
            'pembayarklaim_id' => Yii::t('fe', 'Pembayar klaim'),
            'penerimaanumum_id' => Yii::t('fe', 'Penerimaan umum'),
            'nourutkasir' => Yii::t('fe', 'No urut kasir'),
            'nobuktibayar' => Yii::t('fe', 'No bukti bayar'),
            'tglbuktibayar' => Yii::t('fe', 'Tanggal bukti bayar'),
            'carapembayaran' => Yii::t('fe', 'Cara pembayaran'),
            'dengankartu' => Yii::t('fe', 'Dengan kartu'),
            'bankkartu' => Yii::t('fe', 'Bank kartu'),
            'nokartu' => Yii::t('fe', 'No kartu'),
            'nostrukkartu' => Yii::t('fe', 'No struk kartu'),
            'darinama_bkm' => Yii::t('fe', 'Dari nama Bkm'),
            'alamat_bkm' => Yii::t('fe', 'Alamat Bkm'),
            'sebagaipembayaran_bkm' => Yii::t('fe', 'Sebagai pembayaran Bkm'),
            'jmlpembulatan' => Yii::t('fe', 'Jumlah pembulatan'),
            'jmlpembayaran' => Yii::t('fe', 'Jumlah pembayaran'),
            'biayaadministrasi' => Yii::t('fe', 'Biaya administrasi'),
            'biayamaterai' => Yii::t('fe', 'Biaya materai'),
            'uangditerima' => Yii::t('fe', 'Uang diterima'),
            'uangkembalian' => Yii::t('fe', 'Uang kembalian'),
            'keterangan_pembayaran' => Yii::t('fe', 'Keterangan pembayaran'),
            'is_print' => Yii::t('fe', 'Is print'),
            'pegawai1_id' => Yii::t('fe', 'Pegawai 1'),
            'pegawai2_id' => Yii::t('fe', 'Pegawai 2'),
            'pegawai3_id' => Yii::t('fe', 'Pegawai 3'),
            'pegawai4_id' => Yii::t('fe', 'Pegawai 4'),
            'pjs_tax' => Yii::t('fe', 'Pjs Tax'),
            'pjs_controller' => Yii::t('fe', 'Pjs Controller'),
            'namapenerima' => Yii::t('fe', 'Nama penerima'),
            'additional_data' => Yii::t('fe', 'Additional Data'),
            'created_date' => Yii::t('fe', 'Created Date'),
            'created_by' => Yii::t('fe', 'Created By'),
            'modified_count' => Yii::t('fe', 'Modified Count'),
            'last_modified_date' => Yii::t('fe', 'Last Modified Date'),
            'last_modified_by' => Yii::t('fe', 'Last Modified By'),
            'is_deleted' => Yii::t('fe', 'Is Deleted'),
            'is_active' => Yii::t('fe', 'Is Active'),
            'deleted_date' => Yii::t('fe', 'Deleted Date'),
            'deleted_by' => Yii::t('fe', 'Deleted By'),
        ];
    }
}

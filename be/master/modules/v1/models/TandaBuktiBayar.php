<?php

namespace app\modules\v1\models;

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

 *
 * @property BayarangsuranpelayananT[] $bayarangsuranpelayananTs
 * @property BayaruangmukaT[] $bayaruangmukaTs
 * @property InvoicekeluarT[] $invoicekeluarTs
 * @property PembatalanuangmukaT[] $pembatalanuangmukaTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PembklaimdetailT[] $pembklaimdetailTs
 * @property PenerimaanumumT[] $penerimaanumumTs
 * @property RealisasianggpenerimaanT[] $realisasianggpenerimaanTs
 * @property ReturbayarpelayananT[] $returbayarpelayananTs
 * @property ReturpenerimaanumumT[] $returpenerimaanumumTs
 * @property BankM $bank
 * @property BayaruangmukaT $bayaruangmuka
 * @property ClosingkasirT $closingkasir
 * @property PegawaiM $pegawai1
 * @property PegawaiM $pegawai2
 * @property PegawaiM $pegawai3
 * @property PegawaiM $pegawai4
 * @property PembatalanuangmukaT $pembatalanuangmuka
 * @property PembayarankapitasiT $pembayarankapitasidetail
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property PembayarklaimT $pembayarklaim
 * @property PenerimaanumumT $penerimaanumum
 * @property ReturbayarpelayananT $returbayarpelayanan
 * @property ReturpenerimaanumumT $returpenerimaanumum
 * @property RuanganM $ruangan
 * @property ShiftM $shift
 */
class TandaBuktiBayar extends \Doco\components\DocoActiveRecord
{
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

            [['bank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bank::className(), 'targetAttribute' => ['bank_id' => 'bank_id']],
            // [['bayaruangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => BayarUangMuka::className(), 'targetAttribute' => ['bayaruangmuka_id' => 'bayaruangmuka_id']],
            // [['closingkasir_id'], 'exist', 'skipOnError' => true, 'targetClass' => Closingkasir::className(), 'targetAttribute' => ['closingkasir_id' => 'closingkasir_id']],
            [['pegawai1_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai1_id' => 'pegawai_id']],
            // [['pegawai2_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai2_id' => 'pegawai_id']],
            // [['pegawai3_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai3_id' => 'pegawai_id']],
            // [['pegawai4_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawai4_id' => 'pegawai_id']],
            // [['pembatalanuangmuka_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembatalanuangmukaT::className(), 'targetAttribute' => ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']],
            // [['pembayarankapitasidetail_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayarankapitasiT::className(), 'targetAttribute' => ['pembayarankapitasidetail_id' => 'pembayarankapitasi_id']],
            [['pembayaranpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayaranPelayanan::className(), 'targetAttribute' => ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']],
            // [['pembayarklaim_id'], 'exist', 'skipOnError' => true, 'targetClass' => PembayarklaimT::className(), 'targetAttribute' => ['pembayarklaim_id' => 'pembayarklaim_id']],
            // [['penerimaanumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => PenerimaanumumT::className(), 'targetAttribute' => ['penerimaanumum_id' => 'penerimaanumum_id']],
            // [['returbayarpelayanan_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturbayarpelayananT::className(), 'targetAttribute' => ['returbayarpelayanan_id' => 'returbayarpelayanan_id']],
            // [['returpenerimaanumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturpenerimaanumumT::className(), 'targetAttribute' => ['returpenerimaanumum_id' => 'returpenerimaanumum_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            [['shift_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shift::className(), 'targetAttribute' => ['shift_id' => 'shift_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tandabuktibayar_id' => Yii::t('app', 'Tanda bukti bayar'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'pembatalanuangmuka_id' => Yii::t('app', 'Pembatalan uang muka'),
            'bayaruangmuka_id' => Yii::t('app', 'Bayar uang muka'),
            'closingkasir_id' => Yii::t('app', 'Closing kasir'),
            'returpenerimaanumum_id' => Yii::t('app', 'Retur penerimaan umum'),
            'pembayaranpelayanan_id' => Yii::t('app', 'Pembayaran pelayanan'),
            'returbayarpelayanan_id' => Yii::t('app', 'Retur bayar pelayanan'),
            'bank_id' => Yii::t('app', 'Bank'),
            'shift_id' => Yii::t('app', 'Shift'),
            'pembayarankapitasidetail_id' => Yii::t('app', 'Pembayaran kapitasi detail'),
            'pembayarklaim_id' => Yii::t('app', 'Pembayar klaim'),
            'penerimaanumum_id' => Yii::t('app', 'Penerimaan umum'),
            'nourutkasir' => Yii::t('app', 'No urut kasir'),
            'nobuktibayar' => Yii::t('app', 'No bukti bayar'),
            'tglbuktibayar' => Yii::t('app', 'Tanggal bukti bayar'),
            'carapembayaran' => Yii::t('app', 'Cara pembayaran'),
            'dengankartu' => Yii::t('app', 'Dengan kartu'),
            'bankkartu' => Yii::t('app', 'Bank kartu'),
            'nokartu' => Yii::t('app', 'No kartu'),
            'nostrukkartu' => Yii::t('app', 'No struk kartu'),
            'darinama_bkm' => Yii::t('app', 'Dari nama Bkm'),
            'alamat_bkm' => Yii::t('app', 'Alamat Bkm'),
            'sebagaipembayaran_bkm' => Yii::t('app', 'Sebagai pembayaran Bkm'),
            'jmlpembulatan' => Yii::t('app', 'Jumlah pembulatan'),
            'jmlpembayaran' => Yii::t('app', 'Jumlah pembayaran'),
            'biayaadministrasi' => Yii::t('app', 'Biaya administrasi'),
            'biayamaterai' => Yii::t('app', 'Biaya materai'),
            'uangditerima' => Yii::t('app', 'Uang diterima'),
            'uangkembalian' => Yii::t('app', 'Uang kembalian'),
            'keterangan_pembayaran' => Yii::t('app', 'Keterangan pembayaran'),
            'is_print' => Yii::t('app', 'Is print'),
            'pegawai1_id' => Yii::t('app', 'Pegawai 1'),
            'pegawai2_id' => Yii::t('app', 'Pegawai 2'),
            'pegawai3_id' => Yii::t('app', 'Pegawai 3'),
            'pegawai4_id' => Yii::t('app', 'Pegawai 4'),
            'pjs_tax' => Yii::t('app', 'Pjs Tax'),
            'pjs_controller' => Yii::t('app', 'Pjs Controller'),
            'namapenerima' => Yii::t('app', 'Nama penerima'),
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
    
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }
    
    public function getShift()
    {
        return $this->hasOne(Shift::className(), ['shift_id' => 'shift_id']);
    }
    
    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawai1_id']);
    }
    
    public function getPembayaranPelayanan()
    {
        return $this->hasOne(PembayaranPelayanan::className(), ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']);
    }
    
    public function getBank()
    {
        return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    }
    
    public function extraFields()
    {
        return [
            'ruangan_m' => function($item){
                return $item->ruangan;
            },
            'shift_m' => function($item){
                return $item->shift;
            },
            'pegawai_m' => function($item){
                return $item->pegawai;
            },
            'pembayaranpelayanan_t' => function($item){
                return $item->pembayaranPelayanan;
            },
            'pasien_m' => function($item){
                return @$item->pembayaranPelayanan->pasien;
            },
            'pendaftaran_t' => function($item){
                return @$item->pembayaranPelayanan->pendaftaran;
            },
            'bank_m' => function($item){
                return $item->bank;
            }
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getBayarangsuranpelayananTs()
    // {
        // return $this->hasMany(BayarangsuranpelayananT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getBayaruangmukaTs()
    // {
        // return $this->hasMany(BayaruangmukaT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getInvoicekeluarTs()
    // {
        // return $this->hasMany(InvoicekeluarT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembatalanuangmukaTs()
    // {
        // return $this->hasMany(PembatalanuangmukaT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembayaranpelayananTs()
    // {
        // return $this->hasMany(PembayaranPelayanan::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembklaimdetailTs()
    // {
        // return $this->hasMany(PembklaimdetailT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPenerimaanumumTs()
    // {
        // return $this->hasMany(PenerimaanumumT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getRealisasianggpenerimaanTs()
    // {
        // return $this->hasMany(RealisasianggpenerimaanT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getReturbayarpelayananTs()
    // {
        // return $this->hasMany(ReturbayarpelayananT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getReturpenerimaanumumTs()
    // {
        // return $this->hasMany(ReturpenerimaanumumT::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getBank()
    // {
        // return $this->hasOne(Bank::className(), ['bank_id' => 'bank_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getBayarUangMuka()
    // {
        // return $this->hasOne(BayarUangMuka::className(), ['bayaruangmuka_id' => 'bayaruangmuka_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getClosingkasir()
    // {
        // return $this->hasOne(ClosingKasir::className(), ['closingkasir_id' => 'closingkasir_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPegawai1()
    // {
        // return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai1_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPegawai2()
    // {
        // return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai2_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPegawai3()
    // {
        // return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai3_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPegawai4()
    // {
        // return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawai4_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembatalanuangmuka()
    // {
        // return $this->hasOne(PembatalanuangmukaT::className(), ['pembatalanuangmuka_id' => 'pembatalanuangmuka_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembayarankapitasidetail()
    // {
        // return $this->hasOne(PembayarankapitasiT::className(), ['pembayarankapitasi_id' => 'pembayarankapitasidetail_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembayaranpelayanan()
    // {
        // return $this->hasOne(PembayaranPelayanan::className(), ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPembayarklaim()
    // {
        // return $this->hasOne(PembayarklaimT::className(), ['pembayarklaim_id' => 'pembayarklaim_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getPenerimaanumum()
    // {
        // return $this->hasOne(PenerimaanumumT::className(), ['penerimaanumum_id' => 'penerimaanumum_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getReturbayarpelayanan()
    // {
        // return $this->hasOne(ReturbayarpelayananT::className(), ['returbayarpelayanan_id' => 'returbayarpelayanan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getReturpenerimaanumum()
    // {
        // return $this->hasOne(ReturpenerimaanumumT::className(), ['returpenerimaanumum_id' => 'returpenerimaanumum_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getRuangan()
    // {
        // return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruangan_id']);
    // }

    /**
     * @return \yii\db\ActiveQuery
     */
    // public function getShift()
    // {
        // return $this->hasOne(ShiftM::className(), ['shift_id' => 'shift_id']);
    // }
}

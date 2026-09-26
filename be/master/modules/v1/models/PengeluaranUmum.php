<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pengeluaranumum_t".
 *
 * @property int $pengeluaranumum_id
 * @property int $jenispengeluaran_id
 * @property int $batalkeluarumum_id
 * @property int $tandabuktikeluar_id
 * @property int $closingkasir_id
 * @property int $invoicetagihan_id
 * @property int $invoicemasuk_id
 * @property int $pegawai_id
 * @property int $profilrs_id
 * @property int $supplier_id
 * @property string $kelompok_transaksi
 * @property string $no_pengeluaran
 * @property string $tgl_pengeluaran
 * @property double $volume
 * @property string $satuan_volume
 * @property double $harga_satuan
 * @property double $total_harga
 * @property double $biaya_administrasi
 * @property string $keterangan_keluar
 * @property bool $is_urainkeluarumum
 * @property string $nocheque
 * @property string $nobilyet
 * @property string $transaksi_bankke
 * @property string $norekening_bankke
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
class PengeluaranUmum extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pengeluaranumum_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenispengeluaran_id', 'kelompok_transaksi', 'no_pengeluaran', 'tgl_pengeluaran', 'volume', 'satuan_volume'], 'required'],
            [['jenispengeluaran_id', 'batalkeluarumum_id', 'tandabuktikeluar_id', 'closingkasir_id', 'invoicetagihan_id', 'invoicemasuk_id', 'pegawai_id', 'profilrs_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['jenispengeluaran_id', 'batalkeluarumum_id', 'tandabuktikeluar_id', 'closingkasir_id', 'invoicetagihan_id', 'invoicemasuk_id', 'pegawai_id', 'profilrs_id', 'supplier_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pengeluaran', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['volume', 'harga_satuan', 'total_harga', 'biaya_administrasi'], 'number'],
            [['keterangan_keluar', 'additional_data'], 'string'],
            [['is_urainkeluarumum', 'is_deleted', 'is_active'], 'boolean'],
            [['kelompok_transaksi', 'no_pengeluaran', 'satuan_volume'], 'string', 'max' => 50],
            [['nocheque', 'nobilyet', 'transaksi_bankke', 'norekening_bankke'], 'string', 'max' => 100],
            // [['batalkeluarumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => BatalkeluarumumT::className(), 'targetAttribute' => ['batalkeluarumum_id' => 'batalkeluarumum_id']],
            [['closingkasir_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClosingKasir::className(), 'targetAttribute' => ['closingkasir_id' => 'closingkasir_id']],
            // [['invoicemasuk_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoicemasukT::className(), 'targetAttribute' => ['invoicemasuk_id' => 'invoicemasuk_id']],
            // [['invoicetagihan_id'], 'exist', 'skipOnError' => true, 'targetClass' => InvoicetagihanT::className(), 'targetAttribute' => ['invoicetagihan_id' => 'invoicetagihan_id']],
            // [['jenispengeluaran_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenispengeluaranM::className(), 'targetAttribute' => ['jenispengeluaran_id' => 'jenispengeluaran_id']],
            [['pegawai_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawai_id' => 'pegawai_id']],
            [['profilrs_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfilRumahSakit::className(), 'targetAttribute' => ['profilrs_id' => 'profilrs_id']],
            // [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => SupplierM::className(), 'targetAttribute' => ['supplier_id' => 'supplier_id']],
            [['tandabuktikeluar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiKeluar::className(), 'targetAttribute' => ['tandabuktikeluar_id' => 'tandabuktikeluar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pengeluaranumum_id' => Yii::t('app', 'Pengeluaranumum ID'),
            'jenispengeluaran_id' => Yii::t('app', 'Jenispengeluaran ID'),
            'batalkeluarumum_id' => Yii::t('app', 'Batalkeluarumum ID'),
            'tandabuktikeluar_id' => Yii::t('app', 'Tandabuktikeluar ID'),
            'closingkasir_id' => Yii::t('app', 'Closingkasir ID'),
            'invoicetagihan_id' => Yii::t('app', 'Invoicetagihan ID'),
            'invoicemasuk_id' => Yii::t('app', 'Invoicemasuk ID'),
            'pegawai_id' => Yii::t('app', 'Pegawai ID'),
            'profilrs_id' => Yii::t('app', 'Profilrs ID'),
            'supplier_id' => Yii::t('app', 'Supplier ID'),
            'kelompok_transaksi' => Yii::t('app', 'Kelompok Transaksi'),
            'no_pengeluaran' => Yii::t('app', 'No Pengeluaran'),
            'tgl_pengeluaran' => Yii::t('app', 'Tgl Pengeluaran'),
            'volume' => Yii::t('app', 'Volume'),
            'satuan_volume' => Yii::t('app', 'Satuan Volume'),
            'harga_satuan' => Yii::t('app', 'Harga Satuan'),
            'total_harga' => Yii::t('app', 'Total Harga'),
            'biaya_administrasi' => Yii::t('app', 'Biaya Administrasi'),
            'keterangan_keluar' => Yii::t('app', 'Keterangan Keluar'),
            'is_urainkeluarumum' => Yii::t('app', 'Is Urainkeluarumum'),
            'nocheque' => Yii::t('app', 'Nocheque'),
            'nobilyet' => Yii::t('app', 'Nobilyet'),
            'transaksi_bankke' => Yii::t('app', 'Transaksi Bankke'),
            'norekening_bankke' => Yii::t('app', 'Norekening Bankke'),
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

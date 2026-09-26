<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "penerimaanumum_t".
 *
 * @property int $penerimaanumum_id
 * @property int $tandabuktibayar_id
 * @property int $jenispenerimaan_id
 * @property int $closingkasir_id
 * @property int $returpenerimaanumum_id
 * @property int $ruangan_id
 * @property int $penjamin_id
 * @property string $tgl_penerimaan
 * @property string $no_penerimaan
 * @property string $kelompok_transaksi
 * @property double $volume
 * @property string $satuan_volume
 * @property double $harga_satuan
 * @property double $total_harga
 * @property string $keterangan_penerimaan
 * @property string $nama_penandatangan
 * @property string $nip_penandatangan
 * @property string $jabatan_penandatangan
 * @property string $is_uraintransaksi
 * @property string $no_cheque
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
class PenerimaanUmum extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'penerimaanumum_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tandabuktibayar_id', 'jenispenerimaan_id', 'closingkasir_id', 'returpenerimaanumum_id', 'ruangan_id', 'penjamin_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['tandabuktibayar_id', 'jenispenerimaan_id', 'closingkasir_id', 'returpenerimaanumum_id', 'ruangan_id', 'penjamin_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jenispenerimaan_id', 'ruangan_id', 'penjamin_id', 'tgl_penerimaan', 'no_penerimaan', 'kelompok_transaksi', 'volume'], 'required'],
            [['tgl_penerimaan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['volume', 'harga_satuan', 'total_harga'], 'number'],
            [['keterangan_penerimaan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_penerimaan'], 'string', 'max' => 20],
            [['kelompok_transaksi', 'satuan_volume'], 'string', 'max' => 50],
            [['nama_penandatangan', 'nip_penandatangan', 'jabatan_penandatangan', 'no_cheque'], 'string', 'max' => 100],
            [['is_uraintransaksi'], 'string', 'max' => 10],
            [['closingkasir_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClosingKasir::className(), 'targetAttribute' => ['closingkasir_id' => 'closingkasir_id']],
            // [['jenispenerimaan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenispenerimaanM::className(), 'targetAttribute' => ['jenispenerimaan_id' => 'jenispenerimaan_id']],
            [['penjamin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Penjamin::className(), 'targetAttribute' => ['penjamin_id' => 'penjamin_id']],
            // [['returpenerimaanumum_id'], 'exist', 'skipOnError' => true, 'targetClass' => ReturpenerimaanumumT::className(), 'targetAttribute' => ['returpenerimaanumum_id' => 'returpenerimaanumum_id']],
            [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
            [['tandabuktibayar_id'], 'exist', 'skipOnError' => true, 'targetClass' => TandaBuktiBayar::className(), 'targetAttribute' => ['tandabuktibayar_id' => 'tandabuktibayar_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penerimaanumum_id' => Yii::t('app', 'Penerimaanumum ID'),
            'tandabuktibayar_id' => Yii::t('app', 'Tandabuktibayar ID'),
            'jenispenerimaan_id' => Yii::t('app', 'Jenispenerimaan ID'),
            'closingkasir_id' => Yii::t('app', 'Closingkasir ID'),
            'returpenerimaanumum_id' => Yii::t('app', 'Returpenerimaanumum ID'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'penjamin_id' => Yii::t('app', 'Penjamin ID'),
            'tgl_penerimaan' => Yii::t('app', 'Tgl Penerimaan'),
            'no_penerimaan' => Yii::t('app', 'No Penerimaan'),
            'kelompok_transaksi' => Yii::t('app', 'Kelompok Transaksi'),
            'volume' => Yii::t('app', 'Volume'),
            'satuan_volume' => Yii::t('app', 'Satuan Volume'),
            'harga_satuan' => Yii::t('app', 'Harga Satuan'),
            'total_harga' => Yii::t('app', 'Total Harga'),
            'keterangan_penerimaan' => Yii::t('app', 'Keterangan Penerimaan'),
            'nama_penandatangan' => Yii::t('app', 'Nama Penandatangan'),
            'nip_penandatangan' => Yii::t('app', 'Nip Penandatangan'),
            'jabatan_penandatangan' => Yii::t('app', 'Jabatan Penandatangan'),
            'is_uraintransaksi' => Yii::t('app', 'Is Uraintransaksi'),
            'no_cheque' => Yii::t('app', 'No Cheque'),
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

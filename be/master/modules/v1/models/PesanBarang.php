<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pesanbarang_t".
 *
 * @property int $pesanbarang_id
 * @property int $mutasibrg_id
 * @property int $pegawaipemesan_id
 * @property int $pegawaimengetahui_id
 * @property int $ruanganpemesan_id
 * @property string $no_pemesanan
 * @property string $tgl_pesanbarang
 * @property string $tgl_mintadikirim
 * @property string $keterangan_pesan
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
 * @property int $ruangantujuan_id
 * @property string $statuspesan
 */
class PesanBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanbarang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mutasibrg_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangantujuan_id'], 'default', 'value' => null],
            [['mutasibrg_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangantujuan_id'], 'integer'],
            [['pegawaipemesan_id', 'ruanganpemesan_id', 'no_pemesanan', 'tgl_pesanbarang'], 'required'],
            [['tgl_pesanbarang', 'tgl_mintadikirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_pesan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pemesanan'], 'string', 'max' => 50],
            [['statuspesan'], 'string', 'max' => 100],
            // [['mutasibrg_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasibarangT::className(), 'targetAttribute' => ['mutasibrg_id' => 'mutasibarang_id']],
            // [['pegawaipemesan_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawaipemesan_id' => 'pegawai_id']],
            // [['pegawaimengetahui_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawaimengetahui_id' => 'pegawai_id']],
            // [['ruanganpemesan_id'], 'exist', 'skipOnError' => true, 'targetClass' => RuanganM::className(), 'targetAttribute' => ['ruanganpemesan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarang_id' => Yii::t('app', 'Pesan barang'),
            'mutasibrg_id' => Yii::t('app', 'Mutasi barang'),
            'pegawaipemesan_id' => Yii::t('app', 'Pegawai pemesan'),
            'pegawaimengetahui_id' => Yii::t('app', 'Pegawai mengetahui'),
            'ruanganpemesan_id' => Yii::t('app', 'Ruangan pemesan'),
            'no_pemesanan' => Yii::t('app', 'No pemesanan'),
            'tgl_pesanbarang' => Yii::t('app', 'Tanggal pesan barang'),
            'tgl_mintadikirim' => Yii::t('app', 'Tanggal minta dikirim'),
            'keterangan_pesan' => Yii::t('app', 'Keterangan pesan'),
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
            'ruangantujuan_id' => Yii::t('app', 'Ruangantujuan ID'),
            'statuspesan' => Yii::t('app', 'Statuspesan'),
        ];
    }
}

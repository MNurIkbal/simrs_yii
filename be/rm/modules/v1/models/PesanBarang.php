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
 *
 * @property MutasibarangT[] $mutasibarangTs
 * @property MutasibarangT $mutasibrg
 * @property PegawaiM $pegawaipemesan
 * @property PegawaiM $pegawaimengetahui
 * @property RuanganM $ruanganpemesan
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
            [['mutasibrg_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['mutasibrg_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pegawaipemesan_id', 'ruanganpemesan_id', 'no_pemesanan', 'tgl_pesanbarang'], 'required'],
            [['tgl_pesanbarang', 'tgl_mintadikirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_pesan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pemesanan'], 'string', 'max' => 50],
            // [['mutasibrg_id'], 'exist', 'skipOnError' => true, 'targetClass' => MutasibarangT::className(), 'targetAttribute' => ['mutasibrg_id' => 'mutasibarang_id']],
            [['pegawaipemesan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawaipemesan_id' => 'pegawai_id']],
            [['pegawaimengetahui_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pegawai::className(), 'targetAttribute' => ['pegawaimengetahui_id' => 'pegawai_id']],
            [['ruanganpemesan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruanganpemesan_id' => 'ruangan_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarang_id' => 'Pesanbarang ID',
            'mutasibrg_id' => 'Mutasibrg ID',
            'pegawaipemesan_id' => 'Pegawaipemesan ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'no_pemesanan' => 'No Pemesanan',
            'tgl_pesanbarang' => 'Tgl Pesanbarang',
            'tgl_mintadikirim' => 'Tgl Mintadikirim',
            'keterangan_pesan' => 'Keterangan Pesan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibarangTs()
    {
        return $this->hasMany(MutasibarangT::className(), ['pesanbarang_id' => 'pesanbarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibrg()
    {
        return $this->hasOne(MutasibarangT::className(), ['mutasibarang_id' => 'mutasibrg_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaipemesan()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawaipemesan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaimengetahui()
    {
        return $this->hasOne(PegawaiM::className(), ['pegawai_id' => 'pegawaimengetahui_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuanganpemesan()
    {
        return $this->hasOne(RuanganM::className(), ['ruangan_id' => 'ruanganpemesan_id']);
    }

    public function getPesanbarangdetail()
    {
        return $this->hasOne(PesanBarangDetail::className(), ['pesanbarang_id' => 'pesanbarang_id']);
    }

    public function getBarang()
    {
        return $this->hasOne(Barang::className(), ['barang_id' => 'barang_id'])
            ->viaTable('pesanbarangdetail_t as pb', ['pesanbarang_id' => 'pesanbarang_id']);
    }

    public function extraFields()
    {
        return [
            'pesanbarangdetail_t' => function($item){
                return $item->pesanbarangdetail;
            },
            'barang_m' => function($item){
                return $item->barang;
            }
        ];
    }
}

<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mutasibarang_t".
 *
 * @property int $mutasibarang_id
 * @property int $pesanbarang_id
 * @property int $pegawaipengirim_id
 * @property int $pegawaimengetahui_id
 * @property int $ruangantujuan_id
 * @property string $tgl_mutasibarang
 * @property string $nomutasi_barang
 * @property string $keterangan_mutasi
 * @property double $totalhargamutasi
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
 * @property int $ruanganasal_id
 * @property int $status_mutasi
 *
 * @property BatalmutasibarangT[] $batalmutasibarangTs
 * @property PegawaiM $pegawaipengirim
 * @property PegawaiM $pegawaimengetahui
 * @property PesanbarangT $pesanbarang
 * @property MutasibarangdetailT[] $mutasibarangdetailTs
 * @property PesanbarangT[] $pesanbarangTs
 * @property TerimamutasibarangT[] $terimamutasibarangTs
 */
class MutasiBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mutasibarang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pesanbarang_id', 'pegawaipengirim_id', 'pegawaimengetahui_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruanganasal_id', 'status_mutasi'], 'default', 'value' => null],
            [['pesanbarang_id', 'pegawaipengirim_id', 'pegawaimengetahui_id', 'ruangantujuan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruanganasal_id', 'status_mutasi'], 'integer'],
            [['pegawaipengirim_id', 'ruangantujuan_id', 'tgl_mutasibarang'], 'required'],
            [['tgl_mutasibarang', 'created_date', 'last_modified_date', 'deleted_date', 'nomutasi_barang'], 'safe'],
            [['keterangan_mutasi', 'additional_data'], 'string'],
            [['totalhargamutasi'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nomutasi_barang'], 'string', 'max' => 50],
            // [['pegawaipengirim_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawaipengirim_id' => 'pegawai_id']],
            // [['pegawaimengetahui_id'], 'exist', 'skipOnError' => true, 'targetClass' => PegawaiM::className(), 'targetAttribute' => ['pegawaimengetahui_id' => 'pegawai_id']],
            // [['pesanbarang_id'], 'exist', 'skipOnError' => true, 'targetClass' => PesanbarangT::className(), 'targetAttribute' => ['pesanbarang_id' => 'pesanbarang_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mutasibarang_id' => 'Mutasibarang ID',
            'pesanbarang_id' => 'Pesanbarang ID',
            'pegawaipengirim_id' => 'Pegawaipengirim ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'ruangantujuan_id' => 'Ruangantujuan ID',
            'tgl_mutasibarang' => 'Tgl Mutasibarang',
            'nomutasi_barang' => 'Nomutasi Barang',
            'keterangan_mutasi' => 'Keterangan Mutasi',
            'totalhargamutasi' => 'Totalhargamutasi',
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
            'ruanganasal_id' => 'Ruanganasal ID',
            'status_mutasi' => 'Status Mutasi',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBatalmutasibarangTs()
    {
        return $this->hasMany(BatalMutasiBarang::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaipengirim()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawaipengirim_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPegawaimengetahui()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'pegawaimengetahui_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanbarang()
    {
        return $this->hasOne(PesanBarang::className(), ['pesanbarang_id' => 'pesanbarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMutasibarangdetailTs()
    {
        return $this->hasMany(MutasiBarangDetail::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPesanbarangTs()
    {
        return $this->hasMany(PesanBarang::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerimamutasibarangTs()
    {
        return $this->hasMany(TerimaMutasiBarang::className(), ['mutasibarang_id' => 'mutasibarang_id']);
    }
}

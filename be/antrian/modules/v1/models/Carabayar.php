<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "carabayar_m".
 *
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property string $carabayar_namalainnya
 * @property string $metode_pembayaran
 * @property string $carabayar_loket
 * @property string $carabayar_singkatan
 * @property int $carabayar_urutan
 * @property bool $is_subsidiasuransi
 * @property bool $is_subsidipemerintah
 * @property bool $is_subsidirs
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
 * @property AntrianT[] $antrianTs
 * @property AsuransipasienM[] $asuransipasienMs
 * @property BesaranjasadetailT[] $besaranjasadetailTs
 * @property InvoicemasukdetailT[] $invoicemasukdetailTs
 * @property KomponenjasaM[] $komponenjasaMs
 * @property LoketM[] $loketMs
 * @property MasukkamarT[] $masukkamarTs
 * @property PasienadmisiT[] $pasienadmisiTs
 * @property PembayaranpelayananT[] $pembayaranpelayananTs
 * @property PembayarklaimT[] $pembayarklaimTs
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjaminM[] $penjaminMs
 * @property PenjualanresepT[] $penjualanresepTs
 * @property PindahkamarT[] $pindahkamarTs
 * @property TanggunganpenjaminM[] $tanggunganpenjaminMs
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property TipepaketM[] $tipepaketMs
 */
class Carabayar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'carabayar_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_nama'], 'required'],
            [['carabayar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carabayar_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_subsidiasuransi', 'is_subsidipemerintah', 'is_subsidirs', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['carabayar_nama', 'carabayar_namalainnya', 'metode_pembayaran', 'carabayar_loket'], 'string', 'max' => 50],
            [['carabayar_singkatan'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'carabayar_namalainnya' => 'Carabayar Namalainnya',
            'metode_pembayaran' => 'Metode Pembayaran',
            'carabayar_loket' => 'Carabayar Loket',
            'carabayar_singkatan' => 'Carabayar Singkatan',
            'carabayar_urutan' => 'Carabayar Urutan',
            'is_subsidiasuransi' => 'Is Subsidiasuransi',
            'is_subsidipemerintah' => 'Is Subsidipemerintah',
            'is_subsidirs' => 'Is Subsidirs',
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
    public function getAntrianTs()
    {
        return $this->hasMany(AntrianT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsuransipasienMs()
    {
        return $this->hasMany(AsuransipasienM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBesaranjasadetailTs()
    {
        return $this->hasMany(BesaranjasadetailT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicemasukdetailTs()
    {
        return $this->hasMany(InvoicemasukdetailT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKomponenjasaMs()
    {
        return $this->hasMany(KomponenjasaM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoketMs()
    {
        return $this->hasMany(LoketM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMasukkamarTs()
    {
        return $this->hasMany(MasukkamarT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienadmisiTs()
    {
        return $this->hasMany(PasienadmisiT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayaranpelayananTs()
    {
        return $this->hasMany(PembayaranpelayananT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPembayarklaimTs()
    {
        return $this->hasMany(PembayarklaimT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPendaftaranTs()
    {
        return $this->hasMany(PendaftaranT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjaminMs()
    {
        return $this->hasMany(PenjaminM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPenjualanresepTs()
    {
        return $this->hasMany(PenjualanresepT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPindahkamarTs()
    {
        return $this->hasMany(PindahkamarT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTanggunganpenjaminMs()
    {
        return $this->hasMany(TanggunganpenjaminM::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTindakanpelayananTs()
    {
        return $this->hasMany(TindakanpelayananT::className(), ['carabayar_id' => 'carabayar_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipepaketMs()
    {
        return $this->hasMany(TipepaketM::className(), ['carabayar_id' => 'carabayar_id']);
    }
}

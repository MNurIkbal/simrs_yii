<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dokrekammedis_m".
 *
 * @property int $dokrekammedis_id
 * @property int $warnadokrm_id
 * @property int $subrak_id
 * @property int $lokasirak_id
 * @property int $pasien_id
 * @property string $nodokumenrm
 * @property string $tglrekammedis
 * @property string $tglmasukrak
 * @property string $statusrekammedis
 * @property string $tglkeluarakhir
 * @property string $tglmasukakhir
 * @property string $nomortertier
 * @property string $nomorsekunder
 * @property string $nomorprimer
 * @property string $warnanorm_i
 * @property string $warnanorm_ii
 * @property string $tgl_in_aktif
 * @property string $tglpemusnahan
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
 * @property LokasirakM $lokasirak
 * @property PasienM $pasien
 * @property SubrakM $subrak
 * @property WarnadokrekammedikM $warnadokrm
 * @property PasienM[] $pasienMs
 * @property PengirimanrmT[] $pengirimanrmTs
 */
class PencatatanDokRekamMedik extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dokrekammedis_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['warnadokrm_id', 'tglrekammedis', 'tglmasukrak', 'statusrekammedis'], 'required'],
            [['warnadokrm_id', 'subrak_id', 'lokasirak_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['warnadokrm_id', 'subrak_id', 'lokasirak_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglrekammedis', 'tglmasukrak', 'tglkeluarakhir', 'tglmasukakhir', 'tgl_in_aktif', 'tglpemusnahan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nodokumenrm'], 'string', 'max' => 20],
            [['statusrekammedis'], 'string', 'max' => 10],
            [['nomortertier', 'nomorsekunder', 'nomorprimer'], 'string', 'max' => 2],
            [['warnanorm_i', 'warnanorm_ii'], 'string', 'max' => 50],
            [['lokasirak_id'], 'exist', 'skipOnError' => true, 'targetClass' => LokasiRakRekamMedik::className(), 'targetAttribute' => ['lokasirak_id' => 'lokasirak_id']],
            [['pasien_id'], 'exist', 'skipOnError' => true, 'targetClass' => Pasien::className(), 'targetAttribute' => ['pasien_id' => 'pasien_id']],
            [['subrak_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubRak::className(), 'targetAttribute' => ['subrak_id' => 'subrak_id']],
            [['warnadokrm_id'], 'exist', 'skipOnError' => true, 'targetClass' => WarnaDokRekamMedik::className(), 'targetAttribute' => ['warnadokrm_id' => 'warnadokrm_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'warnadokrm_id' => 'Warnadokrm ID',
            'subrak_id' => 'Subrak ID',
            'lokasirak_id' => 'Lokasirak ID',
            'pasien_id' => 'Pasien ID',
            'nodokumenrm' => 'Nodokumenrm',
            'tglrekammedis' => 'Tglrekammedis',
            'tglmasukrak' => 'Tglmasukrak',
            'statusrekammedis' => 'Statusrekammedis',
            'tglkeluarakhir' => 'Tglkeluarakhir',
            'tglmasukakhir' => 'Tglmasukakhir',
            'nomortertier' => 'Nomortertier',
            'nomorsekunder' => 'Nomorsekunder',
            'nomorprimer' => 'Nomorprimer',
            'warnanorm_i' => 'Warnanorm I',
            'warnanorm_ii' => 'Warnanorm Ii',
            'tgl_in_aktif' => 'Tgl In Aktif',
            'tglpemusnahan' => 'Tglpemusnahan',
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
    public function getLokasirak()
    {
        return $this->hasOne(LokasiRakRekamMedik::className(), ['lokasirak_id' => 'lokasirak_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasien()
    {
        return $this->hasOne(Pasien::className(), ['pasien_id' => 'pasien_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubrak()
    {
        return $this->hasOne(Subrak::className(), ['subrak_id' => 'subrak_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarnadokrm()
    {
        return $this->hasOne(Warnadokrekammedik::className(), ['warnadokrm_id' => 'warnadokrm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPasienMs()
    {
        return $this->hasMany(Pasien::className(), ['dokrekammedis_id' => 'dokrekammedis_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(PengirimanrmT::className(), ['dokrekammedis_id' => 'dokrekammedis_id']);
    }

    public function extraFields()
    {
        return [
            'lokasirak_m' => function($item){
                return $item->lokasirak;
            },
            'subrak_m' => function($item){
                return $item->subrak;
            },
            'pasien_m' => function($item){
                return $item->pasien;
            },
            'warnadokrekammedik_m' => function($item){
                return $item->warnadokrm;
            }
        ];
    }
}

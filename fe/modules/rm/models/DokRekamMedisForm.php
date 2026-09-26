<?php

namespace app\modules\rm\models;

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
class DokRekamMedisForm extends \yii\base\Model
{
    public $row_id;
    public $dokrekammedis_id;
    public $warnadokrm_id;
    public $subrak_id;
    public $lokasirak_id;
    public $pasien_id;
    public $nodokumenrm;
    public $tglrekammedis;
    public $tglmasukrak;
    public $statusrekammedis;
    public $tglkeluarakhir;
    public $tglmasukakhir;
    public $nomortertier;
    public $nomorsekunder;
    public $nomorprimer;
    public $warnanorm_i;
    public $warnanorm_ii;
    public $tgl_in_aktif;
    public $tglpemusnahan;
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
    public $lokasirak_nama;
    public $lokasirak_m;
    public $subrak_m;
    public $pasien_m;
    public $warnadokrekammedik_m;
    public $nama_pasien;
    public $no_rekam_medis;
    
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
            // [['warnadokrm_id', 'tglrekammedis', 'tglmasukrak', 'statusrekammedis'], 'required'],
            [['warnadokrm_id', 'subrak_id', 'lokasirak_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            // ['pasien_id', 'unique', 'targetAttribute' => ['pasien_id'], 'message' => 'Username must be unique.'],
            [['warnadokrm_id', 'subrak_id', 'lokasirak_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglrekammedis', 'tglmasukrak', 'tglkeluarakhir', 'tglmasukakhir', 'tgl_in_aktif', 'tglpemusnahan', 'created_date', 'last_modified_date', 'deleted_date','lokasirak_m','subrak_m','pasien_m','warnadokrekammedik_m','row_id'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nodokumenrm'], 'string', 'max' => 20],
            [['statusrekammedis'], 'string', 'max' => 10],
            [['nomortertier', 'nomorsekunder', 'nomorprimer'], 'string', 'max' => 2],
            [['warnanorm_i', 'warnanorm_ii'], 'string', 'max' => 50]
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
            'subrak_id' => 'No Sub Rak',
            'lokasirak_id' => 'No Rak',
            'pasien_id' => 'No Rekam Medik',
            'nodokumenrm' => 'Nodokumenrm',
            'tglrekammedis' => 'Tglrekammedis',
            'tglmasukrak' => 'Tgl Awal Masuk Rak',
            'statusrekammedis' => 'Statusrekammedis',
            'tglkeluarakhir' => 'Tglkeluarakhir',
            'tglmasukakhir' => 'Tglmasukakhir',
            'nomortertier' => 'Nomor Tertier',
            'nomorsekunder' => 'Nomor Sekunder',
            'nomorprimer' => 'Nomor Primer',
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
            'warnadokrm_namawarna' => 'Warna Dokumen Rekam Medik',
            'row_id' => 'No Rekam Medis'
        ];
    }
}

<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ruangan_m".
 *
 * @property integer $ruangan_id
 * @property integer $instalasi_id
 * @property string $ruangan_nama
 * @property string $ruangan_namalainnya
 * @property string $ruangan_jenispelayanan
 * @property string $ruangan_singkatan
 * @property string $ruangan_fasilitas
 * @property string $ruangan_lokasi
 * @property string $ruangan_image
 * @property integer $ruangan_urutan
 * @property string $ruangan_filesuara
 * @property integer $estimasipelayanan
 * @property string $image_mobile
 * @property integer $warnadokrm_id
 * @property string $kode_ruanganpoli
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $unitkerja_id
 */
class Ruangan extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\RuanganRepositories';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruangan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'ruangan_urutan', 'estimasipelayanan', 'warnadokrm_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'unitkerja_id'], 'integer'],
            [['ruangan_nama', 'unitkerja_id'], 'required'],
            [['ruangan_fasilitas', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_nama', 'ruangan_namalainnya', 'ruangan_jenispelayanan', 'ruangan_lokasi'], 'string', 'max' => 50],
            [['ruangan_singkatan', 'kode_ruanganpoli'], 'string', 'max' => 3],
            [['ruangan_image'], 'string', 'max' => 100],
            [['ruangan_filesuara', 'image_mobile'], 'string', 'max' => 500],
            [['instalasi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Instalasi::className(), 'targetAttribute' => ['instalasi_id' => 'instalasi_id']],
            [['unitkerja_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnitKerja::className(), 'targetAttribute' => ['unitkerja_id' => 'unitkerja_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'ruangan_namalainnya' => Yii::t('app', 'Nama lainnya'),
            'ruangan_jenispelayanan' => Yii::t('app', 'Jenis pelayanan'),
            'ruangan_singkatan' => Yii::t('app', 'Singkatan'),
            'ruangan_fasilitas' => Yii::t('app', 'Fasilitas'),
            'ruangan_lokasi' => Yii::t('app', 'Lokasi'),
            'ruangan_image' => Yii::t('app', 'Image'),
            'ruangan_urutan' => Yii::t('app', 'Urutan'),
            'ruangan_filesuara' => Yii::t('app', 'File suara'),
            'estimasipelayanan' => Yii::t('app', 'Estimasi pelayanan'),
            'image_mobile' => Yii::t('app', 'Image mobile'),
            'warnadokrm_id' => Yii::t('app', 'Warnadokrm ID'),
            'kode_ruanganpoli' => Yii::t('app', 'Kode ruangan poli'),
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
            'unitkerja_id' => Yii::t('app', 'Unitkerja ID'),
        ];
    }
    
    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }
    
    public function getUnitKerja()
    {
        return $this->hasOne(UnitKerja::className(), ['unitkerja_id' => 'unitkerja_id']);
    }
    
    public function extraFields()
    {
        return [
            'instalasi_m' => function($item){
                return $item->instalasi;
            },
            'unitkerja_m' => function($item){
                return $item->unitKerja;
            },
        ];
    }

    /**
    * @author Rizal
    * @since 2018-01-11 10:11:20 
    * @param 
    * @return array list of ruangan
    * @desc 
    */
    public static function getRuangan() {
        $sql = "
        SELECT 
            ruangan_id, 
            ruangan_nama
        FROM ruangan_m
        WHERE is_deleted = false AND is_active = true
        ORDER BY ruangan_urutan, ruangan_id
        ";
        $list = Yii::$app->db->createCommand($sql)->queryAll();
        return $list;
    }
}

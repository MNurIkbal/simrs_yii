<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "instalasi_m".
 *
 * @property integer $instalasi_id
 * @property integer $riwayatruangan_id
 * @property string $instalasi_nama
 * @property string $instalasi_namalainnya
 * @property string $instalasi_singkatan
 * @property string $instalasi_lokasi
 * @property boolean $instalasirujukaninternal
 * @property boolean $instalasi_adakamar
 * @property string $instalasi_image
 * @property boolean $is_penunjang
 * @property integer $profilers_id
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
 */
class Instalasi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instalasi_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riwayatruangan_id', 'profilers_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['instalasi_nama', 'instalasi_singkatan'], 'required'],
            [['instalasirujukaninternal', 'instalasi_adakamar', 'is_penunjang', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['instalasi_nama', 'instalasi_namalainnya', 'instalasi_lokasi'], 'string', 'max' => 50],
            [['instalasi_singkatan'], 'string', 'max' => 5],
            [['instalasi_image'], 'string', 'max' => 200],
            [['profilers_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfilRumahSakit::className(), 'targetAttribute' => ['profilers_id' => 'profilrs_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => Yii::t('app', 'Instalasi ID'),
            'riwayatruangan_id' => Yii::t('app', 'Riwayatruangan ID'),
            'instalasi_nama' => Yii::t('app', 'Instalasi Nama'),
            'instalasi_namalainnya' => Yii::t('app', 'Instalasi Namalainnya'),
            'instalasi_singkatan' => Yii::t('app', 'Instalasi Singkatan'),
            'instalasi_lokasi' => Yii::t('app', 'Instalasi Lokasi'),
            'instalasirujukaninternal' => Yii::t('app', 'Instalasirujukaninternal'),
            'instalasi_adakamar' => Yii::t('app', 'Instalasi Adakamar'),
            'instalasi_image' => Yii::t('app', 'Instalasi Image'),
            'is_penunjang' => Yii::t('app', 'Is Penunjang'),
            'profilers_id' => Yii::t('app', 'Profilers ID'),
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
    
    public function getProfilRumahSakit()
    {
        return $this->hasOne(ProfilRumahSakit::className(), ['profilrs_id' => 'profilers_id']);
    }
    
    public function getRuangan()
    {
        return $this->hasMany(Ruangan::className(), ['instalasi_id' => 'instalasi_id']);
    }

    public function extraFields()
    {
        return [
            'profilrumahsakit_m' => function($item){
                return $item->profilRumahSakit;
            }
        ];
    }
}

<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "ruangan_v".
 *
 * @property integer $instalasi_id
 * @property integer $ruangan_id
 * @property string $instalasi_nama
 * @property string $ruangan_nama
 * @property string $ruangan_singkatan
 * @property string $ruangan_gambar
 * @property string $ruangan_image_blob
 * @property string $ruangan_filesuara_blob
 */
class RuanganView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ruangan_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['instalasi_id', 'instalasi_nama', 'ruangan_id', 'ruangan_nama', 'ruangan_singkatan'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_id'], 'integer'],
            [['instalasi_nama', 'ruangan_nama', 'ruangan_singkatan'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'ruangan_gambar', 'ruangan_image_blob', 'ruangan_filesuara_blob'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['instalasi_id', 'ruangan_id'], 'string', 'max' => 50],
            [['instalasi_nama', 'ruangan_nama', 'ruangan_singkatan'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'ruangan_id' => Yii::t('app', 'Ruangan ID'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'ruangan_gambar' => Yii::t('app', 'image'),
            'ruangan_image_blob' => Yii::t('app', 'Image'),
            'ruangan_filesuara_blob' => Yii::t('app', 'File Suara'),
            'ruangan_singkatan' => Yii::t('app', 'Nama rRuangan singkatan'),
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

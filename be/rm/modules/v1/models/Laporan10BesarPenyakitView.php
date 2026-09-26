<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporan10besarpenyakit_v".
 *
 * @property int $diagnosa_id
 * @property string $diagnosa_kode
 * @property string $diagnosa_nama
 * @property string $tglmorbiditas
 * @property int $pasienmorbiditas_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $instalasi_id
 * @property string $instalasi_nama
 * @property bool $is_deleted
 * @property int $klasifikasidiagnosa_id
 * @property string $klasifikasidiagnosa_nama
 */
class Laporan10BesarPenyakitView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporan10besarpenyakit_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['diagnosa_id', 'pasienmorbiditas_id', 'ruangan_id', 'instalasi_id', 'klasifikasidiagnosa_id'], 'default', 'value' => null],
            [['diagnosa_id', 'pasienmorbiditas_id', 'ruangan_id', 'instalasi_id', 'klasifikasidiagnosa_id'], 'integer'],
            [['tglmorbiditas'], 'safe'],
            [['is_deleted'], 'boolean'],
            [['diagnosa_kode'], 'string', 'max' => 10],
            [['diagnosa_nama'], 'string', 'max' => 200],
            [['ruangan_nama', 'instalasi_nama'], 'string', 'max' => 50],
            [['klasifikasidiagnosa_nama'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'diagnosa_id' => Yii::t('app', 'Diagnosa'),
            'diagnosa_kode' => Yii::t('app', 'Kode diagnosa'),
            'diagnosa_nama' => Yii::t('app', 'Nama siagnosa'),
            'tglmorbiditas' => Yii::t('app', 'Tanggal morbiditas'),
            'pasienmorbiditas_id' => Yii::t('app', 'Pasien morbiditas'),
            'ruangan_id' => Yii::t('app', 'Ruangan'),
            'ruangan_nama' => Yii::t('app', 'Nama ruangan'),
            'instalasi_id' => Yii::t('app', 'Instalasi'),
            'instalasi_nama' => Yii::t('app', 'Nama instalasi'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'klasifikasidiagnosa_id' => Yii::t('app', 'Klasifikasi diagnosa'),
            'klasifikasidiagnosa_nama' => Yii::t('app', 'Nama klasifikasi diagnosa'),
        ];
    }
}

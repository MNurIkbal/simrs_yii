<?php

namespace app\modules\laporan\models;

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
class LaporanSepuluhBesarPenyakitForm extends \yii\base\Model
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
            'diagnosa_id' => Yii::t('fe','Diagnosa ID'),
            'diagnosa_kode' => Yii::t('fe','Diagnosa Kode'),
            'diagnosa_nama' => Yii::t('fe','Diagnosa Nama'),
            'tglmorbiditas' => Yii::t('fe','Tglmorbiditas'),
            'pasienmorbiditas_id' => Yii::t('fe','Pasienmorbiditas ID'),
            'ruangan_id' => Yii::t('fe','Ruangan ID'),
            'ruangan_nama' => Yii::t('fe','Ruangan Nama'),
            'instalasi_id' => Yii::t('fe','Instalasi ID'),
            'instalasi_nama' => Yii::t('fe','Instalasi Nama'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'klasifikasidiagnosa_id' => Yii::t('fe','Klasifikasidiagnosa ID'),
            'klasifikasidiagnosa_nama' => Yii::t('fe','Klasifikasidiagnosa Nama'),
        ];
    }
}

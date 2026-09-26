<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "klasifikasikamar_m".
 *
 * @property integer $klasifikasikamar_id
 * @property string $klasifikasikamar_nama
 * @property integer $sirsonline_id
 * @property integer $eiscovid_id
 * @property integer $applicare_id
 * @property integer $spgdt_id
 * @property string $ruangan_nama
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
 * @property integer $is_modul
 */
class KlasifikasiKamar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'klasifikasikamar_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['klasifikasikamar_nama','sirsonline_id','eiscovid_id','applicare_id','spgdt_id','created_date', 'last_modified_date', 'deleted_date','is_active', 'kodekelas_aplicare', 'namakelas_aplicare'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'klasifikasikamar_id' => Yii::t('app', 'Klasifikasi Kamar'),
            'klasifikasikamar_nama' => Yii::t('app', 'Klasifikasi Kamar Nama')
        ];
    }
}

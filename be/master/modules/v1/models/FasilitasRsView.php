<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-18 11:18:07
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "fasilitasrsdetail_v".
 *
 * @property int $fasilitasrs_id
 * @property int $jenis_fasilitas
 * @property int $nama_jenis
 * @property int $fasilitasrsdetail_id
 * @property int $nama_fasilitas
 * @property int $is_active
 */
class FasilitasRsView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fasilitasrsdetail_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fasilitasrs_id', 'jenis_fasilitas', 'nama_jenis', 'fasilitasrsdetail_id', 'nama_fasilitas', 'is_active'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'fasilitasrs_id' => 'Fasilitas Rs ID',
            'jenis_fasilitas' => 'Jenis Fasilitas',
            'nama_jenis' => 'Nama Jenis Fasilitas',
            'fasilitasrsdetail_id' => 'Fasilitas Rs Detail ID',
            'nama_fasilitas' => 'Nama Fasilitas',
            'is_active' => 'Is Active',
        ];
    }
}

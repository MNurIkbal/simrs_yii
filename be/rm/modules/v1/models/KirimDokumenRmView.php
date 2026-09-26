<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-11 13:56:07
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-12 16:35:54
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kirimdokumenrm_v".
 *
 * @property string $nomor_pengiriman
 * @property string $tgl_pengirimanrm
 * @property int $ruanganpengirim_id
 * @property string $ruangan_asal
 * @property int $instalasi_id
 * @property string $instalasi_asal
 * @property string $no_rekam_medik
 * @property int $pasien_id
 */
class KirimDokumenRmView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kirimdokumenrm_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_pengirimanrm'], 'safe'],
            [['ruanganpengirim_id', 'instalasi_id', 'pasien_id'], 'default', 'value' => null],
            [['ruanganpengirim_id', 'instalasi_id', 'pasien_id'], 'integer'],
            [['nomor_pengiriman'], 'string', 'max' => 5],
            [['ruangan_asal', 'instalasi_asal'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nomor_pengiriman' => 'Nomor Pengiriman',
            'tgl_pengirimanrm' => 'Tgl Pengirimanrm',
            'ruanganpengirim_id' => 'Ruanganpengirim ID',
            'ruangan_asal' => 'Ruangan Asal',
            'instalasi_id' => 'Instalasi ID',
            'instalasi_asal' => 'Instalasi Asal',
            'no_rekam_medik' => 'No Rekam Medik',
            'pasien_id' => 'Pasien ID',
        ];
    }
}

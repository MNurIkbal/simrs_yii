<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_v".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 */
class PasienView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pasien_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pasien_id'], 'default', 'value' => null],
            [['pasien_id'], 'integer'],
            [['tgl_rekam_medik', 'tanggal_lahir'], 'safe'],
            [['no_rekam_medik'], 'string', 'max' => 10],
            [['nama_pasien'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pasien_id' => 'Pasien ID',
            'no_rekam_medik' => 'No Rekam Medik',
            'tgl_rekam_medik' => 'Tgl Rekam Medik',
            'nama_pasien' => 'Nama Pasien',
            'tanggal_lahir' => 'Tanggal Lahir',
        ];
    }
}

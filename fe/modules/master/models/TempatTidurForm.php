<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "tempattidur_v".
 *
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property string $kettempattidur_nama
 * @property bool $status_isi
 */
class TempatTidurForm extends \yii\base\Model
{
    // Public variable
    public $kamarruangan_id;
    public $ruangan_id;
    public $ruangan_nama;
    public $kamarruangan_nokamar;
    public $no_tempattidur;
    public $kettempattidur_nama;
    public $status_isi;
    public $kettempattidur_id;
    public $is_active;
    public $is_rekapkinerjaprofesi;
    public $is_terisi;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id','no_tempattidur','kamarruangan_id'], 'required'],
            [['no_tempattidur'], 'trimWhitespace'],
            [['kamarruangan_id', 'ruangan_id'], 'default', 'value' => null],
            [['kamarruangan_id', 'ruangan_id', 'kettempattidur_id'], 'integer'],
            [['status_isi', 'is_active', 'is_rekapkinerjaprofesi', 'is_terisi'], 'boolean'],
            [['ruangan_nama'], 'string', 'max' => 50],
            [['kamarruangan_nokamar'], 'string', 'max' => 25],
            [['no_tempattidur', 'kettempattidur_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kamarruangan_id' => 'Kamar',
            'ruangan_id' => 'Ruangan',
            'ruangan_nama' => 'Ruangan',
            'kamarruangan_nokamar' => 'Kamar',
            'no_tempattidur' => 'No Tempat Tidur',
            'kettempattidur_nama' => 'Keterangan Tempat Tidur',
            'status_isi' => 'Status Tempat Tidur',
            'kettempattidur_id' => 'Warna Tempat Tidur',
            'is_active' => Yii::t('fe', 'Aktif'),
            'is_rekapkinerjaprofesi' => 'Integrasi Aplikasi'
        ];
    }

    public function trimWhitespace(){
        $no_tempattidur = $this->no_tempattidur;
        if (strpos(substr($no_tempattidur, 0, 1), ' ') !== FALSE) {
            $this->addError('no_tempattidur', 'No Tempat Tidur mengandung spasi di awal kata');
            return false;
        }
        return true;
    }
}

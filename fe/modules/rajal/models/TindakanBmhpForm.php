<?php

namespace app\modules\rajal\models;

use Yii;

/**
 *
 * @property int $soaprj_id
 */
class TindakanBmhpForm extends \yii\base\Model
{
    public $dokterpenanggungjawab_id;
    public $dokterdelegasi_id;
    public $perawat1_id;
    public $perawat2_id;
    public $nama_dokterpj;
    public $depo_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nama_dokterpj', 'depo_id'], 'required'],
            [['dokterpenanggungjawab_id', 'dokterdelegasi_id', 'perawat1_id', 'perawat2_id'], 'default', 'value' => null],
            [['dokterpenanggungjawab_id', 'dokterdelegasi_id', 'perawat1_id', 'perawat2_id'], 'integer'],
            // [['tgl_soaprj', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nama_dokterpj' => 'Dokter Penanggung Jawab',
            'dokterpenanggungjawab_id' => 'Dokter Penanggung Jawab',
            'dokterdelegasi_id' => 'Dokter Delegasi',
            'perawat1_id' => 'Perawat 1',
            'perawat2_id' => 'Perawat 2',
            'depo_id' => 'Depo'
        ];
    }
}

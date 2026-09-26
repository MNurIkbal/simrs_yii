<?php

namespace app\modules\ranap\models;

use Yii;

class PindahKamarForm extends \yii\base\Model
{
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $jeniskasuspenyakit_id;
    public $kelaspelayanan_id;
    public $ruangan_id;
    public $kamarruangan_id;
    public $kamartempattidur_id;
    public $tgl_pindahkamar;
    public $kelaspelayanan_selected;

    public $kamartempattidur_no_tempattidur;

    public $is_pasientitipan;
    public $ruangan_titipan_id;
    public $kelas_ditagihkan_id;
    public $kamar_titipan_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'ruangan_id', 'jeniskasuspenyakit_id', 'kelaspelayanan_id', 
                    'kamartempattidur_no_tempattidur'
                ], 
                'required'
            ],
            [
                'kelas_ditagihkan_id', 'required', 'when' => function($model) {
                    return $model->is_pasientitipan;
                }
            ],
            [
                [
                    'pendaftaran_id',  'pasienadmisi_id', 'kamartempattidur_id', 
                    'kamarruangan_id', 'tgl_pindahkamar', 'kelaspelayanan_selected', 'is_pasientitipan', 'ruangan_titipan_id', 'kelas_ditagihkan_id', 'kamar_titipan_id'
                ], 'safe'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Jenis kasus penyakit'),
            'kelaspelayanan_id' => Yii::t('fe', 'Kelas pelayanan'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'kamartempattidur_no_tempattidur' => Yii::t('fe', 'No tempat tidur'),
        ];
    }
}

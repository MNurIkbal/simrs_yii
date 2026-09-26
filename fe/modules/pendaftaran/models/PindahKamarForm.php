<?php

namespace app\modules\pendaftaran\models;

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
    public $kelas_ditagihkan_id;
    public $kamar_titipan_id;
    public $ruangan_titipan_id;
    public $old_kamarruangan_id;

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
                [
                    'pendaftaran_id', 'pasienadmisi_id', 'kamartempattidur_id', 
                    'kamarruangan_id', 'tgl_pindahkamar', 'kelaspelayanan_selected',
                    'kelas_ditagihkan_id', 'kamar_titipan_id', 'ruangan_titipan_id', 'old_kamarruangan_id'
                ], 'safe'
            ],
            [
                [
                    'is_pasientitipan'
                ], 'boolean'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => Yii::t('fe', 'Jenis Kasus Penyakit'),
            'kelaspelayanan_id' => Yii::t('fe', 'Kelas Pelayanan'),
            'ruangan_id' => Yii::t('fe', 'Ruangan'),
            'kamartempattidur_no_tempattidur' => Yii::t('fe', 'No Tempat Tidur'),
            'is_pasientitipan' => Yii::t('fe', 'Kamar Tagihan')
        ];
    }
}

<?php

/**
 * @Author: Naufal Ziyad L
 * @Date:   2018-01-17 13:22
*/

namespace app\modules\pendaftaran\models;

use Yii;


class RujukanForm extends \yii\base\Model
{
    public $asalrujukan_id;
    public $rujukandari_id;
    public $diagnosa_id;
    public $no_rujukan;
    public $nama_perujuk;
    public $tanggal_rujukan;
    public $kodediagnosa_rujukan;
    public $is_rujukan;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // [
            //     [
            //         'rujukandari_id', 
            //         'asalrujukan_id',
            //         'no_rujukan', 
            //         'nama_perujuk'
            //     ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            // ],
            [['rujukandari_id'], 'default', 'value' => null],
            [['nama_perujuk'], 'trim'],
            [[
                'tanggal_rujukan', 
                'diagnosa_id',
                'asalrujukan_id',
                'rujukandari_id',
                'no_rujukan',
                'nama_perujuk'
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asalrujukan_id' => Yii::t('fe', 'Asal rujukan'),
            'rujukandari_id' => Yii::t('fe', 'Rujukan dari'),
            'diagnosa_id' => Yii::t('fe', 'Diagnosa'),
            'no_rujukan' => Yii::t('fe', 'Nomor rujukan'),
            'nama_perujuk' => Yii::t('fe', 'Nama perujuk'),
            'tanggal_rujukan' => Yii::t('fe', 'Tanggal rujukan'),
        ];
    }
}

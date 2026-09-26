<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * Validation model for approval pengajuan form.
 *
 * @property string $noKartu
 * @property string $tglSep
 * @property int $jnsPelayanan
 * @property int $jnsPengajuan
 * @property string $keterangan
 * @property string $user
 */

class ApprovalPengajuanForm extends \yii\base\Model
{
    public $noKartu;
    public $tglSep;
    public $jnsPelayanan;
    public $jnsPengajuan;
    public $keterangan;
    public $user;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'noKartu',
                    'tglSep',
                    'jnsPelayanan',
                ], 
                'required'
            ],
            [
                [
                    'jnsPengajuan', 'keterangan', 'user',
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
            'noKartu' => Yii::t('fe', 'No Kartu BPJS'),
            'tglSep' => Yii::t('fe', 'Tanggal SEP'),
            'jnsPelayanan' => Yii::t('fe', 'Jenis Pelayanan'),
            'jnsPengajuan' => Yii::t('fe', 'Jenis Pengajuan'),
            'keterangan' => Yii::t('fe', 'Keterangan'),
        ];
    }
}
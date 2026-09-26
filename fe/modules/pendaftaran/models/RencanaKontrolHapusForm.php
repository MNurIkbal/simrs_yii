<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $rencanakontrol_id
 * @property string $username
 * @property string $password
 *
 */

class RencanaKontrolHapusForm extends \yii\base\Model
{
    public $rencanakontrol_id;
    public $username;
    public $password;
    public $noSuratKontrol;
    public $is_from_vclaim;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'rencanakontrol_id',
                'username',
                'password',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong'), 'on' => ['default']
            ],
            [[
                'noSuratKontrol',
                'username',
                'password',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong'), 'on' => ['hapus-dari-vclaim']
            ],
            [[
                'rencanakontrol_id',
                'noSuratKontrol',
                'is_from_vclaim',
            ],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rencanakontrol_id' => 'Rencana Kontrol ID',
            'username' => 'Username',
            'password' => 'Password',
            'noSuratKontrol' => 'No Surat Kontrol/SPRI',
        ];
    }

}

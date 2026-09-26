<?php

namespace app\modules\pendaftaran\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property string $nosep
 * @property string $nosurat
 * @property string $tglrujukinternal
 * @property string $kdpolituj
 * @property string $username
 *
 */

class HapusSepInternalForm extends \yii\base\Model
{
    public $noSep;
    public $noSurat;
    public $tglRujukanInternal;
    public $kdPoliTuj;
    public $username;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'noSep', 
                'noSurat',
                'tglRujukanInternal',
                'kdPoliTuj',
                'username',
            ], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'noSep' => 'No SEP',
            'noSurat' => 'No Surat Rujukan',
            'tglRujukanInternal' => 'Tangga Rujukan Internal',
            'kdPoliTuj' => 'Poli Tujuan',
            'username' => 'Username',
        ];
    }

}
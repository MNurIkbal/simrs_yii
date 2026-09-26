<?php

namespace app\modules\penjaminasuransi\models;

use Yii;

/**
 *
 * @property double $instalasi
 * @property string $no_pendaftaran
 */
class SyncPasienForm extends \yii\base\Model
{
    public $instalasi;
    public $no_pendaftaran;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['no_pendaftaran', 'instalasi'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_pendaftaran' => 'No Registrasi',
            'instalasi' => 'Instalasi',
        ];
    }
}

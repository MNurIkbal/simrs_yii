<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
class LoginJknR extends \Integrasi\Components\IntgrateActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logjkn_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'state', 'created_date','created_by','payload','sync_respon','pendaftaranol_id'], 'safe'],
        ];
    }
}
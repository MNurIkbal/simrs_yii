<?php

/**
 * @author Dede Herdiana
 * @todo Master Kontrak Manajemen
 * @copyright 18 Juni 2021
 */

namespace app\modules\master\models;
use app\components\DocoBaseModel;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tipediskon_v".
 *
 * @property int $tipediskon_id
 * @property string $tipediskon_nama
 * @property bool $is_active
 */

class TipeDiskonForm extends DocoBaseModel
{
    public $tipediskon_id;
    public $tipediskon_nama;
    public $is_active;

    /**
     * @todo Master Tipe Diskon
     * @return void
     */
    public function rules()
    {
        return [
            [['tipediskon_nama'], 'required', 'message' => 'Nama tipe diskon tidak boleh kosong!'],
            [['tipediskon_nama'], 'string'],
            [['tipediskon_id'], 'integer'],
            [['is_active', 'default', 'value' => false], 'safe'],
        ];
    }

}

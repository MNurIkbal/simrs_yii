<?php

namespace Doco\models\bpjs;

use Yii;

/**
 * This is the model class for table "{{%audit_trail_k}}".
 *
 * @property int $id
 * @property string $idrujukan
 * @property string $norujukan
 * @property string $nokapst
 * @property string $nmpst
 * @property string $diagppk
 * @property string $tglrujukan_awal
 * @property int $tglrujukan_berakhir
 * @property string $created_date
 * @property string $is_deleted
 * @property string $last_sync
 */
class BpjsRujukanKhususT extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpjs_rujukankhusus_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Audit Trail ID',
            'idrujukan' => 'Service Name',
            'norujukan' => 'Status',
            'nokapst' => 'Detail',
            'nmpst' => 'Action',
            'diagppk' => 'Messages',
            'tglrujukan_awal' => 'Stamp',
            'tglrujukan_berakhir' => 'Loginpemakai ID',
            'created_date' => 'Ip Address',
            'is_deleted' => 'Url Referer',
            'last_sync' => 'Browser',
        ];
    }
}
<?php

namespace Doco\Libraries\Asuransi\Models;

use Doco\components\DocoActiveRecord;

/**
 * This is the model class for table "penjamin_m".
 *
 * @property string $waktumulai
 * @property string $waktuselesai
 * @property string $payload
 * @property string $response
 * @property string $raw_response
 * @property string $url
 * @property integer $status_code
 * @property string $state
 * @property string $provider
 * @property string $created_at
 */
class IntegrasiVendorAsuransiR extends DocoActiveRecord
{

    public static function getDb()
    {
        return \Yii::$app->db_integration;
    }

    public static function tableName()
    {
        return 'integrasi_vendor_asuransi_r';
    }
}

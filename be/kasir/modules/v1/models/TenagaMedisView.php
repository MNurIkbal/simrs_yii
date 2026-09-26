<?php
/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasien_v".
 *
 * @property int $pasien_id
 * @property string $no_rekam_medik
 * @property string $tgl_rekam_medik
 * @property string $nama_pasien
 * @property string $tanggal_lahir
 */
class TenagaMedisView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tenagamedis_v';
    }
}

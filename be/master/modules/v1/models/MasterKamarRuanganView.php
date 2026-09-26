<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-09 16:35:24
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "masterkamarruangan_v".
 *
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property int $kamarruangan_jenis
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $kamartempattidur_id
 * @property string $no_tempattidur
 * @property bool $status_isi
 */
class MasterKamarRuanganView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'masterkamarruangan_v';
    }
}

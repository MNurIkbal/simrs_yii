<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "kertas_k".
 *
 * @property int $kertas_id
 * @property string $kertas_kode
 * @property string $kertas_nama
 * @property double $panjang
 * @property double $lebar
 * @property double $batas_kiri
 * @property double $batas_kanan
 * @property double $batas_atas
 * @property double $batas_bawah
 * @property double $ppi
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class KegiatanKeperawatan extends \Doco\components\DocoActiveRecord
{
    // protected $xssProtected = [
    //     'kertas_kode',
    //     'kertas_nama'
    // ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kegiatankeperawatan_m';
    }
}

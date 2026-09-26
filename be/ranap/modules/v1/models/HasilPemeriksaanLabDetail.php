<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "hasilpemeriksaanlabdetail_t".
 *
 * @property int $hasilpemeriksaanlabdetail_id
 * @property int $tindakanpelayanan_id
 * @property int $pemeriksaanlab_id
 * @property int $hasilpemeriksaanlab_id
 * @property int $nilairujukan_id
 * @property string $hasil
 * @property string $nilai_rujukan
 * @property string $satuan_hasil
 * @property string $keterangan
 * @property int $petugaslab_id
 * @property int $tindakanpaket_id
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
 *
 * @property HasilpemeriksaanlabT $hasilpemeriksaanlab
 * @property PemeriksaanlabM $pemeriksaanlab
 * @property TindakanpelayananT $tindakanpelayanan
 */
class HasilPemeriksaanLabDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasilpemeriksaanlabdetail_t';
    }
}

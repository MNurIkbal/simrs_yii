<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tariftindakanlab_v".
 *
 * @property int $tariftindakan_id
 * @property int $ruangan_id
 * @property string $ruangan_nama
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $jenispemeriksaanlab_id
 * @property string $jenispemeriksaanlab_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property int $pemeriksaanlab_id
 * @property string $pemeriksaanlab_nama
 * @property int $komponentarif_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property int $persencyto_tindakan
 * @property int $persendiskon_tindakan
 * @property bool $is_default
 */
class RincianPasienDetail2View extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rincianpasiendetail2_v';
    }
}

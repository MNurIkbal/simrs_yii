<?php

namespace Doco\models\Laboratorium;

use Yii;

/**
 * This is the model class for table "infoorderanlabdetail_v".
 *
 * @property int $permintaankepenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $no_rujukan
 * @property string $jenispemeriksaanlab_nama
 * @property string $daftartindakan_nama
 * @property int $qtypermintaan
 * @property bool $is_cyto
 */
class InfoOrderanLabDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoorderanlabdetail_v';
    }
}

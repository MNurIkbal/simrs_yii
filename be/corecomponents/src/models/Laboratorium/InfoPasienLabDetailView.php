<?php

namespace Doco\models\Laboratorium;

use Yii;

/**
 * This is the model class for table "infopasienlabdetail_v".
 *
 * @property int $tindakanpelayanan_id
 * @property int $pasienmasukpenunjang_id
 * @property string $tgl_tindakan
 * @property string $jenispemeriksaanlab_nama
 * @property int $daftartindakan_id
 * @property string $daftartindakan_nama
 * @property double $tarif_satuan
 * @property bool $cyto_tindakan
 * @property double $tarifcyto_tindakan
 * @property double $tarif_tindakan
 */
class InfoPasienLabDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    
    public static function primaryKey()
    {
        return [
            "pendaftaran_id", 
            "tindakanpelayanan_id", 
            "tipepaket_id",
            "daftartindakan_id",
        ];
    }
    public static function tableName()
    {
        return 'infopasienlabdetail_v';
    }
}

<?php

namespace Integrasi\Service\Roche\Models;


// use Integrasi\Components\Repositories\InfoPasienLabDetailViewRepositories;
/**
 * This is the model class for table "infopasienlabdetail_v".
 *
 */
class BridgingOrderLabRocheView extends \Integrasi\Components\ActiveRepositories
{
    // public $_repositori = InfoPasienLabDetailViewRepositories::class;


    // public static function primaryKey()
    // {
    //     return [
    //         "pendaftaran_id", 
    //         "tindakanpelayanan_id", 
    //         "tipepaket_id",
    //         "daftartindakan_id",
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bridging_orderlab_roche_v';
    }
}
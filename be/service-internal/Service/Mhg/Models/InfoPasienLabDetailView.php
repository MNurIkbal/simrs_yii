<?php

namespace Integrasi\Service\Mhg\Models;


use Integrasi\Components\Repositories\InfoPasienLabDetailViewRepositories;
/**
 * This is the model class for table "infopasienlabdetail_v".
 *
 */
class InfoPasienLabDetailView extends \Integrasi\Components\ActiveRepositories
{
    public $_repositori = InfoPasienLabDetailViewRepositories::class;


    public static function primaryKey()
    {
        return [
            "pendaftaran_id", 
            "tindakanpelayanan_id", 
            "tipepaket_id",
            "daftartindakan_id",
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasienlabdetail_v';
    }
}
<?php

namespace Integrasi\Service\InaCbgs\Models;


use Integrasi\Components\Repositories\InfoPasienBpjsViewRepositories;
/**
 * This is the model class for table "infopasienlabdetail_v".
 *
 */
class InfoPasienBpjsView extends \Integrasi\Components\ActiveRepositories
{
    public $_repositori = InfoPasienBpjsViewRepositories::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infopasienbpjs_v';
    }
}
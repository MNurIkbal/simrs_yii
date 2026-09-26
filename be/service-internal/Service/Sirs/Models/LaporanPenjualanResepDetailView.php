<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LaporanPenjualanResepDetailView extends \Integrasi\Components\ActiveRepositories
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'laporanpenjualanobatalkes_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [];
    }
}

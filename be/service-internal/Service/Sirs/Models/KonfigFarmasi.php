<?php

namespace Integrasi\Service\Sirs\Models;


class KonfigFarmasi extends \Integrasi\Components\ActiveRepositories {
    /**
     * {@inheritdoc}
     */

    public static function tableName()
    {
        return 'konfigfarmasi_k';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}


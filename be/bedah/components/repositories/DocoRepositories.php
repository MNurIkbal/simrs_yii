<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class DocoRepositories extends \yii\db\ActiveQuery
{

    public function getDataArray()
    {
        return $this->asArray()->all();
    }

    public function getRowArray()
    {
        return $this->asArray()->one();
    }

}

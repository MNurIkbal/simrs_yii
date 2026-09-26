<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

class DocoRepositories extends \yii\db\ActiveQuery
{

    /**
     * get row array
     * @return array
     */
    public function getDataArray()
    {
        return $this->asArray()->all();
    }

    /**
     * get one row
     * @return array
     */
    public function getRowArray()
    {
        return $this->asArray()->one();
    }

    /**
     * Convert to date time
     * @param  datetime $date
     * @return datetime
     */
    public function convertToDateTime($date)
    {
        return date('Y-m-d H:i:s', strtotime($date));
    }

    /**
     * @param  string $field
     * @param  datetime $start
     * @param  datetime $end
     * @return \yii\db\ActiveQuery
     */
    protected function betweenCondition($field, $start, $end)
    {
        $start = $this->convertToDateTime($start);
        $end = $this->convertToDateTime($end);
        return $this->andWhere(['between', $field, $start, $end]);
    }

}

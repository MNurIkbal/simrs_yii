<?php

/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\repositories;

use Doco\components\DocoConstansId;

class OdooRepositories extends \yii\db\ActiveQuery
{
    const LIMIT = 100;
    /**
     * where is_sending
     * @param  [boolean] $value
     * @return object
     */
    public function findByIsSending($value)
    {
        return $this->andWhere([
            'is_sending' => $value
        ]);
    }

    /**
     * where is_sent
     * @param  [boolean] $value
     * @return object
     */
    public function findByIsSent($value = false)
    {
        return $this->andWhere([
            'is_sent' => $value
        ]);
    }

    /**
     * untuk order by tangal proses
     * @param  [numeric] $order
     * @return object
     */
    public function orderByTglProses($order = SORT_ASC)
    {
        return $this->orderBy([
            'tglproses' => $order
        ]);
    }

    public function getDataArray()
    {
        $csize = DocoConstansId::actionGetAdditional(
            preg_replace('#.+\\\\(.+)$#', 'odoo_$1_size', strtolower($this->modelClass))
        );
        return $this->limit($csize ?: self::LIMIT)->asArray()->all();
    }

    public function findBySendingBill()
    {
        return $this->andWhere(['or',
            ['is_sent_billing' => true],
            ['is_sent_billing' => null],
        ]);
    }
}
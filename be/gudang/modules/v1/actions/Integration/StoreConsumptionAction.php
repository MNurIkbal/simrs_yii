<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use Doco\Services\InternalService;
use Doco\components\DocoConstansId;
use app\modules\v1\models\StockScrapView;

class StoreConsumptionAction extends Action {

    public function run() {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
        $stockScraps = StockScrapView::find()->andWhere([
            'is_sending' => false
        ])
        ->orderBy([
            'transaction_datetime' => SORT_ASC
        ])
        ->limit(DocoConstansId::actionGetAdditional('odoo_stockscrapview_size') ?: 50)
        ->all();

        if (empty($stockScraps)) {
            return [
                'status' => 422,
                'messages' => 'Tidak ada data yang di proses'
            ];
        }

        $tables = [
            StockScrapView::BMHP => 'pemakaianobatdetail_r',
            StockScrapView::ADJK => 'adjusmenobatkeluar_r',
            StockScrapView::ADJM => 'adjusmenobatmasuk_r',
            StockScrapView::MUSNAH => 'pemusnahanobatdetail_r',
            StockScrapView::SO => 'stokopnamedetail_r',
            StockScrapView::BMHP_BARANG => 'pemakaianbarangdetail_r',
            StockScrapView::ADJK_BARANG => 'adjusmenbarangkeluar_r',
            StockScrapView::ADJM_BARANG => 'adjusmenbarangmasuk_r',
            StockScrapView::MUSNAH_BARANG => 'pemusnahanbarangdetail_r',
            StockScrapView::SO_BARANG => 'stokopnamebarangdetail_r'
        ];
        $results = [];
        $ids = [];

        foreach ($stockScraps as $stockScrap) {
            if (array_key_exists($stockScrap->tipe_rekap, $tables)) {
                if (!array_key_exists($tables[$stockScrap->tipe_rekap], $ids)) {
                    $ids[$tables[$stockScrap->tipe_rekap]] = [];
                }
                $ids[$tables[$stockScrap->tipe_rekap]][] = $stockScrap->id;
                $results[] = $stockScrap->attributes;
            }
        }

        foreach ($ids as $table => $values) {
            $stringIds = implode(',', $values);
            Yii::$app->db->createCommand("
                UPDATE {$table} SET is_sending = true, id_sync_sercon = '{$uid}' WHERE id IN ({$stringIds})
            ")->execute();
        }

        (new InternalService)->sendTo([
            'Odoo' => [
                'StoreConsumption' => []
            ]
        ]);

        return $results;
    }
}
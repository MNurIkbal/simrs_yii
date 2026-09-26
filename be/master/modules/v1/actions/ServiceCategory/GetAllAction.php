<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ServiceCategory;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use app\modules\v1\cache\Cache;

class GetAllAction extends Action {
    public function run() {
        try {
            $data = Cache::getServiceCategory();
            return $data;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
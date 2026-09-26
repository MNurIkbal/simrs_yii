<?php 

namespace app\modules\v1\entities;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\InfoStokOpnameBarangDetailView;
use app\components\GudangComponent;

class StokOpnameBarangDetail {
    protected $_stokOpnameDetail;

    public function loadViewById($stokopname_id) {
        $detail = InfoStokOpnameBarangDetailView::find()->where(['stokopnamebarang_id' => $stokopname_id])->asArray()->all();
        if(empty($detail)) throw new \Exception("Error Processing Request", 1);

        $this->_stokOpnameDetail = $detail;
        return $this;
    }

    public function updateStokAkhir() {
        $arr_keys = $arr_stok_akhir = $arr_selisih_akhir = [];
        foreach ($this->_stokOpnameDetail as $value) {
            $arr_keys[] = $value['stokopnamebarangdetail_id'];
            $arr_stok_akhir[] = floatval(ArrayHelper::getValue($value, 'stok_sistem', 0)); 
            $arr_selisih_akhir[] = floatval(ArrayHelper::getValue($value, 'stok_selisih', 0));
        }

        if (!empty($arr_keys)) {
            $update_data = [
                'stok_akhir' => $arr_stok_akhir, 
                'selisih_akhir' => $arr_selisih_akhir
            ];

            $updateCondition = ['stokopnamebarangdetail_id' => $arr_keys];

            $update = GudangComponent::updateMultiple('stokopnamebarangdetail_t', $update_data, $updateCondition);
            if (!$update) throw new \Exception("Gagal update stok akhir", 1);
        }
    }
}
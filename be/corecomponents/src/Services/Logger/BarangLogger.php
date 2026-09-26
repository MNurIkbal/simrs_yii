<?php

namespace Doco\Services\Logger;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use Doco\Services\Cache;

class BarangLogger extends DocoBaseLogger
{
    public function __construct()
    {
        parent::__construct('baranghistory_r', 'barang_id');
    }

    public function log(ActiveRecord $model, $action, $oldData = null, $newData = null, array $extra = [])
    {
        if (ArrayHelper::getValue($oldData, 'barang_harganetto', null) != ArrayHelper::getValue($newData, 'barang_harganetto', null)) {
            $data = $this->buildData($model, $action, $oldData, $newData, $extra);
            Yii::$app->db->createCommand()->insert($this->logTable, $data)->execute();
        } 
    }

    protected function buildData(ActiveRecord $model, $action, $oldData, $newData, array $extra)
    {
        return [
            $this->foreignKey => $model->getPrimaryKey(),
            'tgl_baranghistory' => date('Y-m-d H:i:s'),
            'barang_nama' => ArrayHelper::getValue($newData, 'barang_nama'),
            'harga_dasar' => ArrayHelper::getValue($newData, 'barang_harganetto'),
            'keterangan' => $this->getKeteranganId($action),
            'catatan' => ArrayHelper::getValue($extra, 'catatan', null),
            'last_modified_by' => !empty(Yii::$app->jwt->user->loginpemakai_id) ? Yii::$app->jwt->user->loginpemakai_id : null,
        ];
    }

    private function getKeteranganId($lookup_value)
    {
        $lookup_keterangan_barang = Cache::getLookupByType('ket_hargabarang');
      
        return ArrayHelper::getValue(
            array_values(
                array_filter(
                    $lookup_keterangan_barang, function($var) use ($lookup_value){
                        return $var['lookup_value'] == $lookup_value;
                    }
                )
            ),
            '0.lookup_id', null
        );
    }
}

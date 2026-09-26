<?php

namespace Doco\Services\Logger;

use Yii;
use yii\db\ActiveRecord;

abstract class DocoBaseLogger
{
    protected $logTable;
    protected $foreignKey;

    public function __construct($logTable, $foreignKey)
    {
        $this->logTable = $logTable;
        $this->foreignKey = $foreignKey;
    }

    /**
     * Method utama untuk logging.
     * Bisa dipanggil langsung, atau di-override oleh subclass.
     */
    public function log(ActiveRecord $model, $action, $oldData = null, $newData = null, array $extra = [])
    {
        $data = $this->buildData($model, $action, $oldData, $newData, $extra);
        Yii::$app->db->createCommand()->insert($this->logTable, $data)->execute();
    }

    /**
     * Method untuk menyiapkan data insert log.
     * Default: simpan PK, action, old/new data, user, created_at.
     * Subclass bisa override sesuai kebutuhan.
     */
    protected function buildData(ActiveRecord $model, $action, $oldData, $newData, array $extra)
    {
        return array_merge([
            $this->foreignKey => $model->getPrimaryKey(),
            'action'     => $action,
            'old_data'   => $oldData ? json_encode($oldData) : null,
            'new_data'   => $newData ? json_encode($newData) : null,
            'created_at' => date('Y-m-d H:i:s'),
            'created_by' => !empty(Yii::$app->jwt->user->loginpemakai_id) ? Yii::$app->jwt->user->loginpemakai_id : null,
        ], $extra);
    }
}

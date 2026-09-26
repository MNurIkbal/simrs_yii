<?php

namespace app\modules\v1\models;

use Yii;

class DaftarPaketFisioDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daftarpaketfisiodet_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftarpaketfisiodet_id','daftarpaketfisio_id', 'daftartindakan_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftarTindakan()
    {
        return $this->hasOne(DaftarTindakan::class, ['daftartindakan_id' => 'daftartindakan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(DaftarPaketFisio::class, ['daftarpaketfisio_id' => 'daftarpaketfisio_id']);
    }
}

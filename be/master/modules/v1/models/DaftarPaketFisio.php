<?php

namespace app\modules\v1\models;

use Yii;

class DaftarPaketFisio extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daftarpaketfisio_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['frekuensi'], 'number', 'min' => 1, 'max' => 10],
            [['jumlah'], 'number', 'min' => 1, 'max' => 10],
            [['daftarpaketfisio_id', 'parent_id', 'daftarpaketfisio_nama', 'frekuensi', 'catatan', 'jumlah', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftarTindakan()
    {
        return $this->hasOne(DaftarTindakan::class, ['daftartindakan_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetails()
    {
        return $this->hasOne(DaftarPaketFisioDetail::class, ['daftarpaketfisio_id' => 'daftarpaketfisio_id']);
    }
}

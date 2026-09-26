<?php

namespace app\modules\v1\models;

use Yii;

class KonfigRakView extends \Doco\components\DocoActiveRecord {
    /**
     * {@inheritdoc}
     */

    public static function tableName() {
        return 'konfigrak_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Nama Obatalkes',
            'ruangan_id' => 'Ruangan ID',
            'ruangan_nama' => 'Nama Ruangan',
            'rakobat_id' => 'Rak Obat ID',
            'rakobat_nama' => 'Nama Rak Obat',
            'satuankecil_id' => 'Satuan Kecil ID',
            'satuankecil_nama' => 'Satuan Kecil',
            'min_stok' => 'Minimum Stok',
            'max_stok' => 'Maksimum Stok',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}

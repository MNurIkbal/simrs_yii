<?php

namespace app\modules\gudang\models;

use Yii;

class KonfigRakForm extends \yii\base\Model {
    /**
     * @inheritdoc
     */

    public $obatalkes_id;
    public $obatalkes_nama;
    public $ruangan_id;
    public $rakobat_id;
    public $min_stok;
    public $max_stok;
    public $satuan_kecil;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id',
                'ruangan_id',
                'rakobat_id',
                'min_stok',
                'max_stok',
                'modified_count',
                'last_modified_by',
                'deleted_by'], 'integer'],
            [['min_stok', 'max_stok'], 'default', 'value' => 0],
            [['min_stok', 'max_stok'], 'cekMinMax'],
            [['obatalkes_nama', 'satuan_kecil', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'obatalkes_nama' => 'Nama Obat Alkes',
            'ruangan_id' => 'Ruangan ID',
            'rakobat_id' => 'Rak',
            'min_stok' => 'Stok Minimal',
            'max_stok' => 'Stok Maksimal',
            'satuan_kecil' => 'Satuan Kecil',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By'
        ];
    }

    public function cekMinMax($attributes, $params) {
        if($this->max_stok < $this->min_stok) {
            $this->addError('max_stok', Yii::t('fe', 'Stok Maksimal tidak boleh lebih kecil dari Stok Minimal'));
        }

        if($this->min_stok > $this->max_stok) {
            $this->addError('min_stok', Yii::t('fe', 'Stok Minimal tidak boleh lebih besar dari Stok Maksimal'));
        }

        if($this->min_stok < 0) {
            $this->addError('min_stok', Yii::t('fe', 'Stok Minimal tidak boleh kurang dari 0'));
        }
    }
}

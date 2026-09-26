<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "esselon_m".
 *
 * @property integer $esselon_id
 * @property string $esselon_nama
 * @property string $esselon_namalainnya
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class KategoriTransaksiForm extends \yii\base\Model
{
    public $kategoritransaksi_id;
    public $kategoritransaksi_kode;
    public $kategoritransaksi_nama;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kategoritransaksi_kode', 'kategoritransaksi_nama'], 'required'],
            [['kategoritransaksi_kode', 'kategoritransaksi_nama'], 'safe'],
            [['is_active'], 'boolean'],
            [['kategoritransaksi_kode'], 'string', 'max' => 50],
            [['kategoritransaksi_nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kategoritransaksi_id' => 'Kategori Transaksi ID',
            'kategoritransaksi_kode' => 'Kode Kategori',
            'kategoritransaksi_nama' => 'Nama Kategori',
            'is_active' => 'Status',
        ];
    }
}

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
class JenisNonTunaiForm extends \yii\base\Model
{
    public $jenisnontunai_id;
    public $kode;
    public $nama;
    public $bank_id;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kode', 'nama', 'bank_id', 'is_active'], 'required'],
            [['kode', 'nama', 'bank_id'], 'safe'],
            [['bank_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['kode'], 'string', 'max' => 50],
            [['nama'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisnontunai_id' => 'Jenis Non Tunai ID',
            'kode' => 'Kode',
            'nama' => 'Jenis',
            'bank_id' => 'Bank',
            'is_active' => 'Status',
        ];
    }
}

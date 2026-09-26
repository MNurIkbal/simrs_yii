<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "konfigmargin_k".
 *
 * @property int $konfigmargin_id
 * @property string $perda_margin
 * @property string $tgl_berlaku
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $diskon
 */
class KonfigMarginForm extends \yii\base\Model
{
     public $konfigmargin_id;
     public $perda_margin;
     public $nama_margin;
     public $groupmargin_id;
     public $tgl_berlaku;
     public $detail;
     public $kelaspelayanan_id;
     public $jenisobatalkes_id;
     public $diskon;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'nama_margin', 
                'groupmargin_id', 
                'perda_margin', 
                'tgl_berlaku',
                'kelaspelayanan_id',
                'jenisobatalkes_id'
            ], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [[
                'groupmargin_id',
                'nama_margin',
                'perda_margin',
                'kelaspelayanan_id',
                'jenisobatalkes_id',
                'diskon'
            ], 'safe'],
            [['perda_margin'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'konfigmargin_id' => 'Konfigmargin ID',
            'groupmargin_id' => 'Group Margin',
            'perda_margin' => 'Perda/SK',
            'nama_margin' => 'Nama',
            'tgl_berlaku' => 'Tanggal Berlaku',
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
            'kelaspelayanan_id' => 'Kelas Pelayanan',
            'jenisobatalkes_id' => 'Jenis Obat',
            'discount' => 'Discount (%)',
        ];
    }
}

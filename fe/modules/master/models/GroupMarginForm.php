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
 */
class GroupMarginForm extends \yii\base\Model
{
     public $groupmargin_id;
     public $groupmargin_kode;
     public $groupmargin_nama;
     public $is_discount;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['groupmargin_nama'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['groupmargin_kode'], 'required','message'=>'{attribute} Tidak boleh kosong'],
            [['is_discount'], 'safe'],
            [['is_discount'], 'default', 'value' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'groupmargin_id' => 'Group Margin ID',
            'groupmargin_kode' => 'Group Margin Kode',
            'groupmargin_nama' => 'Group Margun Nama',
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
            'is_discount' => 'Diskon Khusus'
        ];
    }
}

<?php

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $pasienpulang_id
 * @property date $tgl_pembatalan
 * @property string $alasan_pembatalan
 * @property string $password
 * @property string $alasan_batal
 *
 */

class PasienBatalPulangForm extends \yii\base\Model
{
	public $pasienpulang_id;
	public $tgl_pembatalan;
    public $alasan_pembatalan;
    public $created_by;
    public $modified_count;
    public $last_modified_by;
    public $deleted_by;
    public $created_date;
    public $last_modified_date;
    public $deleted_date;
    public $is_deleted;
	public $is_active;
	public $kamarruangan_id;
	public $ruangan_id;
	public $kamartempattidur_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pasienpulang_id', 'tgl_pembatalan', 'alasan_pembatalan'], 'required'
            ,'message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')
            ],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kamartempattidur_id','kamarruangan_id','ruangan_id','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienpulang_id' => 'Pasien Pulang ID',
            'tgl_pembatalan' => 'Tanggal Pembatalan',
            'alasan_pembatalan' => 'Alasan Pembatalan',
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

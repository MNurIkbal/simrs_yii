<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 16:27:54
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-27 17:43:28
 */

namespace app\modules\master\models;

use Yii;

class KategoriTindakanForm extends \yii\base\Model
{
	public $kategoritindakan_id;
	public $kategori_kode;
	public $kategoritindakan_nama;
	public $kategoritindakan_namalainnya;
	public $catatan;
	public $is_active;
	public $daftartindakan_id;
	public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $deleted_date;
    public $deleted_by;

	public function rules()
	{
		return [
			[['kategori_kode','kategoritindakan_nama','kategoritindakan_namalainnya'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
			[['catatan','is_active','daftartindakan_id'],'safe']
		];
	}

	public function attributeLabels()
    {
        return [
            'kategoritindakan_id' => 'kategoritindakan ID',
            'kategoritindakan_nama' => 'Nama Kategori',
            'kategoritindakan_namalainnya' => 'Nama Lainnya',
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
            'kategori_kode' => 'Kode Kategori',
            'catatan' => 'Catatan',
            'daftartindakan_id'=>\Yii::t('fe','Nama Tindakan'),
        ];
    }
}
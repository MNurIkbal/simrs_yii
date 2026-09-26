<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "propinsi_m".
 *
* @property integer kunjungandetail_id
* @property integer kunjungan_id
* @property string no_pendaftaran
* @property string no_rekammedik
* @property integer kelompok_diagnosa
* @property string diagnosa_kode
* @property string diagnosa_nama
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

class SyKunjunganDetail extends \Doco\components\DocoActiveRecord
{
	public $kunjungandetail_id;
	public $kunjungan_id;
    public $no_pendaftaran;
    public $no_rekammedik;
    public $kelompok_diagnosa;
    public $diagnosa_kode;
    public $diagnosa_nama;

    public static function tableName()
    {
        return 'sy_kunjungandetail';
    }

    public function rules()
    {
        return [
            [['kunjungan_id', 'no_pendaftaran', 'no_rekammedik'], 'required'],
            [['kunjungan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kunjungan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pendaftaran', 'no_rekammedik'], 'string', 'max' => 150],
            [['kelompok_diagnosa'], 'string', 'max' => 25],
            [['diagnosa_kode'], 'string', 'max' => 50],
            [['diagnosa_nama'], 'string', 'max' => 255],
        ];
    }
}

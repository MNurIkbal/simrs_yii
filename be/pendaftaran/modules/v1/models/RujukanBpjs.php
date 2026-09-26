<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
/**
 * This is the model class for table "rujukanbpjs_t".
 *
 * @property integer $rujukanbpjs_id
 * @property integer $pendaftaran_id
 * @property integer $pasienadmisi_id
 * @property integer $instalasi_id
 * @property integer $diagnosa_id
 * @property integer $perujuk_id
 * @property integer $bpjs_id
 * @property integer $kelaspelayanan_id
 * @property date $tanggal_rujukan
 * @property string $no_rujukan
 * @property string $rujukan
 * @property string $spesialis
 * @property string $catatan_rujukan
 * @property string $jenis_pelayanan_bpjs
 * @property string $dirujukke
 * @property string $additional_request
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property string $tanggal_rencana_kunjungan
 * @property string $kode_spesialis
 */
class RujukanBpjs extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rujukanbpjs_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'pendaftaran_id', 'pasienadmisi_id', 'instalasi_id', 'diagnosa_id', 'perujuk_id',
                'bpjs_id', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by',
                'deleted_by'
            ], 'integer'],
            [[
                'diagnosa_rujukan', 'tanggal_rujukan', 'no_rujukan', 'rujukan', 'spesialis', 'catatan_rujukan',
                'jenis_pelayanan_bpjs', 'dirujukke', 'dirujukke_nama' ,'poli_rujukan', 'created_date', 'last_modified_date', 'deleted_date', 'tglsep', 'tanggal_rencana_kunjungan', 'kode_spesialis', 'additional_request'
            ], 'safe'],
            [[
                'diagnosa_rujukan_nama'
            ], 'string', 'max' => 255],
        ];
    }
}

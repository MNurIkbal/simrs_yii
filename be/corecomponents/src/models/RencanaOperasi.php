<?php

namespace Doco\models;

use Yii;

use Doco\components\DocoConstants;

/**
 * This is the model class for table "rencanaoperasi_t".
 *
 * @property int $rencanaoperasi_id
 * @property int $pasienkirimkeunitlain_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property string $tgl_permintaan
 * @property string $jam_rencana_mulai
 * @property string $jam_rencana_selesai
 * @property int $dr_operator_id
 * @property int $dr_anastesi_id
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
class RencanaOperasi extends \Doco\components\DocoActiveRecord
{

    protected $xssProtected = [
        'pemakaian_implant',
        'sewa_vendor',
        'sewa_alat_rs',
        'catatan_klinis'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rencanaoperasi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'pasienkirimkeunitlain_id',
                'jam_rencana_mulai',
                'jam_rencana_selesai',
                'dr_operator_id',
                'catatan_klinis'
            ], 'required'],
            [['pasienkirimkeunitlain_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'dr_operator_id', 'dr_anastesi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'dr_operator_id', 'dr_anastesi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                'pemakaian_implant',
                'sewa_vendor',
                'sewa_alat_rs',
                'jenis_operasi_cyto',
                'jenis_operasi_elektif',
                'jenis_operasi_odc',
                'tgl_permintaan',
                'jam_rencana_mulai',
                'jam_rencana_selesai',
                'created_date',
                'last_modified_date',
                'deleted_date',
                'rencanaoperasi_id',
                'dr_anastesi_id'
            ], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }
}

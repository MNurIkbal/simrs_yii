<?php

namespace app\modules\v1\models;

use Yii;

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
                'dr_anastesi_id'
            ], 'required'],
            [['pasienkirimkeunitlain_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'dr_operator_id', 'dr_anastesi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pasienkirimkeunitlain_id', 'pasienmasukpenunjang_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'dr_operator_id', 'dr_anastesi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [[
                'tgl_permintaan', 
                'jam_rencana_mulai', 
                'jam_rencana_selesai', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'rencanaoperasi_id'
            ], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rencanaoperasi_id' => 'Rencanaoperasi ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'tgl_permintaan' => 'Tgl Permintaan',
            'jam_rencana_mulai' => 'Jam Rencana Mulai',
            'jam_rencana_selesai' => 'Jam Rencana Selesai',
            'dr_operator_id' => 'Dr Operator ID',
            'dr_anastesi_id' => 'Dr Anastesi ID',
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
        ];
    }
}

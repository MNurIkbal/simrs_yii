<?php

namespace app\modules\integrator\models;

use Yii;

class PasienKirimKeUnitLain extends \Doco\components\DocoActiveRecord
{
    public static function tableName()
    {
        return 'pasienkirimkeunitlain_t';
    }

    public function rules()
    {
        return [
            [['pegawai_id', 'instalasi_id', 'pasien_id', 'kelaspelayanan_id', 'ruangan_id', 'tgl_kirimpasien'], 'required'],
            [['pegawai_id', 'pasienmasukpenunjang_id', 'instalasi_id', 'pasien_id', 'pendaftaran_id', 'kelaspelayanan_id', 'ruangan_id', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instruksi_id', 'pasienadmisi_id'], 'default', 'value' => null],
            [['pegawai_id', 'pasienmasukpenunjang_id', 'instalasi_id', 'pasien_id', 'pendaftaran_id', 'kelaspelayanan_id', 'ruangan_id', 'antrian_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'instruksi_id', 'pasienadmisi_id'], 'integer'],
            [[
                'tgl_kirimpasien', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'status_penunjang',
                'no_urut'
            ], 'safe'],
            [['catatan_dokterpengirim', 'additional_data'], 'string'],
            [['is_bayarkekasirpenunjang', 'is_deleted', 'is_active'], 'boolean'],
            [['no_urut'], 'string', 'max' => 3],
            [['no_orderkeunitlain'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'pegawai_id' => 'Pegawai ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'instalasi_id' => 'Instalasi ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'ruangan_id' => 'Ruangan ID',
            'antrian_id' => 'Antrian ID',
            'no_urut' => 'No Urut',
            'tgl_kirimpasien' => 'Tgl Kirimpasien',
            'catatan_dokterpengirim' => 'Catatan Dokterpengirim',
            'is_bayarkekasirpenunjang' => 'Is Bayarkekasirpenunjang',
            'no_orderkeunitlain' => 'No Orderkeunitlain',
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
            'instruksi_id' => 'Instruksi ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'status_penunjang' => 'Status Penunjang',
        ];
    }
}
<?php

namespace app\modules\v1\models;

use Yii;

class PengajuanKlaim extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\PengajuanKlaimRepository';

    /**
     * {@inheritdoc}
     */
    
    public static function tableName()
    {
        return 'pengajuanklaim_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carabayar_id', 'penjamin_id'], 'required'],
            [['carabayar_id', 'penjamin_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['carabayar_id', 'penjamin_id', 'pegawaimengetahui_id', 'status_pengajuanklaim', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pengajuanklaim', 'tgl_jatuhtempo', 'tgl_pelayanansampai', 'tgl_pelayanandari', 'created_date', 'last_modified_date', 'deleted_date','instalasi_id','ruangan_id','no_pengajuanklaim', 'tgl_keluardari', 'tgl_keluarsampai'], 'safe'],
            [['total_piutang', 'total_terbayar', 'total_sisapiutang', 'totalbiaya_obat', 'totalbiaya_tindakan'], 'number'],
            [['alamat_penjamin', 'catatan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pengajuanklaim'], 'string', 'max' => 255],
            [['npwp'], 'string', 'max' => 100],
            [['no_pengajuanklaim'], 'chkNoPengajuan'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pengajuanklaim_id' => 'Pengajuanklaim ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'tgl_pengajuanklaim' => 'Tanggal Pengajuan',
            'no_pengajuanklaim' => 'No Pengajuan',
            'tgl_jatuhtempo' => 'Tanggal Jatuh Tempo',
            'tgl_pelayanansampai' => 'Tanggal Pelayanansampai',
            'tgl_pelayanandari' => 'Tgl Pelayanandari',
            'tgl_keluarsampai' => 'Tanggal keluarsampai',
            'tgl_keluardari' => 'Tgl keluardari',
            'total_piutang' => 'Total Piutang',
            'total_terbayar' => 'Total Terbayar',
            'total_sisapiutang' => 'Total Sisapiutang',
            'alamat_penjamin' => 'Alamat Penjamin',
            'npwp' => 'Npwp',
            'totalbiaya_obat' => 'Totalbiaya Obat',
            'totalbiaya_tindakan' => 'Totalbiaya Tindakan',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'catatan' => 'Catatan',
            'status_pengajuanklaim' => 'Status Pengajuanklaim',
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

    public function chkNoPengajuan($params, $attributes)
    {
        $no_pengajuanklaim = $this->no_pengajuanklaim;
        if (strpos(substr($no_pengajuanklaim, 0, 1), ' ') !== FALSE) {
            $this->addError('no_pengajuanklaim', 'No Pengajuan mengandung spasi di awal kata');
            return false;
        } else {
            $model = self::find()->where([
                'TRIM(LOWER (no_pengajuanklaim))' => strtolower($no_pengajuanklaim), 
                'is_deleted' => false
            ])->one();
            if(!empty($model) && $model->pengajuanklaim_id != $this->pengajuanklaim_id ){
                $this->addError("no_pengajuanklaim","No Pengajuan Klaim Sudah Dipakai");
                return false;
            }
        }
    
        return true;
    }
}

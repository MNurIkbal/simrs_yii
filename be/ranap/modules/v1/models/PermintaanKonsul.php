<?php
/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-07-23 11:38:08
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "racikan_m".
 *
 * @property int $permintaankonsul_id
 * @property int $racikan_id
 * @property string $racikan_nama
 * @property string $racikan_singkatan
 * @property double $tarif_service
 * @property double $persen_service
 * @property double $biaya_kemasan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $pasienadmisi_id
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property AntrianfarmasiT[] $antrianfarmasiTs
 * @property RacikandetailM[] $racikandetailMs
 */
class PermintaanKonsul extends \Doco\components\DocoActiveRecord
{
   /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaankonsul_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'waktu_permintaan', 'dokter_id', 'jenis_konsul', 'ket_konsul'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'dokter_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'cppt_id'], 'integer'],
            [['created_date', 'last_modified_date', 'deleted_date','dokterdpjpasal_id','disetujui_oleh'], 'safe'],
            [['ket_konsul', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaankonsul_id' => 'Permintaan Konsul ID',
            'dokter_id' => 'Dokter ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'waktu_permintaan' => 'Waktu Permintaan',
            'jenis_konsul' => 'Jenis Konsul',
            'ket_konsul' => 'Keterangan Konsul',
            'status_konsul' => 'Status Konsul',
            'jawaban_konsul' => 'Jawaban Konsul',
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
            'dokterdpjpasal_id' => 'Dokter DPJP Asal',
            'cppt_id' => 'Cppt ID'
        ];
    }

}

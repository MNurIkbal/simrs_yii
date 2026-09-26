<?php

namespace Doco\models;

use Yii;

/**
 * This is the model class for table "permintaankepenunjang_t".
 *
 * @property int $permintaankepenunjang_id
 * @property int $daftartindakan_id
 * @property int $pasienkirimkeunitlain_id
 * @property int $tindakanpelayanan_id
 * @property int $tindakanrm_id
 * @property int $pemeriksaanrad_id
 * @property int $pemeriksaanlab_id
 * @property int $operasi_id
 * @property string $tglpermintaankepenunjang
 * @property int $qtypermintaan
 * @property double $tarif_pelayananan
 * @property bool $is_cyto
 * @property string $status_implementasi
 * @property int $tipepaket_id
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
 * @property bool $is_approve
 */
class PermintaanKepenunjangan extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permintaankepenunjang_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'pasienkirimkeunitlain_id', 'tglpermintaankepenunjang'], 'required'],
            [['daftartindakan_id', 'pasienkirimkeunitlain_id', 'tindakanpelayanan_id', 'tindakanrm_id', 'pemeriksaanrad_id', 'pemeriksaanlab_id', 'operasi_id', 'qtypermintaan', 'tipepaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['is_approve'], 'default', 'value' => false],
            [['daftartindakan_id', 'pasienkirimkeunitlain_id', 'tindakanpelayanan_id', 'tindakanrm_id', 'pemeriksaanrad_id', 'pemeriksaanlab_id', 'operasi_id', 'qtypermintaan', 'tipepaket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tglpermintaankepenunjang', 'created_date', 'last_modified_date', 'deleted_date','tarif_pelayanan','tarif_cytotindakan', 'alasan_batal'], 'safe'],
            [['tarif_pelayanan'], 'number'],
            [['is_cyto', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['status_implementasi'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaankepenunjang_id' => 'Permintaankepenunjang ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'pasienkirimkeunitlain_id' => 'Pasienkirimkeunitlain ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'tindakanrm_id' => 'Tindakanrm ID',
            'pemeriksaanrad_id' => 'Pemeriksaanrad ID',
            'pemeriksaanlab_id' => 'Pemeriksaanlab ID',
            'operasi_id' => 'Operasi ID',
            'tglpermintaankepenunjang' => 'Tglpermintaankepenunjang',
            'qtypermintaan' => 'Qtypermintaan',
            'tarif_pelayananan' => 'Tarif Pelayananan',
            'is_cyto' => 'Is Cyto',
            'status_implementasi' => 'Status Implementasi',
            'tipepaket_id' => 'Tipepaket ID',
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

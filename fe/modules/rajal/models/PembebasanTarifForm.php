<?php
// author: Ardi Pratama

namespace app\modules\rajal\models;

use Yii;

class PembebasanTarifForm extends \yii\base\Model
{

    public $pembebasantarif_id;
    public $pendaftaran_id;
    public $pembayaranpelayanan_id;
    public $tgl_pembebasantarif;
    public $no_pembebasantarif;
    public $total_pembebasantarif;
    public $catatan;
    public $status_pembebasantarif;
    public $pegawaimengetahui_id;
    public $pegawaimenyetujui_id;
    public $jabatanmengetahui_id;
    public $jabatanmenyetujui_id;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $total_tarifpelayanan;
    public $total_tagihan;
 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pembebasantarif_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'total_tarifpelayanan'], 'required'],
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'pegawaimengetahui_id', 'pegawaimenyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pembayaranpelayanan_id', 'pegawaimengetahui_id', 'pegawaimenyetujui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendaftaran_id','tgl_pembebasantarif', 'created_date', 'last_modified_date', 'deleted_date','total_tagihan'], 'safe'],
            [['total_pembebasantarif'], 'number'],
            [['total_tarifpelayanan'], 'number','min'=>1],
            [['catatan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pembebasantarif'], 'string', 'max' => 255],
            [['status_pembebasantarif'], 'string', 'max' => 32],
            [['no_pembebasantarif'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pembebasantarif_id' => 'Pembebasantarif ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'tgl_pembebasantarif' => Yii::t('fe', 'tgl_pembebasantarif'),
            'no_pembebasantarif' => Yii::t('fe', 'no_pembebasantarif'),
            'total_pembebasantarif' => Yii::t('fe', 'total_pembebasantarif'),
            'catatan' => Yii::t('fe', 'catatan'),
            'status_pembebasantarif' => Yii::t('fe', 'status_pembebasantarif'),
            'pegawaimengetahui_id' => Yii::t('fe', 'pegawaimengetahui_id'),
            'pegawaimenyetujui_id' => Yii::t('fe', 'pegawaimenyetujui_id'),
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
            'total_tarifpelayanan' => Yii::t('fe', 'total_tarifpelayanan'),
            'jabatanmengetahui_id' => Yii::t('fe', 'Jabatan'),
            'jabatanmenyetujui_id' => Yii::t('fe', 'Jabatan')
        ];
    }
}

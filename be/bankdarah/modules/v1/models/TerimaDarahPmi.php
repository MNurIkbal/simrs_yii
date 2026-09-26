<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\TerimaDarahPmiDetail;

/**
 * This is the model class for table "terimadarahpmi_t".
 *
 * @property int $terimadarahpmi_id
 * @property int $pesandarahpmi_id
 * @property int $ruangan_id
 * @property int $penerima_id pegawai
 * @property string $tgl_terimadarahpmi
 * @property string $no_terimadarahpmi
 * @property double $total_harga
 * @property int $total_kantongdarah
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
class TerimaDarahPmi extends \Doco\components\DocoActiveRecord
{
    public $dataDetail;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'terimadarahpmi_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pesandarahpmi_id', 'ruangan_id', 'penerima_id', 'dataDetail'], 'required'],
            [['pesandarahpmi_id', 'ruangan_id', 'penerima_id', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pesandarahpmi_id', 'ruangan_id', 'penerima_id', 'total_kantongdarah', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['dataDetail', 'no_pesandarahpmi', 'no_tlp', 'nama_pmi', 'supplier_alamat', 'tgl_terimadarahpmi', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['total_harga'], 'number'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_terimadarahpmi'], 'string', 'max' => 255],
            [['dataDetail'], 'chkDetail'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimadarahpmi_id' => 'Terimadarahpmi ID',
            'pesandarahpmi_id' => 'Pesandarahpmi ID',
            'ruangan_id' => 'Ruangan ID',
            'penerima_id' => 'Petugas Penerima',
            'tgl_terimadarahpmi' => 'Tgl Terimadarahpmi',
            'no_terimadarahpmi' => 'No Terimadarahpmi',
            'total_harga' => 'Total Harga',
            'total_kantongdarah' => 'Total Kantongdarah',
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

    public function chkDetail($params, $attributes)
    {
        $no_kantongdarah = [];
        $inCondition = [];
        foreach ($this->dataDetail as $key => $value) {
            if(is_array($value)) {
                foreach($value as $kk => $vv) {
                    $no_kantongdarah[$vv['no_kantongdarah']] = $kk; 
                    $inCondition[] = strtolower($vv['no_kantongdarah']); 
                }
            }
        }

        $model = TerimaDarahPmiDetail::find()->where([
            'TRIM(LOWER (no_kantongdarah))' => $inCondition, 
            'is_deleted' => false
        ])->all();

        foreach($model as $k => $v) {
            if(isset($no_kantongdarah[$v['no_kantongdarah']])) {
                $idKey = $no_kantongdarah[$v['no_kantongdarah']];
                $this->addError('no_kantongdarah-'.$idKey.'', 'No Kantong Darah tidak boleh sama.');
            } 
        }
    }
}

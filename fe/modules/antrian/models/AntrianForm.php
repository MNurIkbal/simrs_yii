<?php

namespace app\modules\antrian\models;

use Yii;

/**
 * This is the model class for table "antrian_t".
 *
 * @property int $antrian_id
 * @property int $ruangan_id
 * @property int $carabayar_id
 * @property int $pendaftaran_id
 * @property int $layarantrian_id
 * @property int $loket_id
 * @property string $tgl_antrian
 * @property string $no_antrian
 * @property string $status_pasien
 * @property string $carabayar_loket
 * @property bool $panggil_flag
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
 *
 * @property CarabayarM $carabayar
 * @property LoketM $loket
 * @property PendaftaranT $pendaftaran
 * @property ProfilrumahsakitM $layarantrian
 * @property RuanganM $ruangan
 * @property PendaftaranT[] $pendaftaranTs
 * @property PenjualanresepT[] $penjualanresepTs
 */
class AntrianForm extends \yii\base\Model
{
    public $no_rekam_medik;
    public $display_rekam_medik;
    public $cara_bayar;
    public $penjamin;

    public $INSTALASI_RAJAL = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'antrian_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'carabayar_id', 'layarantrian_id', 'tgl_antrian', 'no_antrian'], 'required'],
            [['ruangan_id', 'carabayar_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'carabayar_id', 'pendaftaran_id', 'layarantrian_id', 'loket_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_antrian', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['panggil_flag', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['no_antrian'], 'string', 'max' => 6],
            [['status_pasien', 'carabayar_loket'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'antrian_id' => Yii::t('fe','Antrian ID'),
            'ruangan_id' => Yii::t('fe','Ruangan ID'),
            'carabayar_id' => Yii::t('fe','Carabayar ID'),
            'pendaftaran_id' => Yii::t('fe','Pendaftaran ID'),
            'layarantrian_id' => Yii::t('fe','Layarantrian ID'),
            'loket_id' => Yii::t('fe','Loket ID'),
            'tgl_antrian' => Yii::t('fe','Tgl Antrian'),
            'no_antrian' => Yii::t('fe','No Antrian'),
            'status_pasien' => Yii::t('fe','Status Pasien'),
            'carabayar_loket' => Yii::t('fe','Carabayar Loket'),
            'panggil_flag' => Yii::t('fe','Panggil Flag'),
            'additional_data' => Yii::t('fe','Additional Data'),
            'created_date' => Yii::t('fe','Created Date'),
            'created_by' => Yii::t('fe','Created By'),
            'modified_count' => Yii::t('fe','Modified Count'),
            'last_modified_date' => Yii::t('fe','Last Modified Date'),
            'last_modified_by' => Yii::t('fe','Last Modified By'),
            'is_deleted' => Yii::t('fe','Is Deleted'),
            'is_active' => Yii::t('fe','Is Active'),
            'deleted_date' => Yii::t('fe','Deleted Date'),
            'deleted_by' => Yii::t('fe','Deleted By'),
            'no_rekam_medik' => Yii::t('fe','No. Rekam Medik')
        ];
    }

}

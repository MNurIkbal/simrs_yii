<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "loket_m".
 *
 * @property integer $loket_id
 * @property integer $carabayar_id
 * @property string $loket_nama
 * @property string $loket_namalain
 * @property string $loket_fungsi
 * @property string $loket_singkatan
 * @property integer $loket_nourut
 * @property string $loket_formatnomor
 * @property integer $loket_maxantrian
 * @property string $filesuara
 * @property boolean $is_pendaftaran
 * @property boolean $is_kasir
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 * @property integer $layarantrian_id
 *
 * @property AntrianT[] $antrianTs
 * @property CarabayarM $carabayar
 */
class Loket extends \Doco\components\DocoActiveRecord
{
    public $nama_loket;
    public $no_loket;
    protected $xssProtected = [
        'loket_nama',
        'loket_namalain',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'loket_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['carabayar_id', 'loket_nourut', 'loket_maxantrian', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'layarantrian_id'], 'integer'],
            [['loket_nama'], 'required'],
            [['loket_fungsi', 'additional_data'], 'string'],
            [['is_pendaftaran', 'is_kasir', 'is_deleted', 'is_active'], 'boolean'],
            [['created_date', 'last_modified_date', 'deleted_date','jenisantrian_id','loket_id','no_loket','nama_loket'], 'safe'],
            [['loket_nama', 'loket_namalain'], 'string', 'max' => 50],
            [['loket_singkatan'], 'string', 'max' => 1],
            [['loket_formatnomor'], 'string', 'max' => 5],
            [['filesuara'], 'string', 'max' => 500],
            [['loket_nama'],'checkLoket'],
            [['loket_nourut'],'checkNoUrut'],
        ];
    }

    public function checkNoUrut($attribute, $params,$validator)
    {
        $request = Yii::$app->request;
        if (!empty($this->jenisantrian_id)) {
            $query = Yii::$app->db->createCommand("
                SELECT loket_id FROM loket_m 
                WHERE jenisantrian_id = $this->jenisantrian_id 
                AND loket_nourut = $this->loket_nourut
                AND is_deleted = false
            ")->queryOne();

            if (!empty($query)) {
                if ($this->loket_id != $query['loket_id']) {
                    $this->addError('no_loket','No Loket sudah terpakai');
                }
            }
        }
    }

    public function checkLoket($attribute, $params,$validator)
    {
        $request = Yii::$app->request;
        if (!empty($this->jenisantrian_id)) {
            $query = Yii::$app->db->createCommand("
                SELECT loket_id FROM loket_m 
                WHERE jenisantrian_id = $this->jenisantrian_id 
                AND LOWER(loket_nama) = LOWER('{$this->loket_nama}')
                AND is_deleted = false
            ")->queryOne();

            if (!empty($query)) {
                if ($this->loket_id != $query['loket_id']) {
                    $this->addError('nama_loket','Nama Loket sudah terpakai');
                }
            }
        }

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'loket_id' => 'Loket ID',
            'carabayar_id' => 'Carabayar ID',
            'loket_nama' => 'Loket Nama',
            'loket_namalain' => 'Loket Namalain',
            'loket_fungsi' => 'Loket Fungsi',
            'loket_singkatan' => 'Loket Singkatan',
            'loket_nourut' => 'Loket Nourut',
            'loket_formatnomor' => 'Format Nomor',
            'loket_maxantrian' => 'Loket Maxantrian',
            'filesuara' => 'Filesuara',
            'is_pendaftaran' => 'Is Pendaftaran',
            'is_kasir' => 'Is Kasir',
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
            'layarantrian_id' => 'Layarantrian ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAntrianTs()
    {
        return $this->hasMany(Antrian::className(), ['loket_id' => 'loket_id']);
    }

    public function getDetail()
    {
        return $this->hasMany(LoketMp::className(),['loket_id' => 'loket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarabayar()
    {
        return $this->hasOne(CaraBayar::className(), ['carabayar_id' => 'carabayar_id']);
    }
}

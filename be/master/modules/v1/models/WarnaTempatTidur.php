<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
/**
 * This is the model class for table "kettempattidur_m".
 *
 * @property integer $kettempattidur_id
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 * @property string $rgb
 * @property integer $kamarruangan_jenis
 * @property boolean $is_kosong
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class WarnaTempatTidur extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'kettempattidur_nama',
        'kettempattidur_warna',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kettempattidur_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_kosong','kettempattidur_nama', 'kettempattidur_warna' ,'kode_warna' ,'rgb', 'kamarruangan_jenis'], 'default', 'value' => null],
            [['kamarruangan_jenis', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['kettempattidur_nama','is_kosong', 'kode_warna'], 'required'],
            [['is_kosong' ,'is_deleted', 'is_active'], 'boolean'],
            [['kettempattidur_nama', 'kettempattidur_warna', 'kode_warna' ,'rgb'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kettempattidur_warna', 'rgb'], 'string', 'max' => 50],
            [['kettempattidur_nama'], 'string', 'max' => 255],
            [['kettempattidur_nama'], 'trimWhitespace'],
            // [['kettempattidur_nama','is_kosong'], 'checkUniqueCase'],
            [['kettempattidur_nama'], 'checkUnique'],
            [['kode_warna'], 'checkUniqueWarna'],
            // [['instalasi_singkatan'], 'string', 'max' => 5],
            /*[['profilers_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProfilRumahSakit::className(), 'targetAttribute' => ['profilers_id' => 'profilrs_id']],*/
        ];
    }

    public function trimWhitespace(){
        $kettempattidur_nama = $this->kettempattidur_nama;
        $return = true;
        if (strpos(substr($kettempattidur_nama, 0, 1), ' ') !== FALSE) {
            $this->addError('kettempattidur_nama', 'Kode mengandung spasi di awal kata');
            $return = false;
        }
        
        return $return;
    }

    public function checkUniqueCase($attribute, $params)
    {
        $kettempattidur_nama = strtolower($this->kettempattidur_nama);
        $kamarruangan_jenis = $this->kamarruangan_jenis;
        // $is_kosong = ($this->is_kosong == 0) ? false : true;
        $is_kosong = $this->is_kosong;
        $model = WarnaTempatTidur::find();
        $query = $model->Where(['=', 'LOWER(kettempattidur_nama)', $kettempattidur_nama ])
                        ->andWhere([
                                    'kamarruangan_jenis' => $kamarruangan_jenis,
                                    'is_kosong' => $is_kosong,
                                    'is_deleted' => false,
                                    'is_active' => true,
                                    ]);
        $getLookup = Lookup::find()->Where(['lookup_id'=> $this->kamarruangan_jenis])->asArray()->one();
        $kosong = [1=>'Kosong',0=>'Isi'];
        $valKosong = $kosong[$is_kosong];
   
        if (empty($this->kettempattidur_id)) { // con create
            $data = $query->asArray()->one();
            if(!empty($data)){
                $this->addError('kettempattidur_nama','"'.'Keterangan Tempat Tidur : '.$this->kettempattidur_nama.' , '.'<br> Jenis Kamar Ruangan : '.$getLookup['lookup_name'].'<br> Status Kamar : '.$valKosong.'" &nbsp; telah dipergunakan.');
                return false;
            }
        }else{
            $data = $query->andWhere(['not in','kettempattidur_id',[$this->kettempattidur_id] ])
                        ->asArray()->one();
            if(!empty($data)){
                $this->addError('kettempattidur_nama','"'.'Keterangan Tempat Tidur : '.$this->kettempattidur_nama.' , '.'<br> Jenis Kamar Ruangan : '.$getLookup['lookup_name'].'<br> Status Kamar : '.$valKosong.'" &nbsp; telah dipergunakan.');
                return false;
            }
        }

        return true;
    }

    public function checkUnique()
    {
        $kettempattidur_nama = strtolower($this->kettempattidur_nama);
        $model = self::find()->where([
            'ILIKE', 'LOWER(kettempattidur_nama)',$kettempattidur_nama,
        ])->one();
        if (!empty($model) && $model->kettempattidur_id != $this->kettempattidur_id) {
            $this->addError('kettempattidur_nama', 'Keterangan Tempat Tidur telah dipergunakan.');
        }
    }

    public function checkUniqueWarna()
    {
        $kode_warna = strtolower($this->kode_warna);
        $model = self::find()->where([
            'ILIKE', 'LOWER(kode_warna)',$kode_warna,
        ])->one();
        if (!empty($model) && $model->kettempattidur_id != $this->kettempattidur_id) {
            $this->addError('kode_warna', 'Warna Tempat Tidur telah dipergunakan.');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kettempattidur_id' => Yii::t('app', 'Keterangan Tempat Tidur ID'),
            'kettempattidur_nama' => Yii::t('app', 'Nama Tempat Tidur'),
            'kettempattidur_warna' => Yii::t('app', 'Nama Warna Tempat Tidur'),
            'kode_warna' => Yii::t('app', 'Kode Warna'),
            'rgb' => Yii::t('app', 'Warna RGB'),
            'kamarruangan_jenis' => Yii::t('app', 'Jenis Kamar Ruangan'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Status'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'is_kosong' => Yii::t('app', 'Status Kamar'),
        ];
    }
}

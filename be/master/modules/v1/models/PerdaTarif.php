<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "perdatarif_m".
 *
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property string $perda_no
 * @property string $perda_tgl
 * @property string $perda_tentang
 * @property string $ditetapkan_oleh
 * @property string $tempat_ditetapkan
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
 * @property TariftindakanM[] $tariftindakanMs
 */
class PerdaTarif extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'perdatarif_m';
    }

    /**
     * {@inheritdoc}
     */
    
    // Validasi XXS form
    protected $xssProtected = [
        'perdanama_sk',
        'perda_no',
        'perda_tgl',
        'perda_tentang',
        'ditetapkan_oleh',
        'tempat_ditetapkan',
        'nama_lainnya',
        'additional_data'
    ];

    public function rules()
    {
        return [
            [['perda_no','perdanama_sk','nama_lainnya','perda_tgl'], 'required','message'=>'{attribute} '.Yii::t('app','Tidak boleh kosong')],
            [['perda_tgl','nama_lainnya', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['perda_tentang', 'additional_data'], 'string'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['perdanama_sk'], 'string', 'max' => 200],
            [['perda_no'], 'string', 'max' => 20],
            [['ditetapkan_oleh', 'tempat_ditetapkan'], 'string', 'max' => 30],
            [['perda_no'], 'chkPerdaNo'],
            [['perdanama_sk'], 'chkNamaPerda'],
            // [['is_active'], 'chkIsAktive'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'perda_no' => 'Perda No',
            'perda_tgl' => 'Perda Tgl',
            'perda_tentang' => 'Perda Tentang',
            'ditetapkan_oleh' => 'Ditetapkan Oleh',
            'tempat_ditetapkan' => 'Tempat Ditetapkan',
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

    public function chkPerdaNo(){
        $perda_no = $this->perda_no;
        $model = Self::find()->where(['like','lower(perda_no)',strtolower($perda_no)])->andWhere(['is_deleted' => false])->one();
        if(!empty($model) && $model->perdatarif_id != $this->perdatarif_id){
            $this->addError('perda_no','Kode Sudah Dipakai');
            return false;
        }
        return true;
    }

    public function chkNamaPerda(){
        $perdanama_sk = $this->perdanama_sk;
        $model = self::find()->where(['LOWER (perdanama_sk)' => strtolower($this->perdanama_sk), 'is_deleted' => false])->one();
        if (!empty($model) && $model->perdatarif_id != $this->perdatarif_id) {
            $this->addError("perdanama_sk", "Nama Perda Sudah Dipakai");
            return false;
        }

        return true;
    }

    /*public function chkIsAktive(){
        $is_active = $this->is_active;
        if ($is_active == 1) {
            $model = Self::find()
                    ->where(['is_active' => true])
                    ->andWhere(['is_deleted' => false])
                    ->one();
        }
        if(!empty($model)){
            $this->addError('is_active','Perda yang aktif telah digunakan');
            return false;
        }
        return true;
    }*/

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTariftindakanMs()
    {
        return $this->hasMany(TariftindakanM::className(), ['perdatarif_id' => 'perdatarif_id']);
    }
}

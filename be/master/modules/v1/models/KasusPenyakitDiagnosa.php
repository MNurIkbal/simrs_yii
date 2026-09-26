<?php

/**
 * @Author: rizal
 * @Date:   2018-01-05 13:43:12
 * @Last Modified by:   ijal
 * @Last Modified time: 2018-01-05 13:43:22
 * @Description: model kasuspenyakitdiagnosa_mp
 */

namespace app\modules\v1\models;
use app\modules\v1\models\JenisKasusPenyakit;
use app\modules\v1\models\Diagnosa;
use Yii;

/**
 * This is the model class for table "kasuspenyakitdiagnosa_mp".
 *
 * @property integer $jeniskasuspenyakit_id
 * @property integer $diagnosa_id
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
 *
 * @property DiagnosaM $diagnosa
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 */
class KasusPenyakitDiagnosa extends \Doco\components\DocoActiveRecord
{

    public $diagnosa_kode;
    public $jeniskasuspenyakit_nama;
    public $jeniskasuspenyakit_id_before;
    public $diagnosa_id_before;
    public $type_method;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitdiagnosa_mp';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ['jeniskasuspenyakit_id', 'diagnosa_id'];
        // return static::getTableSchema()->primaryKey;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskasuspenyakit_id', 'diagnosa_id','created_by', 'modified_count', 'last_modified_by', 'created_date', 'last_modified_date', 'deleted_date','deleted_by'], 'default', 'value' => null],
            [['jeniskasuspenyakit_id', 'diagnosa_id'],'required'],
            [['jeniskasuspenyakit_id','diagnosa_id'], 'checkUniqueCase'],
            [['jeniskasuspenyakit_id', 
                'diagnosa_id', 
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by'
                ],'integer'
            ],
            [['additional_data', 'jeniskasuspenyakit_nama', 'diagnosa_kode'],'string'],
            [['created_date', 'last_modified_date', 'deleted_date'],'safe'],
            [['is_deleted', 'is_active'],'boolean']
        ];
    }

    public function checkUniqueCase($attribute, $params)
    {
        $jeniskasuspenyakit_id = $this->jeniskasuspenyakit_id;
        $diagnosa_id = $this->diagnosa_id;

        $query = KasusPenyakitDiagnosa::find(true)->where([
            'jeniskasuspenyakit_id' => $this->jeniskasuspenyakit_id,
            'diagnosa_id' => $this->diagnosa_id            
        ]);
        $query->andWhere(['is_deleted' => false]);
        $resKasusPenyakitDiagnosa =  $query->one();

        $getJenisKasusPenyakit = JenisKasusPenyakit::find()->Where(['jeniskasuspenyakit_id'=> $this->jeniskasuspenyakit_id])->one();
        $getDiagnosa = Diagnosa::find()->Where(['diagnosa_id'=> $this->diagnosa_id])->asArray()->one();
        $getDiagnosaBefore = KasusPenyakitDiagnosa::find()->Where(['jeniskasuspenyakit_id'=> $this->jeniskasuspenyakit_id_before,'diagnosa_id' => $this->diagnosa_id_before])->one();       

        
        if (!empty($resKasusPenyakitDiagnosa)) {
            if($this->type_method == 'create'){
                $this->addError('diagnosa_id','"'.'Jenis Kasus Penyakit : '.$getJenisKasusPenyakit->jeniskasuspenyakit_nama.' dan '.'<br> Diagnosa : '.$getDiagnosa['diagnosa_kode'].' - '.$getDiagnosa['diagnosa_nama'].'" sudah ada.');
                return false;                
            }

            if($this->type_method == 'update'){
                if( ($resKasusPenyakitDiagnosa->jeniskasuspenyakit_id != $getDiagnosaBefore->jeniskasuspenyakit_id) ||
                  ($resKasusPenyakitDiagnosa->diagnosa_id != $getDiagnosaBefore->diagnosa_id) ){
                    $this->addError('diagnosa_id','"'.'Jenis Kasus Penyakit : '.$getJenisKasusPenyakit->jeniskasuspenyakit_nama.' dan '.'<br> Diagnosa : '.$getDiagnosa['diagnosa_kode'].' - '.$getDiagnosa['diagnosa_nama'].'" sudah ada.');
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit',
            'diagnosa_id' => 'Diagnosa',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosa()
    {
        $link = ['diagnosa_id' => 'diagnosa_id'];
        return $this->hasOne(Diagnosa::className(), $link);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJenisKasusPenyakit()
    {
        $link = ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id'];
        return $this->hasOne(JenisKasusPenyakit::className(), $link);
    }

    /**
    * @author Rizal
    * @since 2018-01-05 13:47:52
    * @param 
    * @return 
    * @desc extra fields
    */
    public function extraFields()
    {
        return ['jenisKasusPenyakit', 'diagnosa'];
        // return [
        //     'jeniskasuspenyakit_m' => function($item){
        //         return $item->jenisKasusPenyakit;
        //     },
        //     'ruangan_m' => function($item){
        //         return $item->ruangan;
        //     }
        // ];
    }
}

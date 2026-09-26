<?php

/**
 * @Author: afil
 * @Date:   2018-01-03 16:06:30
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-03 17:20:32
 * @Description: model kasuspenyakitruangan_mp
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kasuspenyakitruangan_mp".
 *
 * @property integer $ruangan_id
 * @property integer $jeniskasuspenyakit_id
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
 * @property JeniskasuspenyakitM $jeniskasuspenyakit
 * @property RuanganM $ruangan
 */
class KasusPenyakitRuangan extends \Doco\components\DocoActiveRecord
{
    public $ruangan_nama;
    public $jeniskasuspenyakit_nama;
    public $jeniskasuspenyakit_id_before;
    public $ruangan_id_before;
    public $type_method;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitruangan_mp';
    }

    /**
     * @inheritdoc$primaryKey
     */
    public static function primaryKey()
    {
        return ['ruangan_id', 'jeniskasuspenyakit_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'required'],
            [['ruangan_id', 'jeniskasuspenyakit_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['ruangan_id', 'jeniskasuspenyakit_id'], 'checkUniqueCase'],
            // [['jeniskasuspenyakit_id'], 'exist', 'skipOnError' => true, 'targetClass' => JenisKasusPenyakit::className(), 'targetAttribute' => ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']],
            // [['ruangan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruangan::className(), 'targetAttribute' => ['ruangan_id' => 'ruangan_id']],
        ];
    }

    public function checkUniqueCase($attribute, $params)
    {
        $ruangan_id = $this->ruangan_id;
        $jeniskasuspenyakit_id = $this->jeniskasuspenyakit_id;

        $query = KasusPenyakitRuangan::find(true)->where([
            'ruangan_id' => $this->ruangan_id,            
            'jeniskasuspenyakit_id' => $this->jeniskasuspenyakit_id
        ]);
        $query->andWhere(['is_deleted' => false]);
        $resKasusPenyakitRuangan =  $query->one();

        // $getDiagnosa
        $getRuangan = Ruangan::find()->Where(['ruangan_id'=> $this->ruangan_id])->asArray()->one();
        $getJenisKasusPenyakit = JenisKasusPenyakit::find()->Where(['jeniskasuspenyakit_id'=> $this->jeniskasuspenyakit_id])->one();
        $getPenyakitRuanganBefore = KasusPenyakitRuangan::find()->Where(['jeniskasuspenyakit_id'=> $this->jeniskasuspenyakit_id_before,'ruangan_id' => $this->ruangan_id_before])->one();       

        
        if (!empty($resKasusPenyakitRuangan)) {
            if($this->type_method == 'create'){
                $this->addError('ruangan_id','"'.'Ruangan : '.$getRuangan['ruangan_nama'].' dan '.'<br> Jenis Kasus Penyakit : '.$getJenisKasusPenyakit->jeniskasuspenyakit_nama.'" sudah ada.');
                return false;                
            }
            
            if($this->type_method == 'update'){
                if( ($resKasusPenyakitRuangan->jeniskasuspenyakit_id != $getPenyakitRuanganBefore->jeniskasuspenyakit_id) ||
                  ($resKasusPenyakitRuangan->ruangan_id != $getPenyakitRuanganBefore->ruangan_id) ){
                    $this->addError('ruangan_id','"'.'Ruangan : '.$getRuangan['ruangan_nama'].' dan '.'<br> Jenis Kasus Penyakit : '.$getJenisKasusPenyakit->jeniskasuspenyakit_nama.'" sudah ada.');
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
            'ruangan_id' => 'Nama Ruangan',
            'jeniskasuspenyakit_id' => 'Nama Jenis Kasus Penyakit',
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
    public function getJenisKasusPenyakit()
    {
        return $this->hasOne(JenisKasusPenyakit::className(), ['jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function extraFields()
    {
        return [
            'jeniskasuspenyakit_m' => function($item){
                return $item->jenisKasusPenyakit;
            },
            'ruangan_m' => function($item){
                return $item->ruangan;
            }
        ];
    }
}

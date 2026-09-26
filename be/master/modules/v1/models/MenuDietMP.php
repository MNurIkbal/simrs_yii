<?php

/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-11-28 16:06:30
 * @Description: model menudiet_mp
 */
namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "menudiet_mp".
 *
 * @property integer $jenisdiet_id
 * @property integer $makanandiet_id
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
 */
class MenuDietMP extends \Doco\components\DocoActiveRecord
{
    public $primary_key;
    /*public $ruangan_nama;
    public $jeniskasuspenyakit_nama;
    public $jeniskasuspenyakit_id_before;
    public $ruangan_id_before;
    public $type_method;*/
    
    public static function tableName()
    {
        return 'menudiet_mp';
    }

    public static function primaryKey()
    {
        return ['jenisdiet_id', 'makanandiet_id'];
    }

    public function rules()
    {
        return [
            [['jenisdiet_id', 'makanandiet_id'], 'required'],
            [['jenisdiet_id', 'makanandiet_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],

            [['jenisdiet_id'],'checkValidate','on'=>'create']
        ];
    }

    public function checkValidate($attribute, $params)
    {
        $jenisdiet_id = $this->jenisdiet_id;

        $query = MenuDietMP::find(true)->where([
            'jenisdiet_id' => $jenisdiet_id
        ]);
        $query->andWhere(['is_deleted' => false]);
        $resMenuDiet =  $query->all();
        
        $getJenisDiet = JenisDiet::find()->Where(['jenisdiet_id'=> $jenisdiet_id])->one();        
        if (!empty($resMenuDiet)) {
            $this->addError('jenisdiet_id','"'.'Nama Jenis Diet : '.$getJenisDiet->jenisdiet_nama.' sudah ada.');
            return false;                
            
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

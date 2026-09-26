<?php

/**
 * @Author: rizal
 * @Date:   2018-01-05 13:43:12
 * @Last Modified by:   ijal
 * @Last Modified time: 2018-01-05 13:43:22
 * @Description: model kasuspenyakitdiagnosa_mp
 */

namespace app\modules\v1\models;

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
class KasusPenyakitDiagnosa extends \app\components\DocoActiveRecord
{

    public $diagnosa_kode;
    public $jeniskasuspenyakit_nama;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kasuspenyakitdiagnosa_mp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['jeniskasuspenyakit_id', 'diagnosa_id'],
                'required'
            ],
            [
                [
                    'jeniskasuspenyakit_id', 
                    'diagnosa_id', 
                    'created_by', 
                    'modified_count', 
                    'last_modified_by', 
                    'deleted_by'
                ], 
                'integer'
            ],
            [
                ['additional_data', 'jeniskasuspenyakit_nama', 'diagnosa_kode'], 
                'string'
            ],
            [
                ['created_date', 'last_modified_date', 'deleted_date'],
                'safe'
            ],
            [
                ['is_deleted', 'is_active'],
                'boolean'
            ],
            [
                ['diagnosa_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Diagnosa::className(),
                'targetAttribute' => ['diagnosa_id' => 'diagnosa_id']
            ],
            [
                ['jeniskasuspenyakit_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Jeniskasuspenyakit::className(),
                'targetAttribute' =>[
                    'jeniskasuspenyakit_id' => 'jeniskasuspenyakit_id'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'diagnosa_id' => 'Diagnosa ID',
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

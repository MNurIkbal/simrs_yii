<?php

/**
*  @author yaya
*/
namespace SirsCore\models;

use Yii;
use Doco\features\models\PemakaianObatDetail;

class PemakaianObat extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pemakaianobat_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawai_id', 'ruangan_id', 'tglpemakaianobat', 'nopemakaian_obat', 'untukkeperluan_obat'], 'required'],
            [[
                'tglpemakaianobat', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'created_by', 
                'modified_count',
                'last_modified_by',
                'deleted_by',
                'keterangan_pemakaianobat'
            ], 'safe'],
            [['keterangan_pemakaianobat', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nopemakaian_obat'], 'string', 'max' => 20],
            [['untukkeperluan_obat'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemakaianobat_id' => 'Pemakaianobat ID',
            'pegawai_id' => 'Pegawai ID',
            'ruangan_id' => 'Ruangan ID',
            'tglpemakaianobat' => 'Tglpemakaianobat',
            'nopemakaian_obat' => 'Nopemakaian Obat',
            'untukkeperluan_obat' => 'Untukkeperluan Obat',
            'keterangan_pemakaianobat' => 'Keterangan Pemakaianobat',
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

    public function getDetail()
    {
        return $this->hasMany(PemakaianObatDetail::className(),['pemakaianobat_id' => 'pemakaianobat_id']);
    }
}
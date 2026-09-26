<?php

namespace app\modules\v1\models;

use Yii;

class HistoriPelayanan extends \Doco\components\DocoActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'historipelayanan_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'tindakanpelayanan_id',
                'pendaftaran_id',
                'penjamin_id',
            ], 'required'],
            [[
                'tindakanpelayanan_id', 
                'pendaftaran_id', 
                'penjamin_id', 
                'daftartindakan_id', 
                'tipepaket_id', 
                'created_by', 
                'modified_count', 
                'last_modified_by', 
                'deleted_by'
            ], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tindakanpelayanan_id' => Yii::t('app', 'Tindakan Pelayanan'),
            'pendaftaran_id' => Yii::t('app', 'Pendaftaran Id'),
            'pasienadmisi_id' => Yii::t('app', 'Pasien Admisi Id'),
            'penjamin_id' => Yii::t('app', 'Penjamin'),
            'daftartindakan_id' => Yii::t('app', 'Daftar Tindakan'),
            'tipepaket_id' => Yii::t('app', 'Tipe Paket'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
        ];
    }


}

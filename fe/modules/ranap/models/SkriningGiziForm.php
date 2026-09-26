<?php

namespace app\modules\ranap\models;

use Yii;

class SkriningGiziForm extends \yii\base\Model
{
    public $bb_ygdirencanakan;
    public $bb_turun;
    public $porsi_makan;
    public $sakit_berat;
    public $skor;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bb_ygdirencanakan','bb_turun','porsi_makan','sakit_berat','skor'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
        	'bb_ygdirencanakan' => 'Penurunan berat badan yang tidak direncanakan',
        	'bb_turun' => 'bb_turun',
        	'porsi_makan' => 'Hanya mampu menghabiskan 1/4 porsi makanan',
        	'sakit_berat' => 'Menderita Sakit Berat',
        	'skor' => 'Total Skor',
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
}
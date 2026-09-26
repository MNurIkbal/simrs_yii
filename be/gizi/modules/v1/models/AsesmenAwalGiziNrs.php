<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-22 16:27:36
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-22 16:29:30
 * @Description:
 */

namespace app\modules\v1\models;

use Yii;

class AsesmenAwalGiziNrs extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'asesmenawalgizinrs_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id'], 'required'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['tgl_asesmen', 'pasienadmisi_id', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_imt', 'is_berat_badan', 'is_asupan_makan', 'is_penyakit_berat', 'is_deleted', 'is_active', 'is_anak'], 'boolean'],
        ];
    }
}

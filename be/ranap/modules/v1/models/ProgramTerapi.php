<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "programterapi_t".
 *
 *
 * @property integer $programterapi_id
 * @property integer $pendaftaran_id
 * @property integer $pasienmasukpenunjang_id
 * @property integer $pasien_id
 * @property string $tgl_permintaan
 * @property string $diagnosa
 * @property string $frekuensi
 * @property string $catatan
 * @property integer $dokterperujuk_id
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
 */
class ProgramTerapi extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'programterapi_t';
    }



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_dateprogramterapi_id','pendaftaran_id','pasienmasukpenunjang_id','pasien_id','tgl_permintaan','diagnosa','frekuensi','catatan','dokterperujuk_id','tipe_instalasi','additional_data','created_date','created_by','modified_count','last_modified_date','last_modified_by','is_deleted','is_active','deleted_date','deleted_by'], 'safe'],
        ];
    }   
}

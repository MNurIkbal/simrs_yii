<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dokumenupload_t".
 *
 * @property int $dokumenupload_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $dokumen_id
 * @property string $filename
 * @property string $path
 *
 */
class DokumenUpload extends \Doco\components\DocoActiveRecord
{
   
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dokumenupload_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pendaftaran_id','pasienadmisi_id','dokumen_id','filename','created_date', 'last_modified_date', 'deleted_date','path','pasien_id','ruangan_id','dokter_id','doc_date', 'nama_dokumen_freetext','is_eklaim'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

}

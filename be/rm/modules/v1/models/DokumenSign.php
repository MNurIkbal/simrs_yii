<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dokumensign_t".
 *
 * @property int $dokumen_sign_id
 * @property int $sign_provider_id
 * @property string $type
 * @property int $transaksi_id
 * @property string $filename
 * @property string $path
 * @property date $signed_date
 * @property int $pegawai_id
 * @property date $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property date $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property date $deleted_date
 * @property int $deleted_by
 * @property int $pendaftaran_id
 * @property string $additional_data
 * @property string $doc_status
 *
 */
class DokumenSign extends \Doco\components\DocoActiveRecord
{
   
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dokumensign_t';
    }

    public function rules()
    {
        return [
            [['pendaftaran_id','transaksi_id','pegawai_id','created_by','modified_count','last_modified_by','deleted_by',], 'integer'],
        	[['pendaftaran_id','type','transaksi_id','filename', 'pegawai_id', 'doc_status'], 'required'],
        	[['sign_provider_id','path','signed_date','additional_data','konfig_dokumen_id','created_date','created_by','modified_count','last_modified_date','last_modified_by','is_deleted','is_active','deleted_date','deleted_by',], 'safe'],
        ];
    }
}

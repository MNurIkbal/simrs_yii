<?php

namespace app\modules\v1\models;

use Yii;

/**
* This is the model class for table "pendaftaranpenjamin_t".
*
* @property int pendaftaranpenjamin_id
* @property int pendaftaran_id
* @property date tgl_pendaftaranpenjamin
* @property int carabayar_id
* @property int penjamin_id
* @property string penjamin_nama
* @property int pasien_id
* @property string nama_pasien
* @property string nokartuasuransi
* @property int nominal_dijamin
* @property string additional_data
* @property date created_date
* @property int created_by
* @property int modified_count
* @property date last_modified_date
* @property int last_modified_by
* @property bool is_deleted
* @property bool is_active
* @property date deleted_date
* @property int deleted_by
* @property string asuransipasien_id
* @property string namapemilikasuransi
* @property string alasan_batal
*/
class PendaftaranPenjamin extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public $primaryKey = "pendaftaranpenjamin_id";

    public static function tableName()
    {
        return 'pendaftaranpenjamin_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'penjamin_id', 'carabayar_id', 'nominal_dijamin', 'nokartuasuransi'], 'required'],
            [['penjamin_id'], 'checkUnique'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['carabayar_id','penjamin_id','pasien_id','asuransipasien_id','nominal_dijamin','created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['tgl_pendaftaranpenjamin','penjamin_nama','nama_pasien','nokartuasuransi','namapemilikasuransi','alasan_batal'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            "pendaftaranpenjamin_id" => "Pendaftaran Penjamin ID",
            "pendaftaran_id" => "Pendaftaran ID",
            "tgl_pendaftaranpenjamin" => "Tanggal Pendaftaran Penjamin",
            "carabayar_id" => "Cara Bayar ID",
            "penjamin_id" => "Penjamin ID",
            "penjamin_nama" => "Penjamin",
            "pasien_id" => "Pasien ID",
            "nama_pasien" => "Nama Pasien",
            "nokartuasuransi" => "No Kartu",
            "nominal_dijamin" => "Nominal Dijamin",
            "additional_data" => "Additional Data",
            "created_date" => "Created Date",
            "created_by" => "Created By",
            "modified_count" => "Modified Count",
            "last_modified_date" => "Last Modified Date",
            "last_modified_by" => "Last Modified By",
            "is_deleted" => "Is Deleted",
            "is_active" => "Is Active",
            "deleted_date" => "Deleted Date",
            "deleted_by" => "Deleted By",
            "asuransipasien_id" => "Asuransi Pasien ID",
            "namapemilikasuransi" => "Nama Pemiliki Asuransi",
            "alasan_batal" => "Alasan Batal",
        ];
    }
}

<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kontrakpenjamin_v".
 *
 * @property integer $kontrakpenjamin_id
 * @property integer $penjamin_id
 * @property string $penjamin_nama
 * @property string $no_kontrak
 * @property string $nama_kontrak
 * @property string $tgl_mulai
 * @property string $tgl_selesai
 * @property bool $is_active
 */
class KontrakPenjamin extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'no_kontrak',
        'nama_kontrak',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kontrakpenjamin_m';
    }

    /**
     * @inheritdoc
     */

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kontrakpenjamin_id' => 'Kontrak Penjamin',
            'penjamin_id' => 'Penjamin',
            'no_kontrak' => 'Nomor Kontrak',
            'nama_kontrak' => 'Nama Kontrak',
            'tgl_mulai' => 'Tanggal Mulai',
            'tgl_selesai' => 'Tanggal Selesai',
            'is_active' => 'Aktif'
        ];
    }

    public function rules()
    {
        return [
            [['penjamin_id','no_kontrak', 'nama_kontrak','tgl_mulai','tgl_selesai'], 'required'],
            [['additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date','deleted_by'], 'default', 'value' => null],
            [['no_kontrak','nama_kontrak'], 'string', 'max' => 225],
            [['created_date', 'deleted_date','penjamin_id','no_kontrak', 'nama_kontrak','tgl_mulai','tgl_selesai'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['penjamin_id'], 'integer'],
            [['tgl_mulai', 'tgl_selesai'], 'date','format' => 'php:Y-m-d'],
        ];
    }

    public static function primaryKey()
    {
        return ['kontrakpenjamin_id'];
    }

    // public function getLookup(){
    //     return $this->hasMany(Lookup::className(),['lookup_id' = 'lookup_id'])
    // }
    
    // public function getPendaftaran()
    // {
        // return $this->hasOne(Pendaftaran::className(), ['pendaftaran_id' => 'pendaftaran_id']);
    // }
    
    // public function getPembayaranPelayanan()
    // {
        // return $this->hasOne(PembayaranPelayanan::className(), ['pembayaranpelayanan_id' => 'pembayaranpelayanan_id']);
    // }
    
    // public function getTandaBuktiBayar()
    // {
        // return $this->hasOne(TandaBuktiBayar::className(), ['tandabuktibayar_id' => 'tandabuktibayar_id']);
    // }
    
    // public function getReturBayarPelayanan()
    // {
        // return $this->hasOne(ReturBayarPelayanan::className(), ['returbayarpelayanan_id' => 'returbayarpelayanan_id']);
    // }
    
    // public function extraFields()
    // {
        // return [
            // 'pendaftaran_t' => function($item){
                // return $item->pendaftaran;
            // },
            // 'pembayaranpelayanan_t' => function($item){
                // return $item->pembayaranPelayanan;
            // },
            // 'tandabuktibayar_t' => function($item){
                // return $item->tandaBuktiBayar;
            // },
            // 'bank_m' => function($item){
                // return @$item->tandaBuktiBayar->bank;
            // },
            // 'returbayarpelayanan_t' => function($item){
                // return $item->returBayarPelayanan;
            // },
            // 'tandabuktikeluar_t' => function($item){
                // return @$item->returBayarPelayanan->tandaBuktiKeluar;
            // }
        // ];
    // }
}

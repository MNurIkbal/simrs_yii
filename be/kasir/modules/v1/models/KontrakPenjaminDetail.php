<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "kontrakpenjamindetail_v".
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
class KontrakPenjaminDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kontrakpenjamindetail_m';
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
            'kontrakpenjamindetail_id' => 'Kontrak Penjamin Detail ID',
            'kontrakpenjamin_id' => 'Kontrak Penjamin',
            'penjamingrade_id' => 'Grade Penjamin ID',
            'grade' => 'Grade',
            'lob_id' => 'Line of Business ID',
            'tipediskon_id' => 'Tipe Diskon ID',
            'is_active' => 'Aktif'
        ];
    }

    public function rules()
    {
        return [
            [['kontrakpenjamin_id','grade', 'lob_id','tipediskon_id', 'penjamingrade_id'], 'required'],
            [['additional_data', 'created_by', 'modified_count', 'last_modified_by', 'deleted_date','deleted_by'], 'default', 'value' => null],
            [['grade'], 'string', 'max' => 255],
            [['created_date', 'deleted_date','grade', 'lob_id','tipediskon_id','kontrakpenjamin_id'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            // [['no_kontrak', 'nama_kontrak'], 'string', 'max' => 25],
        ];
    }



    public static function primaryKey()
    {
        return ['kontrakpenjamindetail_id'];
    }
    
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

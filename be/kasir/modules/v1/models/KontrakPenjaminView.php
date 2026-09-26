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
class KontrakPenjaminView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kontrakpenjamin_v';
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
            'penjamin_nama' => 'Nama Penjamin',
            'no_kontrak' => 'Nomor Kontrak',
            'nama_kontrak' => 'Nama Kontrak',
            'tgl_mulai' => 'Tanggal Mulai',
            'tgl_selesai' => 'Tanggal Selesai',
            'is_active' => 'Aktif'
        ];
    }

    public static function primaryKey()
    {
        return ['kontrakpenjamin_id'];
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

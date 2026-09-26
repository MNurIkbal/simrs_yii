<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

class LogEditTagihan extends \Integrasi\Components\ActiveRepositories
{
   public static function tableName()
   {
      return 'logedittagihan_r';
   }

   public function rules()
   {
      return [
         [[
            'pendaftaran_id',
            'pasienmasukpenunjang_id',
            'kelompok',
            'status', 
            'tipe_pasien', 
            'checkPenjamin',
            'cyto', 
            'defaultPenjamin',
            'dijamin',
            'harga', 
            'instalasi', 
            'isPenjamin',
            'is_obat', 
            'kelompoktindakan_nama', 
            'keterangan', 
            'nominal_diskon', 
            'penjamin',
            'persen_diskon', 
            'qty',
            'subtotal',
            'subtotal_origin', 
            'tanggal', 
            'tindakan', 
            'tindakan_obat_id', 
            'totalDibayar', 
            'value', 
            'penyulit', 
            'pelayanan_id', 
            'harga_origin', 
            'cyto_origin', 
            'penyulit_origin',
            'id', 
            'subPenjamin', 
            'dijamin_subpayer',
            'plafon_payer' ,
            'plafon_subpayer',
            'created_date', 'last_modified_date', 'deleted_date', 'deleted_by', 'last_modified_by',
            'is_deleted', 'is_active', 'additional_data', 'created_by', 'modified_count'
         ],'safe'],
         [['penyulit_origin', 'cyto_origin', 'harga_origin'], 'default', 'value'=> 0],
         [['pasienmasukpenunjang_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value'=> null],
         [['pasienmasukpenunjang_id', 'pendaftaran_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
         [['additional_data'], 'string'],
         [['is_deleted', 'is_active'], 'boolean'],
     ];
   }
}

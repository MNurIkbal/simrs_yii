<?php 

/**
 * ? @author Budi <budi@sirs.co.id>
 * ! @copyright Sirs
 */

namespace Doco\models\kasir;

use yii\base\Model;

class DetailPasienInvoice extends Model {
   
   public $alamat_pasien;
   public $propinsi_nama;
   public $kabupaten_nama;
   public $kecamatan_nama;
   public $kelurahan_nama;
   public $warganegara;

   /**
    * @return rules array default values
    */
   public function rules()
   {
      return [
         [[
            'alamat_pasien',
            'propinsi_nama',
            'kabupaten_nama',
            'kecamatan_nama',
            'kelurahan_nama',
            'warganegara',
         ], 'default', 'value' => null],
         [[
            'alamat_pasien',
            'propinsi_nama',
            'kabupaten_nama',
            'kecamatan_nama',
            'kelurahan_nama',
            'warganegara',
         ], 'safe'],
      ];
   }
}
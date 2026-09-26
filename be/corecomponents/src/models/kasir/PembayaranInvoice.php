<?php 

/**
 * ? @author Budi <budi@sirs.co.id>
 * ! @copyright Sirs
 */

namespace Doco\models\kasir;

use yii\base\Model;

class PembayaranInvoice extends Model {
   
   public $tgl_pembayaran;
   public $no_pembayaran;
   public $total_tagihan;
   public $total_administrasi;
   public $discount;
   public $penggunaan_uangmuka;
   public $total_dijamin;
   public $total_sisatagihan;
   public $total_ditagihkan;
   public $patient_amount;
   public $total_tunai;
   public $total_nontunai;
   public $total_kembalian;
   public $additional_data;
   public $total_pembulatan;
   public $nobuktibayar;
   public $no_invoice;
   public $no_invoicepasien;
   public $pembulatan;

   /**
    * @return rules array default values
    */
   public function rules()
   {
      return [
         [[
            'total_tagihan',
            'total_administrasi',
            'discount',
            'penggunaan_uangmuka',
            'total_dijamin',
            'total_sisatagihan',
            'total_ditagihkan',
            'patient_amount',
            'total_tunai',
            'total_nontunai',
            'total_kembalian',
            'total_pembulatan',
            'pembulatan',
         ], 'default', 'value' => 0],
         [[
            'tgl_pembayaran',
            'no_pembayaran',
            'additional_data',
            'nobuktibayar',
            'no_invoice',
            'no_invoicepasien'
         ], 'default', 'value' => null],
         [[
            'tgl_pembayaran',
            'no_pembayaran',
            'total_tagihan',
            'total_administrasi',
            'discount',
            'penggunaan_uangmuka',
            'total_dijamin',
            'total_sisatagihan',
            'total_ditagihkan',
            'patient_amount',
            'total_tunai',
            'total_nontunai',
            'total_kembalian',
            'additional_data',
            'total_pembulatan',
            'nobuktibayar',
            'no_invoice',
            'no_invoicepasien',
            'pembulatan',
         ], 'safe'],
      ];
   }
}
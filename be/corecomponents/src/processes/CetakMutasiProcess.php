<?php 

namespace Doco\processes; 

use Yii;
use Doco\components\DocoPrint;
use app\modules\v1\models\InfoMutasiBarangView;
use app\modules\v1\models\InfoMutasiBarangDetailView;

class CetakMutasiProcess extends \Doco\components\DocoBaseProcessExtension {
  protected function processFlow()
  {
    $id = Yii::$app->request->get('id');

    $print = new DocoPrint;

    $m = new InfoMutasiBarangView;
    $m_d = new InfoMutasiBarangDetailView;

    $where = [
        "mutasibarang_id" => $id
    ];

    $tgl_cetak = date("dmY H:i:s");

    $mutasi_barang = $m->find()->where($where)->one();

    if (!empty($mutasi_barang))
    {
        $mutasi_barang_detail = $m_d->find()
            ->where($where)
            ->orderBy([
                "barang_nama" => SORT_ASC
            ])
            ->all();

        if (!empty($mutasi_barang_detail))
        {
            $print_attributes = [
                "#no_pemesanan#"       => $mutasi_barang["no_pemesanan"],
                "#no_mutasi#"          => $mutasi_barang["nomutasi_barang"],
                "#tgl_cetak#"          => $tgl_cetak,
                "#tgl_dikirim#"        => isset($mutasi_barang["tgl_mutasibarang"]) ? date("d-M-Y", strtotime($mutasi_barang["tgl_mutasibarang"])) : "-" ,
                "#ruangan_asal#"       => $mutasi_barang["ruangan_asal"],
                "#ruangan_tujuan#"     => $mutasi_barang["ruangan_nama"],
                "#pegawai_mutasi#"     => isset($mutasi_barang["pegawai_mutasi"]) ? $mutasi_barang["pegawai_mutasi"] : "-" ,
                "#pegawai_mengetahui#" => isset($mutasi_barang["pegawai_mengetahui"]) ? $mutasi_barang["pegawai_mengetahui"] : "-" ,
                "#detail_mutasi#"      => Yii::$app->controller->renderPartial("table-mutasi", ["detail_mutasi"=> $mutasi_barang_detail]),
            ];
            $print->attributes = $print_attributes;
            $print->Output();
        }else
        {
            return [
                "status" => 402,
                "text" => "No. Retur tidak ditemukan"
            ];
        }
    }else{
        return [
            "status" => 402,
            "text" => "No. Retur tidak ditemukan"
        ];
    }
  }
}

?>
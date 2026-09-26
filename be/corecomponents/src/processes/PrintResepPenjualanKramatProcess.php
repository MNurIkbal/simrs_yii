<?php

/**
 * @author : Aris Munandar (aris.munandar@docotel.com)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\exceptions\ValidationException;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\InfoResepDetail1View;
use app\modules\v1\models\RiwayatAlergiView;

class PrintResepPenjualanKramatProcess extends \Doco\processes\PrintResepPenjualanProcess
{
    protected function printResep()
    {
        try {
            $request = Yii::$app->request;
            $reseptur_id = $request->get('id');
            $noresep = $request->get('noresep');
            $nomor = $request->get('nomor');
            // $result = $this->getReseptur();
            $getHeader = $this->getResepHeader($nomor);

            if($getHeader['status_reseptur_id'] == DocoConstants::VAR_B_R) {
                $modelInfoResepturDetailView = new InfoResepDetail1View;
            } else {
                $modelInfoResepturDetailView = new InfoResepDetailView;
            }


            $InfoResepturDetailView = $modelInfoResepturDetailView::find(true)->where([
                'noresep'       => $noresep
            ])->orderBy([
                'racikan_id'    => SORT_ASC,
                'rke'           => SORT_ASC
            ]);
            $resDataObat = $InfoResepturDetailView->asArray()->all();

            //Aris ToDo
            $alergi = RiwayatAlergiView::find()->where([
                'pasien_id' => $getHeader['pasien_id']
            ])->all();

            $no = 1;
            $string = '<ul>';
            foreach ($alergi as $key => $value) {
                $valAlergi = $value['riwayat_alergi'];
                $replace = str_replace("-",",",$valAlergi);
                $replace = preg_replace('/["\[\]]/i', "", $replace);
                $list = explode(",",$replace);
                if (is_array($list)) {
                    foreach ($list as $val) {
                        if($val != ""){
                            $string .= '<li> '. $no .'. '. $val .'</li>';
                            $no++;
                        }
                    }
                }
            }
            $string .= '</ul>';
            //Aris ToDo

            $data_detail = [];
            $data_obat = [];
            $totalharga = 0;
            foreach ($resDataObat as $key => $value) {
                $jenisRacikan = ($value['racikan_id'] == 1) ? Yii::t('app', 'Racikan') : Yii::t('app', 'Non Racikan');
                $newData['jenis_racikan'] = $jenisRacikan;
                $newData['rke'] = ($value['rke'] == 0 || empty($value['rke']) ) ? '-' : $value['rke'] ;
                $newData['obatalkes_nama'] = $value['obatalkes_nama'];
                $newData['hargajual_oa'] = DocoHelpers::formatNumber($value['hargajual_satuan']);
                // $subtotal = ($value['hargajual_satuan'])*$value['qty_reseptur'];
                $subtotal = $value['totalharga_jual'];
                $newData['signa_nama'] = !empty($value['signa_nama']) ? $value['signa_nama'] : '-';
                $newData['qty'] = isset($value['det']) ? $value['det'] : $value['qty_oa'];
                $newData['satuan_input'] = $value['satuan_input'];
                $newData['etiket'] = $value['etiket'];
                $newData['sub_total'] = $subtotal;
                $totalharga += $subtotal;
                $data_obat[] = $newData;
            }

            $biayaadmin = $getHeader['biayaadministrasi'];
            $result = [
                'detail'        => $data_detail,
                'data_obat'     => $data_obat,
                'subtotal'      => DocoHelpers::formatNumber($totalharga),
                'biayaadmin'    => DocoHelpers::formatNumber($biayaadmin),
                'total'         => DocoHelpers::formatNumber($totalharga + $biayaadmin),
                'getHeader'     => $getHeader
            ];

            $tanggal = !empty($getHeader['tgl_resep_dibuat']) ? $getHeader['tgl_resep_dibuat'] : '-';
            $tanggal_cetak = date("d M Y H:i");
            if(!empty($getHeader['tgl_resep_dibuat'])) {
                $tanggal = date("d M Y H:i", strtotime($tanggal));
            }
            $nama = !empty($getHeader['no_rekam_medik']) ? $getHeader['nama']." / ".$getHeader['no_rekam_medik'] : $getHeader['nama'];
            $print = new DocoPrint();
            $print->attributes = [
                '#dataTable#' => Yii::$app->controller->renderPartial('index_kramat',[
                    'data_obat'=>$data_obat,
                    'subtotal'      => DocoHelpers::formatNumber($totalharga),
                    'biayaadmin'    => DocoHelpers::formatNumber($biayaadmin),
                    'total'         => DocoHelpers::formatNumber($totalharga + $biayaadmin),
                ]),
                '#tanggal#' => $tanggal,
                '#tanggal_cetak#' => $tanggal_cetak,
                '#noresep#' => !empty($getHeader['nomor']) ? $getHeader['nomor'] : '-',
                '#no_pendaftaran#' => !empty($getHeader['no_pendaftaran']) ? $getHeader['no_pendaftaran'] : '-',
                '#nama_pasien#' => $nama,
                '#dokter_resep#' => !empty($getHeader['nama_pegawai']) ? $getHeader['nama_pegawai'] : '-',
                '#penjamin#' => !empty($getHeader['penjamin_nama']) ? $getHeader['carabayar_nama'].' - '.$getHeader['penjamin_nama'] : '-',
                '#catatan#' => !empty($getHeader['catatan']) ? $getHeader['catatan'] : '-'
            ];
            // return[$data_alergi];
            $print->Output();

        }catch (\Yii\db\Exception $e) {
             throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }

    }
}

<?php

/**
 * @author : Ardi Pratama (ardi.pratama@sirs.co.id)
 * Powered by Sirs
 */

namespace Extensions\reseptur;

use app\modules\v1\models\PegawaiMasterView;
use app\modules\v1\models\ProfilRumahSakit;
use Doco\components\DocoConstansId;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;

use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\components\ApotekComponent;

use app\modules\v1\models\Reseptur;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\Loket;
use app\modules\v1\models\InfoPenjualanResepDetailView;
use app\modules\v1\models\InfoResepView;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\InfoResepDetail1View;
use app\modules\v1\models\PenjualanResep;
use app\modules\v1\models\PembatalanResep;
use app\modules\v1\models\ObatAlkesPasien;

use app\modules\v1\models\RiwayatAlergiView;
use app\modules\v1\models\StokObatAlkes;
use app\modules\v1\repositories\ResepRepository;
use Doco\Notifications\FarmasiNotification;
use app\modules\v1\models\ResepturRacikan;
use Doco\components\DocoBaconQrCode;

class CetakResepMayapada extends \Doco\processes\CetakResepProcess
{
	public function cetakResep(){
        try {
            $request = Yii::$app->request;
            $reseptur_id = $request->get('id');
            
            $nomor = $request->get('nomor');
            $type = $request->get('type', 'resep');
            $pegawai_print = $request->get('nama_pegawai');
            $getHeader = InfoResepView::find()->where([
                            'nomor' => $nomor
                        ])->asArray()->one();
                        
            if($getHeader['status_reseptur_id'] == DocoConstants::VAR_B_R) {
                $modelInfoResepturDetailView = new InfoResepDetail1View;
            } else {
                $modelInfoResepturDetailView = new InfoResepDetailView;
            }
            $InfoResepturDetailView = $modelInfoResepturDetailView::find(true)
                                    ->where(['noresep' => $nomor])
                                    ->orderBy([
                                        'racikan_id'    => SORT_ASC,
                                        'rke'           => SORT_ASC
                                    ]);
            $temp = $InfoResepturDetailView->createCommand()->sql;
            $resDataObat = $InfoResepturDetailView->asArray()->all();
            $alergi = RiwayatAlergiView::find()->where([
                'pasien_id' => $getHeader['pasien_id']
            ])->all();

            $data_detail = [];
            $data_obat = [];
            $list_obat = [];
            $totalharga = 0;
            foreach ($resDataObat as $key => $value) {
                $det = is_null($value['det_transaksi']) ? $value['qty_transaksi'] : $value['det_transaksi'];
                $jenisRacikan = ($value['racikan_id'] == 1) ? Yii::t('app', 'Racikan') : Yii::t('app', 'Non Racikan');
                $newData['jenis_racikan'] = $jenisRacikan;
                $newData['rke'] = ($value['rke'] == 0 || empty($value['rke']) ) ? '-' : $value['rke'] ;
                $newData['obatalkes_nama'] = $value['obatalkes_nama'];
                $newData['hargajual_oa'] = DocoHelpers::formatNumber($value['hargajual_satuan']);
                $subtotal = ($value['hargajual_satuan'])*$value['qty_oa'];
                $newData['signa_nama'] = !empty($value['signa_nama']) ? $value['signa_nama'] : '-';
                $newData['qty'] = $value['qty_oa'];
                $newData['satuan_input'] = $value['satuan_input'];
                $newData['etiket'] = $value['etiket'];
                $newData['qty_transaksi'] = $value['qty_transaksi'];
                $newData['racikan_obat'] = $value['racikan_id'];
                $newData['det'] = $det;
                $newData['sub_total'] = $subtotal;
                $totalharga += $subtotal;
                $data_obat[] = $newData;
            }
            $no = 0;
            $prev_rke = null;
            $total_racikan = 0.0;
            $prev_sub = 0;
            $firstkey = null;
            foreach($data_obat as $key => $value){
                if($value['jenis_racikan']== DocoConstants::IS_RACIKAN ){
                    $rke = $value['rke'];
                    if($rke != $prev_rke){
                        $no++;
                        $firstkey = $key;
                        $data_obat[$key]['no'] = $no;
                        $data_obat[$key]['show'] = true;
                        $total_racikan = $value['sub_total'];
                    }else{
                        $data_obat[$key]['no'] = "";
                        $total_racikan = $total_racikan + $value['sub_total'];
                        $data_obat[$key]['show'] = false;
                        $data_obat[$firstkey]['sub_total_racikan'] = $total_racikan;
                    }
                    $prev_rke = $rke;
                } else {
                    $no++;
                    $data_obat[$key]['no'] = $no;
                    $data_obat[$key]['show'] = true;
                }
            }

            if(count($data_obat)>0) usort($data_obat, function($a,$b){
                if($a['rke'] == $b['rke']) return 0;
                return ($a['rke'] > $b['rke']) ? 1 : -1;
            });

            $nama_jabatan = [
                DocoConstants::DIREKTUR,
                DocoConstants::HEAD_OF_PURCHASING,
                DocoConstants::HEAD_OF_APOTEKER,
                DocoConstants::HEAD_OF_FINANCE
            ];

            $jabatan = $this->getIdJabatan($nama_jabatan);
            $pegawai = $this->getPegawaiByJabatan($jabatan);
            $sign_index = $this->getSignIndex($pegawai, $jabatan);

            $verificator = DocoConstants::HEAD_OF_APOTEKER;
            $no_sipa = is_int($sign_index[$verificator]) ? "SIP . " . $pegawai[$sign_index[$verificator]]['suratizinpraktek'] : "";
            $profil_rs = ProfilRumahSakit::find()->select(['nama_rumahsakit', 'alamatlokasi_rumahsakit', 'no_telp_profilrs'])->where(['profilrs_id' => 1])->one();
            $apoteker = is_int($sign_index[$verificator]) ? $this->capwords($pegawai[$sign_index[$verificator]]['nama_pegawai']) : "";

            $tanggal = !empty($getHeader['tgl_resep_dibuat']) ? date("d M Y H:i", strtotime($getHeader['tgl_resep_dibuat'])) : '-';
            $tgl_lahir = !empty($getHeader['tanggal_lahir']) ? date("d M Y", strtotime($getHeader['tanggal_lahir'])) : '-';

            $nama = !empty($getHeader['no_rekam_medik']) ? $getHeader['no_rekam_medik']." / ".$getHeader['nama'] : $getHeader['nama'];
            $racikan = array_filter($data_obat,function($val){ return $val['racikan_obat'] == 1; });
            $non_racikan = array_filter($data_obat,function($val){ return $val['racikan_obat'] == 2; });
            $tmp_racikan = [];
            foreach($racikan as $key => $row){
                $rke = !empty($row['rke']) ? $row['rke'] : 0;
                $tmp_racikan[$rke][] = $row;
            }

            $data_obat['racikan'] = $tmp_racikan;
            $data_obat['non_racikan'] = $non_racikan;
            $title = ($type == "resep") ? "Resep" : "Copy Resep";
            // Yii::error(["of" => $data_obat['racikan']]); return false;
            $additional_data = !empty($getHeader['additional_data']) ? json_decode($getHeader['additional_data'], true) : [];
            $pegawai_approve = !empty($getHeader['pegawai_approve']) ? $getHeader['pegawai_approve'] : null;
            $status_worklist = [];
            if (!empty($additional_data['log_status'])){
                foreach($additional_data['log_status'] as $key => $val){
                    $status_worklist_id = !empty($val['status_worklist_id']) ? $val['status_worklist_id'] : 0;
                    $status_worklist[$status_worklist_id]=$val;
                }
            }
            
            $print = new DocoPrint();

            $styleQrCode = ['style_height' => '50px','style_width' => '50px'];
            $qrCode = DocoBaconQrCode::renderQrCode($nomor, $styleQrCode);
            
            $print->attributes = [
                '#dataTable#' => Yii::$app->controller->renderPartial('resep_obat',[
                    'data_obat'=>$data_obat,
                    'getHeader'=>$getHeader,
                    'no_sipa'=>$no_sipa,
                    'profil_rs'=>$profil_rs,
                    'apoteker'=>$apoteker,
                    'type'=>$type,
                    'status_worklist'=>$status_worklist,
                    'pegawai_print' => $pegawai_print,
                    'pegawai_approve' => $pegawai_approve
                ]),
                '#tanggal#' => $tanggal,
                '#noresep#' => !empty($getHeader['nomor']) ? $getHeader['nomor'] : '-',
                '#dokter_resep#' => !empty($getHeader['nama_pegawai']) ? $getHeader['nama_pegawai'] : '-',
                '#no_pendaftaran#' => !empty($getHeader['no_pendaftaran']) ? $getHeader['no_pendaftaran'] : '-',
                '#nama_pasien#' => !empty($getHeader['nama']) ? $getHeader['nama'] : '-',
                '#no_rekam_medik#' => !empty($getHeader['no_rekam_medik']) ? $getHeader['no_rekam_medik'] : '-',
                '#jenis_kelamin#' => !empty($getHeader['jenis_kelamin']) ? $getHeader['jenis_kelamin'] : '-',
                '#tgl_lahir#' => $tgl_lahir,
                '#berat_badan#' => !empty($getHeader['berat_badan']) ? $getHeader['berat_badan'] : '',
                '#tinggi_badan#' => !empty($getHeader['tinggi_badan']) ? $getHeader['tinggi_badan'] : '',
                '#poli#' => !empty($getHeader['ruangan_tujuan']) ? $getHeader['ruangan_tujuan'] : '-',
                '#cara_bayar#' => !empty($getHeader['carabayar_nama']) ? $getHeader['carabayar_nama'] : '-',
                '#title#' => $title,
                '#nama_rs#' => !empty($profil_rs->nama_rumahsakit) ? $profil_rs->nama_rumahsakit : "Rumah Sakit",
                '#alamat#' => !empty($profil_rs->alamatlokasi_rumahsakit) ? $profil_rs->alamatlokasi_rumahsakit : "-",
                '#hotline#' => !empty($profil_rs->no_telp_profilrs) ? $profil_rs->no_telp_profilrs : "-",
                '#apoteker#' => ($type == "resep") ? "" : $apoteker,
                '#no_sipa#' => ($type == "resep") ? "" : $no_sipa,
                '#qr_code#' => $qrCode,
            ];

            $print->Output();
        }catch (\Yii\db\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e){
            Yii::error(["message" => $e->getMessage()]); 
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
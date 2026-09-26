<?php
//Author: Ardi Pratama

namespace app\modules\igd\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\data\ArrayDataProvider;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

// Using model
use app\modules\igd\models\ImplementasiForm;
use app\modules\igd\models\ImplementasiTindakanForm;
use app\modules\igd\models\ImplementasiBmhpForm;

// Trait
trait ImplementasiTrait 
{
    public function actionImplementasi()
    {
        try{
            $params = Yii::$app->request;
            $decryptedPendaftaran_id = $params->get('id','MA');
            $pendaftaran_id = DocoHelpers::decrypt($decryptedPendaftaran_id);
            $linkcetak = Url::to(['cetak-implementasi-pdf', 'id'=>$decryptedPendaftaran_id]);
            $data = [];
            return $this->renderAjax('implementasi/index',[
                'data' => $data,
                'linkcetak' => $linkcetak
            ]);
        } catch(RequestException $e){
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch(\Exception $e){
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionImplementasiGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $params = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($params->get());
            $draw = $params->get('draw', 1);
            $data = [];
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id'));

            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;
            
            $request = $this->_restIgd->get('implementasi-igd/index?pendaftaran_id='.$pendaftaran_id.'&pegawai_id='.$this->_pegawai_id.'&ruangan_id='.$this->_ruangan_id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $response = json_decode($request->getBody(), true);
            $no = $params->get('start', 1);


            foreach ($response['response']["data"] as $key => $value) {
                $no++;
                $value['primary'] = DocoHelpers::encrypt($value['instruksi_id']);
                $value['rowNum'] = $no;
                $value['dataNumber']['rowNum'] = $no;
                $value['dataNumber']['pendaftaran_id'] = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['dataNumber']['pasien_id'] = DocoHelpers::encrypt($this->_pasien_id);
                $value['dataNumber']['instruksi_id'] = DocoHelpers::encrypt($value['instruksi_id']);
                $value['dataNumber']['cppt_id'] = DocoHelpers::encrypt($value['cppt_id']);
                $value['dataNumber']['grouping_tipe'] = $value['grouping_tipe'];
                $value['list_instruksi'] = $this->getInstruksiDokter($value['data_instruksi']);
                $value['list_instruksi_status'] = $this->getInstruksiStatusImplementasi($value['data_instruksi']);
                $value['list_instruksi_dokter'] = $this->getInstruksiNamaDokter($value['data_instruksi']);
                $value['instruksi_implemented'] = $this->getStatusImplementasi($value['data_instruksi']);
                $value['is_batal_penunjang'] = $this->getStatusBatalPenunjang($value['data_instruksi']);

                $value['dokter_instruksi'] = isset($value['data_instruksi'][0]['dokter']) ? $value['data_instruksi'][0]['dokter'] : ' - ';
                $value['grouping_tipe'] = $this->getWordingInstruksi($value['data_instruksi']);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $response['response']['totalCount'];
            $result['recordsFiltered'] = $response['response']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    private function getWordingInstruksi($wording_intruksi = [])
    {
        $wording_intruksi = $wording_intruksi[0];

        if($wording_intruksi['grouping_tipe']  == 'TINDAKANBMHP'){
            $label_wording = 'Tindakan';
        }else if($wording_intruksi['grouping_tipe']  == 'RESEPTUR'){
            $label_wording = 'Obat';
        }else if($wording_intruksi['grouping_tipe']  == 'PENUNJANG'){
            if($wording_intruksi['tipe_instruksi'] == 'BED_TINDAKAN'){
                $label_wording = 'Penjadwalan';
            }else if($wording_intruksi['tipe_instruksi'] == 'LAB_TINDAKAN'){
                $label_wording = 'Laboratorium';
            }else if($wording_intruksi['tipe_instruksi'] == 'RAD_TINDAKAN'){
                $label_wording = 'Radiologi';
            }else{
                $label_wording = 'Penunjang';
            }
        }else if($wording_intruksi['grouping_tipe']  == 'TINDAKANDIET'){
            $label_wording = 'Diet';
        }else{
            $label_wording = 'Tindakan';
        }

        $detail = '<button type="button" class="btn btn-sm bg-teal"><i class="fa fa-plus-square-o bg-teal"></i></button>';

        return $label_wording.'<br><br>'.$wording_intruksi['tgl_instruksi'].'<br><br>'.$detail;
        // TINDAKANDIET

    }

    private function getInstruksiDokter($data_instruksi)
    {

        $groupInstruksi = [];
        $html = '<table><tr><td>';
        foreach ($data_instruksi as $d_instruksi) {

            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_id'] = $d_instruksi['instruksi_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cppt_id'] = $d_instruksi['cppt_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['tipe_instruksi'] = $d_instruksi['tipe_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['catatan_instruksi'] = $d_instruksi['catatan_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cpptpegawai_id'] = $d_instruksi['cpptpegawai_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['is_verifikasi_dpjp'] = $d_instruksi['is_verifikasi_dpjp'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_deleted'] = $d_instruksi['instruksi_deleted'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['list_tindakan'][] = [
                'nama_tindakan' => $d_instruksi['tindakaninstruksi_nama'],
                'qty_tindakan' => $d_instruksi['qty'],
                'tgl_tindakan' => $d_instruksi['tgl_instruksi'],
                'ket_cyto' => $d_instruksi['ket_cyto'],
                'ket_racik' => $d_instruksi['ket_racik'],
                'tindakan_deleted' => $d_instruksi['tindakan_deleted'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'tipe_instruksi' => $d_instruksi['tipe_instruksi'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'instruksi' => str_replace('Cyto', 'CITO', $d_instruksi['instruksi']),
                'is_telah_implementasi' => $d_instruksi['is_telah_implementasi'],
                'ruangan_pertindakan' => $d_instruksi['ruangan_pertindakan'],
            ];
        }
 
        foreach ($groupInstruksi as $id_ins => $groupTipe) {

            foreach ($groupTipe as $nama_tipe => $data_ins) {
                $label_nama_tipe = "";
                if($nama_tipe == 'PENUNJANG'){
                    $label_nama_tipe = "Ruangan : ".$data_ins['list_tindakan'][0]['ruangan_pertindakan'];
                }
                // if($nama_tipe == 'TINDAKANBMHP'){
                //     $label_nama_tipe = 'Tindakan';
                // }else if($nama_tipe == 'RESEPTUR'){
                //     $label_nama_tipe = 'Obat';
                // }else if($nama_tipe == 'PENUNJANG'){
                //     $label_nama_tipe = 'Penunjang';
                // if($data_ins['instruksi_deleted'] == true){
                //     $html .= '<tr class="strikeout"><td><table>';
                // }else{
                //     $html .= '<tr><td><table>';
                // }
                    $html .= '<tr><td>'.@$label_nama_tipe.'</td>';

                if($data_ins['instruksi_deleted'] != true && $this->_pegawai_id == $data_ins['cpptpegawai_id'] && $data_ins['is_verifikasi_dpjp'] != true){
                    //is_verif
                }
                    $catatan_instruksi = $data_ins['catatan_instruksi']!= null ? $data_ins['catatan_instruksi'] : ' - ';
                    $html .= '</tr>';

                    $html .= '<tr><td><table>';
                    $groupTglTindakan = [];
                    foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                        $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                    }

                    foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                        $html .= '<tr>';
                        if($tipe_instruksi == 'LAB_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Laboratorium';
                        }else if($tipe_instruksi == 'RAD_TINDAKAN'){
                            $label_tipe_instruksi = "";
                        }else if($tipe_instruksi == 'LAB_PAKET'){
                            $label_tipe_instruksi = 'Paket Laboratorium';
                        }else if($tipe_instruksi == 'RAD_PAKET'){
                            $label_tipe_instruksi = 'Paket Radiologi';
                        }else if($tipe_instruksi == 'BED_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Bedah';
                        }else if($tipe_instruksi == 'DIET'){
                            $label_tipe_instruksi = 'DIET';
                        }else{
                            $label_tipe_instruksi = $tipe_instruksi;
                        }
                        $html .= '<td><b>'.@$label_tipe_instruksi.'</b></td></tr>';
                        if($nama_tipe == 'TINDAKANDIET') {
                            $html .= '<tr><td>Catatan : '.$catatan_instruksi.'</td></tr>';
                        }
                        foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                            $hitungTgl = 0;
                            $hitungInsTindakan = count($ins_tgltindakan);
                            foreach ($ins_tgltindakan as $row_tgltindakan) {    
                                $html .= '<tr>';
                                // if($hitungTgl == 0){
                                //     if($hitungInsTindakan == 1 && $row_tgltindakan['tindakan_deleted'] == true){$html .= '<td rowspan="'.@$hitungInsTindakan.'"><strike>'.@$row_tgltindakan['tgl_tindakan'].'</strike></td>';
                                //     }else{
                                //         $html .= '<td rowspan="'.@$hitungInsTindakan.'">'.@$row_tgltindakan['tgl_tindakan'].'</td>';
                                //     }
                                // }           
                                if($row_tgltindakan['tindakan_deleted'] == true){
                                    $html .= '<td><strike>';
                                    $html .= @$row_tgltindakan['instruksi'];
                                    if(substr($tipe_instruksi, -3) == 'KET'){
                                        $dftr_paket = json_decode($row_tgltindakan['daftar_paket'],true);
                                        if(count($dftr_paket)>1){
                                            $html .= '<ul>';
                                            foreach ($dftr_paket as $paket) {
                                                $html .= '<li>'.$paket.'</li>';
                                            }
                                            $html .= '</ul>';
                                        }
                                    }
                                    $html .= '</strike></td>';
                                }else{
                                    $html .= '<td>';
                                    $html .= @$row_tgltindakan['instruksi'];
                                    if(substr($tipe_instruksi, -3) == 'KET'){
                                        $dftr_paket = json_decode($row_tgltindakan['daftar_paket'],true);
                                        if(count($dftr_paket)>1){

                                        }
                                    }
                                    $html .= '</td>';
                                }
                                $html .= '</tr>';
                                $hitungTgl ++;
                            }
                        }
                        $html .= '<tr><td>&nbsp;</td></tr>';
                    }
                    $html .= '</table></td></tr>';
                $html .= '</table>';
            }
        }
        $html .= '</td></tr><table>';
        return $html;
    }

    private function getInstruksiStatusImplementasi($data_instruksi)
    {
        $groupInstruksi = [];
        $html = '<table><tr><td>';
        foreach ($data_instruksi as $d_instruksi) {
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_id'] = $d_instruksi['instruksi_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cppt_id'] = $d_instruksi['cppt_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['tipe_instruksi'] = $d_instruksi['tipe_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['catatan_instruksi'] = $d_instruksi['catatan_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cpptpegawai_id'] = $d_instruksi['cpptpegawai_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['is_verifikasi_dpjp'] = $d_instruksi['is_verifikasi_dpjp'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_deleted'] = $d_instruksi['instruksi_deleted'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['list_tindakan'][] = [
                'nama_tindakan' => $d_instruksi['tindakaninstruksi_nama'],
                'qty_tindakan' => $d_instruksi['qty'],
                'tgl_tindakan' => $d_instruksi['tgl_instruksi'],
                'ket_cyto' => $d_instruksi['ket_cyto'],
                'ket_racik' => $d_instruksi['ket_racik'],
                'tindakan_deleted' => $d_instruksi['tindakan_deleted'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'tipe_instruksi' => $d_instruksi['tipe_instruksi'],
                'status' => $d_instruksi['status']
            ];
        }
        foreach ($groupInstruksi as $id_ins => $groupTipe) {
            foreach ($groupTipe as $nama_tipe => $data_ins) {
                if($data_ins['instruksi_deleted'] == true){
                    $html .= '<tr><td><table>';
                }else{
                    $html .= '<tr><td><table>';
                }
                    $html .= '<tr><td>&nbsp;</td>';


                if($data_ins['instruksi_deleted'] != true && $this->_pegawai_id == $data_ins['cpptpegawai_id'] && $data_ins['is_verifikasi_dpjp'] != true){
                    //is_verif
                }
                    $html .= '</tr>';
                    $html .= '<tr><td>&nbsp;</td></tr>';
                        $html .= '<tr><td><table>';
                    $groupTglTindakan = [];
                    foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                        $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                    }
                    foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                        foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                            $hitungTgl = 0;
                            $hitungInsTindakan = count($ins_tgltindakan);
                            foreach ($ins_tgltindakan as $row_tgltindakan) {    
                                $html .= '<tr>';
                                if($hitungTgl == 0){
                                    $html .= '<td rowspan="'.@$hitungInsTindakan.'">&nbsp;</td>';
                                }           
                                if($row_tgltindakan['tindakan_deleted'] == true){
                                    $html .= '<td><strike>TERHAPUS</strike></td>';
                                }else{
                                    $html .= '<td>'.@$row_tgltindakan['status'].'</td>';
                                }
                                $html .= '</tr>';
                                $hitungTgl ++;
                            }
                        }
                        $html .= '<tr><td>&nbsp;</td></tr>';
                    }
                    $html .= '</table></td></tr>';
                $html .= '</table>';
            }
        }
        $html .= '</td></tr><table>';
        return $html;
    }

    private function getInstruksiNamaDokter($data_instruksi)
    {
        $groupInstruksi = [];
        $html = '<table><tr><td>';
        foreach ($data_instruksi as $d_instruksi) {
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_id'] = $d_instruksi['instruksi_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cppt_id'] = $d_instruksi['cppt_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['tipe_instruksi'] = $d_instruksi['tipe_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['catatan_instruksi'] = $d_instruksi['catatan_instruksi'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['cpptpegawai_id'] = $d_instruksi['cpptpegawai_id'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['is_verifikasi_dpjp'] = $d_instruksi['is_verifikasi_dpjp'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['instruksi_deleted'] = $d_instruksi['instruksi_deleted'];
            $groupInstruksi[$d_instruksi['instruksi_id']][$d_instruksi['grouping_tipe']]['list_tindakan'][] = [
                'nama_tindakan' => $d_instruksi['tindakaninstruksi_nama'],
                'qty_tindakan' => $d_instruksi['qty'],
                'tgl_tindakan' => $d_instruksi['tgl_instruksi'],
                'ket_cyto' => $d_instruksi['ket_cyto'],
                'ket_racik' => $d_instruksi['ket_racik'],
                'tindakan_deleted' => $d_instruksi['tindakan_deleted'],
                'daftar_paket' => $d_instruksi['daftar_paket'],
                'tipe_instruksi' => $d_instruksi['tipe_instruksi'],
                'status' => $d_instruksi['status'],
                'dokter' => $d_instruksi['dokter'],
                'bmhp_tindakandetail' => $d_instruksi['bmhp_tindakandetail']
            ];
        }
        foreach ($groupInstruksi as $id_ins => $groupTipe) {
            foreach ($groupTipe as $nama_tipe => $data_ins) {
                if($data_ins['instruksi_deleted'] == true){
                    $html .= '<tr><td><table>';
                }else{
                    $html .= '<tr><td><table>';
                }
                    $html .= '<tr><td>&nbsp;</td>';


                if($data_ins['instruksi_deleted'] != true && $this->_pegawai_id == $data_ins['cpptpegawai_id'] && $data_ins['is_verifikasi_dpjp'] != true){
                    //is_verif
                }
                    $html .= '</tr>';
                    $html .= '<tr><td>&nbsp;</td></tr>';
                        $html .= '<tr><td><table>';
                    $groupTglTindakan = [];
                    foreach ($data_ins['list_tindakan'] as $ins_tindakan) {
                        $groupTglTindakan[$ins_tindakan['tipe_instruksi']][$ins_tindakan['tgl_tindakan']][] = $ins_tindakan;
                    }
                    foreach ($groupTglTindakan as $tipe_instruksi => $group_tgl) {
                        $html .= '<tr>';
                        if($tipe_instruksi == 'LAB_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Laboratorium';
                        }else if($tipe_instruksi == 'RAD_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Radiologi';
                        }else if($tipe_instruksi == 'LAB_PAKET'){
                            $label_tipe_instruksi = 'Paket Laboratorium';
                        }else if($tipe_instruksi == 'RAD_PAKET'){
                            $label_tipe_instruksi = 'Paket Radiologi';
                        }else if($tipe_instruksi == 'BED_TINDAKAN'){
                            $label_tipe_instruksi = 'Tindakan Bedah';
                        }else{
                            $label_tipe_instruksi = $tipe_instruksi;
                        }
                        $html .= '<td>&nbsp;</td><td>'.@$label_tipe_instruksi.'</td></tr>';
                        foreach ($group_tgl as $tgl_ins => $ins_tgltindakan) {
                            $hitungTgl = 0;
                            $hitungInsTindakan = count($ins_tgltindakan);
                            foreach ($ins_tgltindakan as $row_tgltindakan) {    
                                $html .= '<tr>';
                                if($hitungTgl == 0){
                                    $html .= '<td rowspan="'.@$hitungInsTindakan.'">&nbsp;</td>';
                                }           
                                $namadokterinstruksi = '-';
                                if(isset($row_tgltindakan['dokter'])){
                                    $namadokterinstruksi = $row_tgltindakan['dokter'];
                                }
                                if($tipe_instruksi == 'BMHP' && isset($row_tgltindakan['bmhp_tindakandetail'])){
                                    $detailbmhp = json_decode($row_tgltindakan['bmhp_tindakandetail'],TRUE);
                                    if(isset($detailbmhp['nama_pegawai'])){
                                        $namadokterinstruksi = $detailbmhp['nama_pegawai'];
                                    }
                                }
                                if($row_tgltindakan['tindakan_deleted'] == true){
                                    $html .= '<td><strike>'.@$namadokterinstruksi.'</strike></td>';
                                }else{
                                    $html .= '<td>'.@$namadokterinstruksi.'</td>';
                                }
                                $html .= '</tr>';
                                $hitungTgl ++;
                            }
                        }
                        $html .= '<tr><td>&nbsp;</td></tr>';
                    }
                    $html .= '</table></td></tr>';
                $html .= '</table>';
            }
        }
        $html .= '</td></tr><table>';
        return $html;
    }

    private function getStatusImplementasi($data_instruksi)
    {
        $array_status = [];
        foreach ($data_instruksi as $instruksi) {
            if($instruksi['tindakan_deleted'] == false){
                $array_status[] = $instruksi['is_telah_implementasi'];
            }
        }
        if(count(array_unique($array_status)) === 1){
            if(current($array_status) == true){
                return true;
            }
        }
        return false;
    }

    private function getStatusBatalPenunjang($data_instruksi)
    {
        $array_status_penunjang_batal = [];

        foreach ($data_instruksi as $ins_tindakan) {
            if($ins_tindakan['tindakan_deleted'] != true){
                $array_status_implemented[] = $ins_tindakan['is_telah_implementasi'];
                if($ins_tindakan['grouping_tipe'] == 'PENUNJANG'){
                    $array_status_penunjang_batal[] = $ins_tindakan['status_implementasi'];
                }
            }
        }
        $is_penunjang_batal = false;
        if(count($array_status_penunjang_batal)>0){
            if(in_array('472', $array_status_penunjang_batal)){
                $is_penunjang_batal = true;
            }
        }
        return $is_penunjang_batal;
    }

    public function actionImplementasiTransaksi()
    {
        try{
            $params = Yii::$app->request;
            $pendaftaran_id = DocoHelpers::decrypt($params->get('id','MA'));
            $decryptedCppt_id = $params->get('cppt_id','MA');
            $decryptedInstruksi_id = $params->get('instruksi_id','MA');

            $cppt_id = DocoHelpers::decrypt($params->get('cppt_id','MA'));
            $instruksi_id = DocoHelpers::decrypt($params->get('instruksi_id','MA'));
            $docoVars = Yii::$app->docoVars;
            $ruangan_id = $docoVars->workspace('ruangan_id') ? $docoVars->workspace('ruangan_id') : 1;
            $instalasi_id = $docoVars->workspace('instalasi_id') ? $docoVars->workspace('instalasi_id') : 1;

            $request = $this->_restIgd->get('implementasi-igd/bundle-data-transaksi',[
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'cppt_id' => $cppt_id,
                    'instruksi_id' => $instruksi_id,
                    'ruangan_id' => $ruangan_id
                ]
            ]);
            $response = json_decode($request->getBody(),TRUE);
            $response = $response['response'];
            $dataInstruksi = $response['dataInstruksi'];
            $dataAdditional = $response['dataAdditional'];
            $datasetTindakan = $response['datasetTindakan'];
            $datasetBmhp = $response['datasetBmhp'];
            $data_perawat = $response['data_perawat'];
            $modelImplementasi = new ImplementasiForm;
            $modelImplementasi->instruksi_id = $instruksi_id;
            if(!empty($dataInstruksi['catatan_instruksi'])){
                $modelImplementasi->catatan = $dataInstruksi['catatan_instruksi'];
            }
            return $this->renderAjax('implementasi/transaksi',[
                'decryptedCppt_id' => $decryptedCppt_id,
                'decryptedInstruksi_id' => $decryptedInstruksi_id,
                'modelImplementasi' => $modelImplementasi,
                'datasetBmhp' => $datasetBmhp,
                'datasetTindakan' => $datasetTindakan,
                'data_perawat' => $data_perawat,
                'dataAdditional' => $dataAdditional,
                'instalasi_id' => $instalasi_id,
                'ruangan_id' => $ruangan_id
            ]);
        } catch(RequestException $e){
            // var_dump(json_decode($e->getResponse()->getBody(),TRUE));exit;
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch(\Exception $e){
            // var_dump($e->getMessage());exit;
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionImplementasiCreateTransaksi()
    {
        try{
            $request = Yii::$app->request;
            $post = $request->post();
            $model = new ImplementasiForm;
            $model->attributes = $post['ImplementasiForm'];
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if($model->validate()){
                $detailImplementasiTindakan = $request->post('ImplementasiTindakanForm',[]);
                if(is_array($detailImplementasiTindakan) && count($detailImplementasiTindakan)>0){
                    foreach ($detailImplementasiTindakan as $key_detailtindakan => $val_detailimplementasitindakan) {
                        $detailTindakanForm = new ImplementasiTindakanForm;
                        $detailTindakanForm->attributes = $val_detailimplementasitindakan;
                        if(!$detailTindakanForm->validate()){
                            $response = $detailTindakanForm->errors;
                            return DocoHelpers::response($response, 422, 'ImplementasiTindakanForm['.$key_detailtindakan.']');
                        }
                    }
                }
                $detailImplementasiBmhp = $request->post('ImplementasiBmhpForm',[]);
                if(is_array($detailImplementasiBmhp) && count($detailImplementasiBmhp)>0){
                    foreach ($detailImplementasiBmhp as $key_detailbmhp => $val_detailimplementasibmhp) {
                        $detailBmhpForm = new ImplementasiBmhpForm;
                        $detailBmhpForm->attributes = $val_detailimplementasibmhp;
                        if(!$detailBmhpForm->validate()){
                            $response = $detailBmhpForm->errors;
                            return DocoHelpers::response($response, 422, 'ImplementasiBmhpForm['.$key_detailbmhp.']');
                        }
                    }
                }
                $response = $this->_restIgd->post('implementasi-igd/create-transaksi-implementasi', [
                    'form_params' => $post
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }

        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } 
    }

    public function actionCetakImplementasiPdf()
    {

        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id','MA'));
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $path = Yii::getAlias("@download") . "/instruksi-implementasi.pdf";
        $response = $this->_restIgd->get('implementasi-igd/cetak-implementasi-pdf',[
            'query' => [
                'pendaftaran_id'=>$pendaftaran_id,
                'ruangan_id' => $ruangan_id,
                'id_usercetak' => $id_usercetak,
                'nama_usercetak' => $nama_usercetak
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
}

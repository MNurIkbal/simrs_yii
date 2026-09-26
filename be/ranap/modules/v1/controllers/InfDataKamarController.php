<?php

namespace app\modules\v1\controllers;

/**
 * @Author: Ardi Pratama
 */

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KetTempatTidur;
use app\modules\v1\models\DashboardKamarView;
use app\modules\v1\models\DashboardKamarDetailView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KelasPelayanan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KetersediaanKamarFn;
use app\modules\v1\models\KetersediaanKamarFnDet;
use app\modules\v1\models\KetersediaanKamarFnDetail;
use app\modules\v1\models\Lookup;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;


class InfDataKamarController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KamarTempatTidur';
    protected $_title = 'Informasi Data Kamar';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $mKetTempatTidur = KetTempatTidur::find()->orderBy(['kamarruangan_jenis'=> SORT_ASC])->all();
        $list_data_ruangan = Ruangan::find()->where(['instalasi_id' => 3])->orderBy(['ruangan_nama' => SORT_ASC])->all();
        $list_data_kamar = KamarRuangan::find()->orderBy(['kamarruangan_nokamar' => SORT_ASC])->all();
        $list_kelas_pelayanan = KelasPelayanan::find()->all();
        $status = [
            ['is_stopakomodasi' => false,
            'status' => 'Sedang dirawat'],
            ['is_stopakomodasi' => true,
            'status' => 'Akan pulang'],
        ];
        return [
            'list_keterangan'=>$mKetTempatTidur,
            'list_data_ruangan' => $list_data_ruangan,
            'list_data_kamar' => $list_data_kamar,
            'list_kelas_pelayanan' => $list_kelas_pelayanan,
            'list_status' => $status,
        ];
    }

    /**
     * @todo Fungsi untuk mendapatkan data tempat tidur
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @param int advanced-filter['ruangan_nama']
     * @param int advanced-filter['kelaspelayanan_nama']
     * @param int advanced-filter['kamarruangan_nokamar']
     * @return array $results
     */
    public function actionGetDataTempatTidur()
    {
        $request = Yii::$app->request;
        $results = array();
        $advanced_filter = $request->get('advanced-filter', false);
        $model = new KetersediaanKamarFn;
        $modelQuery = $model::find();
        
        if ($advanced_filter) {
            if( isset($advanced_filter['no_tempattidur']) ) {
                $modelQuery->andWhere([
                    'ILIKE' ,'no_tempattidur', $advanced_filter['no_tempattidur']
                ]);
                unset($advanced_filter['no_tempattidur']);
            }
           
            if(isset($_GET['advanced-filter']['type'])) {
                $type = $_GET['advanced-filter']['type'];
                $pattern = "/[,\s:]/";
                $components = preg_split($pattern, $type);
                
                $modelQuery->andWhere(['in', 'kettempattidur_warna', $components ]);
                
                unset($advanced_filter['type']);
            }

            $modelQuery->andWhere($advanced_filter);
        }
        //$modelQuery->andWhere(['kettempattidur_warna' => 'GhostWhite' ]);
        
        $data = $modelQuery->all();
        
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if(!isset($results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']])) {
                    $results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']] = $value;
                }
            }
            $results = $this->generateBed($results);
        }
        
        return [
            'data' => $results,
            'totalCount' => count($results)
        ];
    }

    public function actionGetDataTempatTidurV2()
    {
        $request = Yii::$app->request;
        $results = array();
        $advanced_filter = $request->get('advanced-filter', false);
        // $detail = Yii::$app->db->createCommand("
        //     SELECT * FROM fgetketersediaankamar_detail()")->queryAll();
       
        $model = new KetersediaanKamarFnDet;
        $model2 = new KetersediaanKamarFn;
        $modelQuery = $model::find();
        $modelQuery2 = $model2::find();
        if ($advanced_filter) {
            if( isset($advanced_filter['no_tempattidur']) ) {
                $modelQuery->andWhere([
                    'ILIKE' ,'no_tempattidur', $advanced_filter['no_tempattidur']
                ]);
                unset($advanced_filter['no_tempattidur']);
            }
           
            if(isset($_GET['advanced-filter']['type'])) {
                $type = $_GET['advanced-filter']['type'];
                $pattern = "/[,\s:]/";
                $components = preg_split($pattern, $type);
                
                $modelQuery->andWhere(['in', 'kettempattidur_warna', $components ]);
                
                unset($advanced_filter['type']);
            }

            if(isset($_GET['advanced-filter']['status'])) {
                $status = ($_GET['advanced-filter']['status'] == '1') ?  'true' : 'false';
                
                
                $modelQuery->andWhere(['is_stopakomodasi' => $status ]);
                
                unset($advanced_filter['status']);
            }

            $modelQuery->andWhere($advanced_filter);
        }
        //$modelQuery->andWhere(['kettempattidur_warna' => 'GhostWhite' ]);
        $modelQuery->orderBy([ 
            'ruangan_id' => SORT_ASC,
            'no_tempattidur' => SORT_ASC
         ]);
        $data = $modelQuery->all();
        $data2 = $modelQuery2->all();
        $temp =[];
        if (!empty($data)) {
            foreach ($data2 as $key2 => $value2) {
                if(!isset($results2[$value2['ruangan_id'].'-'.$value2['kamarruangan_id']][$value2['kamartempattidur_id']])) {
                    $results2[$value2['ruangan_id'].'-'.$value2['kamarruangan_id']][$value2['kamartempattidur_id']] = $value2;
                    
                }
            }
            foreach ($data as $key => $value) {
                if(!isset($results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']])) {
                    $results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']] = $value;
                    if(!isset($results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_isi']) && !isset($results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_kosong'])) {
                        $results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_kosong'] =   (isset($results2[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_kosong'])) ? $results2[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_kosong'] : '0'; 
                        $results[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_isi'] = (isset($results2[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_isi'])) ?   $results2[$value['ruangan_id'].'-'.$value['kamarruangan_id']][$value['kamartempattidur_id']]['total_isi'] : '0';
                    }
                }
            }
            
            $results = $this->generateBedV2($results);
           
          
        }
        
        return [
            'data' => $results,
            'totalCount' => count($results)
        ];
    }

    public function actionDetailKamarRuangan()
    {
        $request = Yii::$app->request;
        $kamarruangan_id = $request->get('kamarruangan_id',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id',0);

        $data_detail = DashboardKamarDetailView::find()->where([
            'kamarruangan_id'=>$kamarruangan_id,
            'ruangan_id'=>$ruangan_id,
            'kelaspelayanan_id'=>$kelaspelayanan_id
        ])->asArray()->all();

        $info_kamar = (new \yii\db\Query())
        ->select([
            'kamarruangan_nokamar',
            'ruangan_m.ruangan_nama',
            'kelaspelayanan_m.kelaspelayanan_nama'
        ])
        ->from('kamarruangan_m')
        ->leftJoin('ruangan_m','ruangan_m.ruangan_id = kamarruangan_m.ruangan_id')
        ->leftJoin('kelaspelayanan_m','kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id')
        ->where([
            'kamarruangan_m.kamarruangan_id'=>$kamarruangan_id,
            'kamarruangan_m.ruangan_id'=>$ruangan_id,
            'kamarruangan_m.kelaspelayanan_id'=>$kelaspelayanan_id
        ])
        ->one();
        
        return [
            'data_detail' => $data_detail,
            'info_kamar' => $info_kamar,
        ];
    }

    public function actionListKamarRuangan()
    {
        $request = Yii::$app->request;
        $kamarruangan_id = $request->get('kamarruangan_id');

        $data_kamar = KamarRuangan::find()->asArray()->all();

        return ['list_kamar'=>$data_kamar];
    }

    /**
     * @todo Fungsi untuk menghasilkan tempat tidur perruangan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     * @param array $data
     * @return array $results
     */
    private function generateBed($data)
    {
        $results = array();

        if (empty($data)) {
            return [];
        }

        foreach ($data as $keyRuangan => $ruangan) {
            $bed = '';
            foreach ($ruangan as $keyKamar => $kamar) {
                $dataAttribute = "data-title='Ruangan ".$kamar['ruangan_nama']." - ".$kamar['no_tempattidur']."' data-value='".$this->helper->encrypt($kamar['kamartempattidur_id'])."' data-ket_id='".$kamar['kettempattidur_id']."'";
                $bed = $bed."<button type='button' ".$dataAttribute." class='btn btn-xs btn-bed' style='background-color:".$kamar['kode_warna'].";padding-left:10px!important;'><i class='fa fa-bed position-left'></i>".$kamar['no_tempattidur']."</button> ";
                $results[$keyRuangan] = $kamar;
                $results[$keyRuangan]['harga_tariftindakan'] = DocoHelpers::rupiahDisplay($kamar['harga_tariftindakan']);
                $results[$keyRuangan]['no_tempattidur'] = $bed;
            }
        }

        return $results;
    }

    private function generateBedV2($data)
    {
        $results = array();

        if (empty($data)) {
            return [];
        }
       
        foreach ($data as $keyRuangan => $ruangan) {
            $bed = '';
            $index =0;
            foreach ($ruangan as $keyKamar => $kamar) {
                $index++;
                if ($index % 4 == 0) {
                    # code...
                    $new_row = '<br>';
                }else {
                    $new_row ='';
                }
                if ($kamar['status_isi'] != 'false') {
                    # code...
                    $no_rekam_medik = ($kamar['no_rekam_medik'] )? $kamar['no_rekam_medik'] : '' ;
                    $status_pasien = ($kamar['is_stopakomodasi']  == 'true') ? 'Akan Pulang': 'Sedang dirawat' ;
                    // $nama_pasien = ($kamar['nama_pasien'] )? $kamar['nama_pasien'] : '' ;
                    if ($kamar['nama_pasien']) {
                        # code...
                        $nama_pasien = preg_replace('/[^a-zA-Z0-9\']/', ' ', $kamar['nama_pasien']);
                        $nama_pasien = str_replace("'", '', $nama_pasien);
                       
                    } else {
                        # code...
                        $nama_pasien = '';
                    }
                    if ($kamar['nama_pegawai']) {
                        # code...
                        $nama_pegawai = preg_replace('/[^a-zA-Z0-9\']/', ' ', $kamar['nama_pegawai']);
                        $nama_pegawai = str_replace("'", '', $nama_pegawai);
                    } else {
                        # code...
                        $nama_pegawai = '';
                    }
                    
                    $ruang_sebelum_nama = ($kamar['ruang_sebelum_nama'] )? $kamar['ruang_sebelum_nama'] : '' ;
                    $tgl_admisi = ($kamar['tgl_admisi'])  ? date('d M Y', strtotime($kamar['tgl_admisi'])) : '';
                    $tanggal_lahir =  ($kamar['tanggal_lahir'])  ? date('d M Y', strtotime($kamar['tanggal_lahir'])) : '';
                    $umur =  ($kamar['umur'])  ? substr($kamar['umur'],0,2) : '';
                    if ($kamar['jeniskelamin_id']) {
                        # code...
                        $jenis_kelamin = ($kamar['jeniskelamin_id'] == 15) ? 'L' : 'P';
                    }else {
                        $jenis_kelamin ='';
                    }
                    $nama_pasien_title = DocoHelpers::cutSentence($nama_pasien, 15);
                    $title = $kamar['no_tempattidur']."-".$no_rekam_medik."-".$nama_pasien_title."-".$jenis_kelamin;
                    $bg_color = '#26367a';
                    $text_color = 'text-white';
                    $text_status_akomodasi = "";
                    if ($kamar['is_stopakomodasi']  == 'true') {
                        $bg_color = '#7efff5';
                        $text_color = 'text-warning';
                        $text_status_akomodasi = "Pasien sudah melakukan proses stop akomodasi";
                        $title = "<span class='glyphicon glyphicon-info-sign' aria-hidden='true'></span> &nbsp". $title;
                    }
                    $dataAttribute = "data-toggle='tooltip' data-html='true' data-container='body' data-title='Ruangan ".$kamar['ruangan_nama']." - ".$kamar['no_tempattidur']."' data-value='".$this->helper->encrypt($kamar['kamartempattidur_id'])."' data-ket_id='".$kamar['kettempattidur_id']."' data-placement='bottom' title='
                    <tr>
                        <td>No. Rekam Medik : ".$no_rekam_medik." &nbsp</td>
                        <td></td>
                        <td>Tanggal Masuk : ".$tgl_admisi." &nbsp</td>
                    </tr>
                    <tr>
                        <td>Pasien : ".$nama_pasien." ($jenis_kelamin) &nbsp</td>
                        <td></td>
                        <td>Dokter : ".$nama_pegawai." &nbsp</td>
                    </tr>
                    <tr>
                        <td>Tanggal Lahir : ".$tanggal_lahir." (".$umur.") &nbsp</td>
                        <td></td>
                        <td>Ruangan sebelumnya : ".$ruang_sebelum_nama." &nbsp</td>
                    </tr>
                    <tr>
                        <td>Status : ".$status_pasien." &nbsp</td>
                        <td></td>
                        <td>".$text_status_akomodasi." &nbsp</td>
                    </tr>
                    '";
                   
                }else {
                    $bg_color = '#d4cdbe';
                    $text_color = 'text-dark';
                    $title = $kamar['no_tempattidur']."-".$kamar['kettempattidur_nama'];
                    $dataAttribute ="data-title='Ruangan ".$kamar['ruangan_nama']." - ".$kamar['no_tempattidur']."' data-value='".$this->helper->encrypt($kamar['kamartempattidur_id'])."' data-ket_id='".$kamar['kettempattidur_id']."'"; 
                   
                }
                // $dataAttribute = "data-title='Ruangan ".$kamar['ruangan_nama']." - ".$kamar['no_tempattidur']."' data-value='".$this->helper->encrypt($kamar['kamartempattidur_id'])."' data-ket_id='".$kamar['kettempattidur_id']."'";
                $bed = $bed."<button type='button' ".$dataAttribute." class='btn btn-xs btn-bed mr-2 ".$text_color."' style='background-color:".$bg_color.";padding-left:10px!important;'>".$title."</button> ".$new_row."";
                $results[$keyRuangan] = $kamar;
                $results[$keyRuangan]['harga_tariftindakan'] = DocoHelpers::rupiahDisplay($kamar['harga_tariftindakan']);
                $results[$keyRuangan]['no_tempattidur'] = $bed;
                
            }
        }

        return $results;
    }

    public function actionSaveUpdateStatus()
    {
        $kamartempattidur_id = Yii::$app->request->post('kamartempattidur_id', null);
        if(is_null($kamartempattidur_id)) {
            return $this->responseJson(422, 'parameter kamartempattidur_id tidak boleh kosong!');
        }
        $tempatTidur = KamarTempatTidur::find()->where(['kamartempattidur_id' => $this->helper->decrypt($kamartempattidur_id)])->one();
        if( empty($tempatTidur) ){
            return $this->responseJson(422, 'data tempat tidur tidak ditemukan!');
        }
        $tempatTidur->kettempattidur_id = Yii::$app->request->post('kettempattidur_id');
        $tempatTidur->status_isi = false;
        $tempatTidur->update_status_by = Yii::$app->jwt->user->loginpemakai_id;
        $tempatTidur->update_status_date = date('Y-m-d H:i:s');
        if( !$tempatTidur->save() ){
            Yii::error([
                'kettempattidur' => $tempatTidur->errors
            ]);
            return $this->responseJson(500, 'Update Status Gagal!');
        }
        return $this->responseJson(200, 'Update status tempat tidur berhasil!', [
            'post' => Yii::$app->request->post()
        ]);
    }
}
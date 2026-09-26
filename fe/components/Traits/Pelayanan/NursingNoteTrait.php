<?php
/*
@author: ilhamsyah
*/

namespace app\components\Traits\Pelayanan;

use Yii;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\components\Traits\Pelayanan\NursingNoteForm;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use yii\helpers\Url;
use yii\helpers\Html;

trait NursingNoteTrait 
{

    /**
     * @var String $type
     * @author ilham.pramono@sirs.co.id
     */
    public $type;

    /**
     * @var String $serviceRest
     * @author ilham.pramono@sirs.co.id
     */
    public $url;

    /**
     * @var String $serviceRest
     * @author ilham.pramono@sirs.co.id
     */
    public $serviceRest;

    private function path()
    {
        $url = null;
        $serviceRest = null;
        switch ($this->type) {
            case 'RJ':
                $url = '/rajal/pemeriksaan';
                $serviceRest = Yii::$app->docoRest->rajal;
                break;
            case 'RI':
                $url = '/ranap/pemeriksaan-rawat-inap';
                $serviceRest = Yii::$app->docoRest->ranap;
                break;
            case 'RD':
                $url = '/igd/pemeriksaan-igd';
                $serviceRest = Yii::$app->docoRest->igd;
                break;
            default:
                break;
        }
        $this->serviceRest = $serviceRest;
        $this->url = $url;
    }
    public function actionNursingNote($id,$pasienadmisi_id=null)
    {
        $this->path();
        $url = $this->url;
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $userIdentity = Yii::$app->session->get('user_identity');
        $is_nurse = false;
        if($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
            $is_nurse = true;
        }
        return $this->renderAjax('//pelayanan/nursingnote/index', [
            'pendaftaran_id'   => $id,
            'pasienadmisi_id'  => $pasienadmisi_id,
            'is_nurse' => $is_nurse,
            'url' => $url,
        ]);

     
    }
    public function actionGetNursingNote($id, $pasienadmisi_id=null)
    {
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['id'] = $pendaftaran_id;
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $no = 0;

        $userIdentity = Yii::$app->session->get('user_identity');
        $is_nurse = ($userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN) ? true : false ;
        $this->path();
        $url = $this->url;
        $urlEdit = $url.'/edit-nursing-note';
        $urlDelete = $url.'/hapus-nursing-note';
        $strike = '<s>';
        $res = $this->helper->guzzleExec($this->serviceRest, [
            'method' => 'get',
            'url' => 'nursing-note/get-nursing-note',
            'payload' => [
                'query' => $payload,
            ],
         ]);
         foreach ($res['data'] as $key => $value) {
            $no++;
            $res['data'][$key]['tanggal'] = date('d/m/Y',strtotime($value['tgl_catatan']));
            $res['data'][$key]['jam'] = date('H:i',strtotime($value['waktu_catatan']));
            $res['data'][$key]['rowNum'] = $no;
            if(!$value['is_deleted']){

                $aksi = Html::button(
                    '<i class="fa fa-pencil"></i>',
                    [
                        'disabled' => $value['pegawai_id'] != $userIdentity['id_pegawai'],
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '45%',
                        'class' => 'btn btn-info btn-sm btn-edit-nursing-note',
                        'action' => Url::to([
                            $urlEdit,
                            'id' => $id,
                            'pasienadmisi_id' => $pasienadmisi_id,
                            'catatankeperawatan_id' => $value['catatankeperawatan_id'],
                        ]),
                    ]
                ) . ' ' . Html::button(
                    '<i class="fa fa-trash"></i>',
                    [
                        'id' => 'delete-note',
                        'disabled' => ($is_nurse) ? $value['pegawai_id'] != $userIdentity['id_pegawai'] : true,
                        'class' => 'btn btn-danger btn-sm btn-delete-nursing-note',
                        'href' => Url::to([
                            $urlDelete,
                            'catatankeperawatan_id' => $value['catatankeperawatan_id'],
                        ]),
                    ]
                );

            }
            else{
                if(isset($value['peg_update_nama']) && !empty($value['peg_update_nama'])) {
                    $res['data'][$key]['tanggal'] = $strike.date('d/m/Y',strtotime($value['tgl_catatan'])).'</s>';
                    $res['data'][$key]['jam'] = $strike.date('H:i',strtotime($value['waktu_catatan'])).'</s>';
                    $res['data'][$key]['rowNum'] = $strike.$no.'</s>';
                    $res['data'][$key]['kegiatan_perawat'] = $strike.$value['kegiatan_perawat'].'</s>';
                    $res['data'][$key]['catatan'] = $strike.$value['catatan'].'</s>';
                    $res['data'][$key]['nama_pegawai'] = $strike.$value['nama_pegawai'].'</s>';
                    $aksi = !empty($value['deleted_by']) ? 'Diubah Oleh :'.$value['peg_update_nama'] : ' - ';
                } else {
                    $res['data'][$key]['tanggal'] = $strike.date('d/m/Y',strtotime($value['tgl_catatan'])).'</s>';
                    $res['data'][$key]['jam'] = $strike.date('H:i',strtotime($value['waktu_catatan'])).'</s>';
                    $res['data'][$key]['rowNum'] = $strike.$no.'</s>';
                    $res['data'][$key]['kegiatan_perawat'] = $strike.$value['kegiatan_perawat'].'</s>';
                    $res['data'][$key]['catatan'] = $strike.$value['catatan'].'</s>';
                    $res['data'][$key]['nama_pegawai'] = $strike.$value['nama_pegawai'].'</s>';
                    $aksi = !empty($value['deleted_by']) ? 'Dihapus Oleh :'.$value['peg_delete_nama'] : ' - ';                    
                }
            }
            $res['data'][$key]['aksi'] = $aksi;
           }
        return DocoHelpers::response($res);
        
    }
    public function actionCetakNursingNote($id, $pasienadmisi_id=null)
    {
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $pasienadmisiId  = DocoHelpers::decrypt($pasienadmisi_id);
       $urlReport = 'nursing-note';
       if(Yii::$app->report->isAvailable($urlReport)){
        return Yii::$app->report->exec($urlReport,[
            'queryParameter' => [
                'pendaftaran_id'=>$pendaftaran_id,
                'pasienadmisi_id'=>$pasienadmisiId,

            ],
           
        ]);
    }
    }

    public function actionHapusNursingNote($catatankeperawatan_id){
        $userIdentity = Yii::$app->session->get('user_identity');
        $this->path();
        $result = $this->guzzleExec($this->serviceRest, [
            'url' => 'nursing-note/hapus-nursing-note',
            'method' => 'post',
            'payload' => [
                'query' => [
                    'catatankeperawatan_id' => $catatankeperawatan_id,
                    'pegawai_id' => $userIdentity['loginpemakai_id'],
                ],
            ],
        ]);
        return $this->helper->response($result, 200);
    }

    public function actionCreateNursingNote($id,$pasienadmisi_id=nulL){
        $this->path();
        $url = $this->url;
        $title = 'Tambah Note';
        $modal = new NursingNoteForm;
        $tgl = date('d',strtotime($this->_data_pasien['tgl_pendaftaran']));
        $bln = date('m',strtotime($this->_data_pasien['tgl_pendaftaran']));
        $thn = date('Y',strtotime($this->_data_pasien['tgl_pendaftaran']));

        $formActionUrl = $url.'/save-nursing-note?id='. $id.'&pasienadmisi_id='.$pasienadmisi_id;
        return $this->renderAjax('//pelayanan/nursingnote/addnote', get_defined_vars());

    }

    public function actionEditNursingNote($id, $catatankeperawatan_id, $pasienadmisi_id=null){
        $pendaftaran_id  = DocoHelpers::decrypt($id);
        $this->path();
        $url = $this->url;
        $title = 'Ubah Note';
        $res = $this->helper->guzzleExec($this->serviceRest, [
            'method' => 'get',
            'url' => 'nursing-note/get-nursing-note',
            'payload' => [
                'query' => [
                    'page' => 1,
                    'advanced-filter' => [
                        'catatankeperawatan_id' => $catatankeperawatan_id,
                    ],
                ],
            ],
        ]);

        $data = reset($res['data']);
        $modal = new NursingNoteForm;
        $modal->tanggal = $data['tgl_catatan'];
        $modal->kegiatan_perawat = [$data['kegiatan_perawat']];
        $modal->catatan = $data['catatan'];
        $tgl = date('d',strtotime($this->_data_pasien['tgl_pendaftaran']));
        $bln = date('m',strtotime($this->_data_pasien['tgl_pendaftaran']));
        $thn = date('Y',strtotime($this->_data_pasien['tgl_pendaftaran']));

        $formActionUrl = $url.'/save-nursing-note?id='. $id.'&pasienadmisi_id='.$pasienadmisi_id.'&catatankeperawatan_id='.$catatankeperawatan_id;
        return $this->renderAjax('//pelayanan/nursingnote/addnote', get_defined_vars());

    }

    public function actionGetKegiatanKeperawatan()
     {
        $this->path();
        $url = $this->url;
        $request = Yii::$app->request->get();
        $jenis_kegiatan = Yii::$app->request->get('jenis_kegiatan');
        $cari = Yii::$app->request->get('q');
        if($jenis_kegiatan == 0){
            $jenis_kegiatan = DocoConstants::LIST_TINDAKAN_KEPERAWATAN;
        }
        else if($jenis_kegiatan == 1){
            $jenis_kegiatan = DocoConstants::LIST_TINDAKAN_KEBIDAHANAN;
        }
        $page = isset($get['page']) ? $get['page'] : 1;
       
        return $this->helper->guzzleExec($this->serviceRest, [
            'method' => 'get',
            'url' => 'nursing-note/get-kegiatan-keperawatan',
            'payload' => [
                'query' => [
                    'jenis_kegiatan' => $jenis_kegiatan,
                    'page'         => $page,
                    'cari'         => $cari,
                ]
            ],
            'returnResponse' => true
         ]);
     }

     public function actionSaveNursingNote($id,$pasienadmisi_id = null, $catatankeperawatan_id=null){
        $payload = Yii::$app->request->post('NursingNoteForm');
        $tabType = Yii::$app->request->get('pasienadmisi_id', null);
        $this->path();
        $model = new NursingNoteForm;
        $model->tanggal = $payload['tanggal'];
        $model->jam = $payload['jam'];
        $model->kegiatan_perawat = !empty($payload['kegiatan_perawat']) ? $payload['kegiatan_perawat'] : '';
        $model->catatan = isset($payload['catatan']) ? $payload['catatan'] : ' - ';
        $model->attributes = $payload;
        $userIdentity = Yii::$app->session->get('user_identity');
        $model->nama_pegawai = $userIdentity['id_pegawai'];
        if (!$model->validate()) {
            return $this->responseJson(422, 'Silakan cek kembali form.', $this->mapErrorForm($model->errors, 'NursingNoteForm', null, [
                    'kegiatan_perawat' => [
                        'is_multiple' => true
                    ]
                ])
            );
        } else if (empty($id)) {
            return $this->responseJson(400, 'ID Pendaftaran tidak boleh kosong');
        }else{
          $result = $this->guzzleExec($this->serviceRest, [
                'url' => 'nursing-note/save-nursing-note',
                'method' => 'post',
                'payload' => [
                    'query' => [
                        'id' => $id,
                        'pasienadmisi_id' => $pasienadmisi_id,
                        'catatankeperawatan_id' => $catatankeperawatan_id,
                    ],
                    'form_params' => [
                        'NursingNoteForm' => $model->attributes
                    ]
                ],
            ]);
            return $this->helper->response($result, 200);
        }
     }

}


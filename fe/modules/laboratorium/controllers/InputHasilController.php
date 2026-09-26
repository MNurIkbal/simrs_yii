<?php

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Laboratorium
 * @copyright 13 Juli 2018 aweutist
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use Doco\laboratorium\models\InputHasilForm;
use Doco\laboratorium\models\UploadForm;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class InputHasilController extends DocoController
{
    protected $_title = "Hasil Pemeriksaan Laboratorium";
    protected $_module = '/laboratorium/hasil-lab';
    protected $_restLab;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
    }

    public function actionIndex($id, $penunjang_id)
    {
        $model = new InputHasilForm;
        $modelUpload = new UploadForm;
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $cache = Yii::$app->cache;
        $title = $this->_title;
        $data = $this->getDataApi($penunjang_id, $id);
        $pasienpenunjang_id = DocoHelpers::decrypt($penunjang_id);
        $sample_id = DocoHelpers::decrypt($id);
        $data_pasien = !empty($data['data_pasien']) ? $data['data_pasien'] : [];
        $detail_gol_umur = !empty($data['detail_gol_umur']) ? $data['detail_gol_umur'] : [];
        $catatan = !empty($data['data_hasil_lab']['catatan_instruksi'])
            ? $data['data_hasil_lab']['catatan_instruksi']
            : '';
        $nama_sample = !empty($data['data_hasil_lab']['nama_sample'])
            ? $data['data_hasil_lab']['nama_sample']
            : '';
        $tgl_ambilsample = !empty($data['data_hasil_lab']['tgl_ambilsample'])
            ? $data['data_hasil_lab']['tgl_ambilsample'] . ' ' . $data['data_hasil_lab']['jam_ambilsample']
            : date('Y-m-d H:i:s');
        $data_dokter = !empty($data['data_dokter'])
            ? ArrayHelper::map($data['data_dokter'], 'pegawai_id', 'nama_pegawai')
            : [];
        $pasien_id = !empty($data['header_gol_umur']['pasien_id'])
            ? $data['header_gol_umur']['pasien_id']
            : null;
        $pendaftaran_id = !empty($data['header_gol_umur']['pendaftaran_id'])
            ? $data['header_gol_umur']['pendaftaran_id']
            : null;
        $pasienadmisi_id = !empty($data['header_gol_umur']['pasienadmisi_id'])
            ? $data['header_gol_umur']['pasienadmisi_id']
            : null;
        $hasilpemeriksaanlab_id = !empty($data['data_hasil']['hasilpemeriksaanlab_id'])
            ? $data['data_hasil']['hasilpemeriksaanlab_id']
            : null;
        $tgl_hasilpemeriksaanlab = !empty($data['data_hasil']['tgl_hasilpemeriksaanlab'])
            ? date('d F Y H:i:s', strtotime($data['data_hasil']['tgl_hasilpemeriksaanlab']))
            : date('d F Y H:i:s');
        $is_kritis = ($data['data_hasil']['is_kritis'])
            ? 'checked'
            : '';
        $expertise = !empty($data['data_hasil']['expertise'])
            ? $data['data_hasil']['expertise']
            : '';
        $file = !empty($data['data_hasil']['upload_file'])
            ? $data['data_hasil']['upload_file']
            : '';
        $penanggungjawab_id = !empty($data['data_hasil']['pegawailab_id'])
            ? $data['data_hasil']['pegawailab_id']
            : $data['data_pasien']['pegawai_id'];

        $petugas_id = !empty($data['data_hasil']['pegawailab_id'])
            ? $data['data_hasil']['pegawailab_id']
            : $data['data_pasien']['pegawai_id'];

        $no_lab = !empty($data['data_hasil']['nohasilperiksalab'])
            ? $data['data_hasil']['nohasilperiksalab']
            : '';

        return $this->render('index', get_defined_vars());
    }

    private function getDataApi($id = null, $sample_id)
    {
        $id = DocoHelpers::decrypt($id);
        $sample_id = DocoHelpers::decrypt($sample_id);
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try {
            $response = $this->_restLab->request('GET', 'input-hasil/generate-api?id=' . $id . '&ruangan_id=' . $ruangan_id . '&sample_id=' . $sample_id);
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_pasien' => $body['response']['data-pasien'],
                'data_hasil_lab' => $body['response']['data-hasil-lab'],
                'header_gol_umur' => $body['response']['header-gol-umur'],
                'detail_gol_umur' => $body['response']['detail-gol-umur'],
                'data_dokter' => $body['response']['data-dokter'],
                'data_hasil' => $body['response']['data-hasil'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionUpload()
    {
        $model = new UploadForm();
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        $file = UploadedFile::getInstance($model, "upload_file");
        if (isset($file->name)) {
            $path = \Yii::getAlias('@webroot');
            $ext = end(explode(".", $file->name));
            $size = $file->size;
            $model->hasilpemeriksaanlab_id = $post['UploadForm']['hasilpemeriksaanlab_id'];
            $model->pasien_id = $post['UploadForm']['pasien_id'];
            $model->pasienmasukpenunjang_id = $post['UploadForm']['pasienmasukpenunjang_id'];
            $model->pendaftaran_id = $post['UploadForm']['pendaftaran_id'];
            $model->samplelab_id = $post['UploadForm']['samplelab_id'];
            $model->upload_file =  $file->name;
            if ($size > DocoConstants::MAX_UPLOAD_LAB) {
                $result['response']['message'] = Yii::t('fe', 'File maksimal 100 mb !');

                return DocoHelpers::response($result, 500);
            }
            $path = $path.'/media/input-hasil-lab/' .  $model->pendaftaran_id . '/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            if (($ext == 'jpg') OR ($ext == 'png') OR ($ext == 'pdf') OR ($ext == 'doc') OR ($ext == 'docx')) {
                if ($file->saveAs($path . $model->upload_file)) {
                    $response = $this->_restLab->request('POST', 'input-hasil/upload', [
                        'form_params' => $model->attributes
                    ]);
                    $response = json_decode($response->getBody(), true);
                    $response['response'] = [
                        'title' => 'Proses Berhasil !',
                        'text' => 'Data berhasil diupload',
                        'file' => $model->upload_file
                    ];
                }
            } else {
                $response = $model->errors;
            }
        } else {
            $response = $model->errors;
        }

        return DocoHelpers::response($response, 422, 'UploadForm');
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $data = $post['InputHasilForm'];
            if (strpos(substr($data['expertise'], 0, 1), ' ') !== FALSE) {
                $result['response']['title'] = Yii::t('fe', 'Perhatian !');
                $result['response']['text'] = Yii::t('fe', 'Expertise mengandung spasi di awal kata');
                return DocoHelpers::response($result, 422);
            }

            $dataPost = [
                'pasien_id' => $data['pasien_id'],
                'pasienadmisi_id' => $data['pasienadmisi_id'],
                'pasienmasukpenunjang_id' => $data['pasienpenunjang_id'],
                'samplelab_id' => $data['samplelab_id'],
                'hasilpemeriksaanlab_id' => $data['hasilpemeriksaanlab_id'],
                'pendaftaran_id' => $data['pendaftaran_id'],
                'pasienadmisi_id' => $data['pasienadmisi_id'],
                'tanggal' => DocoHelpers::convertIndoToEnglish($data['tanggal'], true),
                'pegawailab_id' => $data['penanggungjawab'],
                'is_kritis' => $data['is_kritis'],
                'expertise' => Html::encode($data['expertise']),
                'daftartindakan_id' => !empty($data['daftartindakan_id']) ? $data['daftartindakan_id'] : [],
                'hasil' => !empty($data['hasil_pemeriksaan']) ? $data['hasil_pemeriksaan'] : [],
                'nilairujukan_id' => !empty($data['nilairujukan_id']) ? $data['nilairujukan_id'] : [],
                'petugaslab_id' => !empty($data['petugaslab_id']) ? $data['petugaslab_id'] : [],
                'tindakanpaket_id' => !empty($data['tindakanpaket_id']) ? $data['tindakanpaket_id'] : [],
                'pemeriksaanlab_id' => !empty($data['pemeriksaanlab_id']) ? $data['pemeriksaanlab_id'] : [],
                'nilai_rujukan' => !empty($data['nilai_rujukan']) ? $data['nilai_rujukan'] : [],
                'satuan_hasil' => !empty($data['satuan_hasil']) ? $data['satuan_hasil'] : [],
                'is_verifikasi' => $data['is_verifikasi_filtered'],
                'keterangan' => !empty($data['keterangan']) ? $data['keterangan'] : [],
            ];

            $response = $this->_restLab->request('POST', 'input-hasil/save', [
                'form_params' => $dataPost
            ]);

            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            echo DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

}
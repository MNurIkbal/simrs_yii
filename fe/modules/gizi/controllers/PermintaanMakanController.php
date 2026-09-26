<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-03 16:11:00
 */

namespace Doco\gizi\controllers;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use GuzzleHttp\Exception\RequestException;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use app\modules\gizi\models\PermintaanMakanForm;

class PermintaanMakanController extends DocoController
{
    /**
     * @todo Protected vars
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    protected $_restGizi;
    protected $allowAction = ['*'];

    /**
     * @todo Init function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function init()
    {
        parent::init();
        $this->_restGizi = Yii::$app->docoRest->gizi;
    }

    /**
     * @todo Behaviors function
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    /**
     * @todo Fungsi untuk menampilkan halaman ubah permintaan makan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionUpdate($id)
    {
        try {
            $decrypted_id = DocoHelpers::decrypt($id);
            $modelDetailMakan = new PermintaanMakanForm;
            if (Yii::$app->request->post()) {
                $post[] = Yii::$app->request->post('PermintaanMakanForm');
                if (!empty($post)) {
                    $post[0]['puasa_tgl_awal'] = !empty($post[0]['puasa_tgl_awal']) ? date_format(date_create_from_format('d/m/Y H:i:s', $post[0]['puasa_tgl_awal']), 'Y-m-d H:i:s') : '' ;
                    $post[0]['puasa_tgl_akhir'] = !empty($post[0]['puasa_tgl_akhir']) ? date_format(date_create_from_format('d/m/Y H:i:s', $post[0]['puasa_tgl_akhir']), 'Y-m-d H:i:s') : '' ;
                    $post[0]['puasa_operasi_awal'] = !empty($post[0]['puasa_operasi_awal']) ? date_format(date_create_from_format('d/m/Y H:i:s', $post[0]['puasa_operasi_awal']), 'Y-m-d H:i:s') : '' ;
                    $post[0]['puasa_operasi_akhir'] = !empty($post[0]['puasa_operasi_akhir']) ? date_format(date_create_from_format('d/m/Y H:i:s', $post[0]['puasa_operasi_akhir']), 'Y-m-d H:i:s') : '' ;
                    $post[0]['buka_puasa'] = !empty($post[0]['buka_puasa']) ? date_format(date_create_from_format('d/m/Y H:i:s', $post[0]['buka_puasa']), 'Y-m-d H:i:s') : '' ;
                    $post[0]['form_ubah'] = 1;
                    foreach ($post as $value) {
                        $modelDetailMakan = new PermintaanMakanForm;
                        $modelDetailMakan->attributes = $value;

                        if(!$modelDetailMakan->validate()) {
                            return DocoHelpers::response($modelDetailMakan->errors, 422, 'PermintaanMakanForm');
                        }

                        $detailPermintaanMakan[] = $value;
                    }
                    $restGizi = $this->_restGizi->post('transaksi-permintaan-makan/ubah-permintaan-makan?id='.$decrypted_id, [
                        'form_params' => [
                            'pegawai_pemesan' => Yii::$app->session->get('user_identity')['id_pegawai'],
                            'detailPermintaanMakan' => $post,
                        ]
                    ]);
                    $response = json_decode($restGizi->getBody(), true);

                    return DocoHelpers::response($response, false);
                } else {
                    $message = Yii::t('fe', 'Tidak ada data yang disimpan');
                    return DocoHelpers::responseTemplate(
                        500,
                        'Error',
                        [],
                        [
                            'title' => Yii::t('fe', 'Proses Gagal'),
                            'text' => $message,
                            'message' => $message,
                        ]
                    );
                }
            } else {
                $restGizi = $this->_restGizi->get('transaksi-permintaan-makan/get-data-permintaan-makan?id='.$decrypted_id);
                $response = json_decode($restGizi->getBody(), true);
                $data = $response['response'];

                $dataWaktuDiet = [];
                if(isset($data['waktu_diet'])){
                    $dataWaktuDiet = $data['waktu_diet'];
                }
                $dropdownWaktuDiet= [];
                $dropdownWaktuDiet[] = [
                    'id' => '-1',
                    'text' => 'Pilih Waktu Diet'
                ];

                if(isset($data['detail_permintaan_makan']['keterangan'])){
                    $modelDetailMakan->keterangan = $data['detail_permintaan_makan']['keterangan'];
                }else if(isset($data['permintaan_makan']['catatan_diet'])){
                    $modelDetailMakan->keterangan = $data['permintaan_makan']['catatan_diet'];
                }
                $j_diet = $m_diet = $p_diet = $d_tindakan = 0;


                if(!empty($data['detail_permintaan_makan'][0])){
                $modelDetailMakan->attributes = $data['detail_permintaan_makan'][0];

                foreach($data['detail_permintaan_makan'] as $key => $value){
                    if(isset($value['jenisdiet_id'])){
                        $j_diet = $value['jenisdiet_id'];
                    }
                    if(isset($value['makanandiet_id'])){
                        $m_diet = $value['makanandiet_id'];
                    }
                    if(isset($value['perubahan_diet'])){
                        $p_diet = $value['perubahan_diet'];
                    }
                    if(isset($value['daftartindakan_id'])){
                        $d_tindakan = $value['daftartindakan_id'];
                    }
                    $modelDetailMakan->puasa_tgl_awal = !empty($value['puasa_tgl_awal']) ? date('d/m/Y H:i:s', strtotime($value['puasa_tgl_awal'])) : null ;
                    $modelDetailMakan->puasa_tgl_akhir = !empty($value['puasa_tgl_akhir']) ? date('d/m/Y H:i:s', strtotime($value['puasa_tgl_akhir'])) : null ;
                    $modelDetailMakan->puasa_operasi_awal = !empty($value['puasa_operasi_awal']) ? date('d/m/Y H:i:s', strtotime($value['puasa_operasi_awal'])) : null ;
                    $modelDetailMakan->puasa_operasi_akhir = !empty($value['puasa_operasi_akhir']) ? date('d/m/Y H:i:s', strtotime($value['puasa_operasi_akhir'])) : null ;
                    $modelDetailMakan->buka_puasa = !empty($value['buka_puasa']) ? date('d/m/Y H:i:s', strtotime($value['buka_puasa'])) : null ;
                }
                }

                if(is_array($dataWaktuDiet) && count($dataWaktuDiet) > 0) {
                    foreach ($dataWaktuDiet as $val_waktu_diet) {
                        $dropdownWaktuDiet[] = [
                            'id' => $val_waktu_diet['lookup_id'],
                            'text' => $val_waktu_diet['lookup_name']
                        ];
                    }
                }

                if(isset($data['jenis_diet'])){
                    $dataJenisDiet = $data['jenis_diet'];
                }

                if(isset($data['menu_diet'])){
                    $menu_diet = $data['menu_diet'];
                }

                $perubahan_diet = [];
                if(isset($data['perubahan_diet'])){
                    $perubahan_diet = $data['perubahan_diet'];
                }
                $dropdownPerubahanDiet= [];
                $dropdownPerubahanDiet[] = [
                    'id' => '0',
                    'text' => 'Pilih Perubahan Diet',
                    'disabled'=>'disabled',
                    'selected'=>'selected',
                ];

                if(is_array($perubahan_diet) && count($perubahan_diet) > 0) {
                    foreach ($perubahan_diet as $val_perubahan_diet) {
                        $dropdownPerubahanDiet[] = [
                            'id' => $val_perubahan_diet['lookup_id'],
                            'text' => $val_perubahan_diet['lookup_name']
                        ];
                    }
                }

                return $this->render('form', [
                    'id' => $id,
                    'pendaftaran_id' => DocoHelpers::encrypt($data['data_pasien']['pendaftaran_id']),
                    'data_pasien' => $data['data_pasien'] != '' ? $data['data_pasien'] : [],
                    'permintaan_makan' => $data['permintaan_makan'] != '' ? $data['permintaan_makan'] : [],
                    'permintaan_makan_detail' => !empty($data['permintaan_makan_detail']) ? $data['permintaan_makan_detail'] : [],
                    'dropdownWaktuDiet' => json_encode($dropdownWaktuDiet),
                    'jenisDiet' => json_encode($dataJenisDiet),
                    'listWaktuDiet' => $dropdownWaktuDiet,
                    'listJenisDiet' => $dataJenisDiet,
                    'model' => $modelDetailMakan,
                    'menu_diet' => json_encode($menu_diet),
                    'perubahan_diet' => json_encode($dropdownPerubahanDiet),
                    'j_diet' => $j_diet,
                    'm_diet' => $m_diet,
                    'p_diet' => $p_diet,
                    'd_tindakan' => $d_tindakan,

                ]);
            }
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        }
    }
}

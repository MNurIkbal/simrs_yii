<?php

namespace Doco\Services;

use Doco\components\DocoConstants;
use Doco\Services\BaseService;

use Yii;
use yii\helpers\ArrayHelper;

class KasirService extends BaseService
{
    public function __construct()
    {
        $this->service = Yii::$app->docoRest->kasir;
    }

    /**
     * Tagihan
     *
     * @param Array $registrationData
     * @param Array $actionDetails
     * @return Array/Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function tagihan($registrationData, $actionDetails, $type = 'tindakan')
    {
        $registrationData['kelaspelayanan_id'] = isset($registrationData['kelas_pelayanan_id']) ? $registrationData['kelas_pelayanan_id'] : ((isset($registrationData['kelaspelayanan_id']) ? $registrationData['kelaspelayanan_id'] : null));
        if (empty($registrationData['kelaspelayanan_id'])) {
            throw new \Exception("Kelas pelayanan pada integrasi API Billing tidak boleh kosong.", 1);
        }
        $registrationData['ruangan_id'] = isset($registrationData['ruangan_id']) && !empty($registrationData['ruangan_id']) ? $registrationData['ruangan_id'] : Yii::$app->jwt->ruangan_id;
        $registrationData['instalasi_id'] =  isset($registrationData['instalasi_id']) && !empty($registrationData['instalasi_id']) ? $registrationData['instalasi_id'] : Yii::$app->jwt->instalasi_id;
        $registrationData['tgl_transaksi'] = isset($registrationData['is_soap']) && $registrationData['is_soap'] ? $registrationData['tgl_transaksi'] : date("Y-m-d H:i:s");
        
        $resultDetail = [];
        foreach ($actionDetails as $detail) {
            /** Kebutuhan MCU */
            // if (isset($detail['daftartindakan_id']) && !empty($detail['daftartindakan_id'])) {
                $is_dokter_operator = false;
                if(isset($detail['kode_posisi']) && !empty($detail['kode_posisi'])) {
                    if($detail['kode_posisi'] == DocoConstants::TIM_OPERASI_DOKTER_BEDAH) {
                        $is_dokter_operator = true;
                    }
                }
                $resultDetail[] = [
                    'pasienmasukpenunjang_id' => isset($detail['pasienmasukpenunjang_id']) && !empty($detail['pasienmasukpenunjang_id']) ? $detail['pasienmasukpenunjang_id'] : null,
                    'implementasi_id' => isset($detail['implementasi_id']) && !empty($detail['implementasi_id']) ? $detail['implementasi_id'] : null,
                    'instruksitindakan_id' => isset($detail['instruksitindakan_id']) && !empty($detail['instruksitindakan_id']) ? $detail['instruksitindakan_id'] : null,
                    'dokter_id' => isset($detail['dokter_id']) && !empty($detail['dokter_id']) ? $detail['dokter_id'] : null,
                    'perawat_id' => isset($detail['perawat_id']) && !empty($detail['perawat_id']) ? $detail['perawat_id'] : null,
                    'perawat2_id' => isset($detail['perawat2_id']) && !empty($detail['perawat2_id']) ? $detail['perawat2_id'] : null,
                    'tipepaket_id' => isset($detail['tipepaket_id']) && !empty($detail['tipepaket_id']) ? $detail['tipepaket_id'] : null,
                    'is_cyto' => isset($detail['is_cyto']) && !empty($detail['is_cyto']) ? $detail['is_cyto'] : false,
                    'is_penyulit' => isset($detail['is_penyulit']) && !empty($detail['is_penyulit']) ? $detail['is_penyulit'] : false,
                    'qty' => isset($detail['qty']) && !empty($detail['qty']) ? $detail['qty'] : 1,
                    'daftartindakan_id' => isset($detail['daftartindakan_id']) && !empty($detail['daftartindakan_id']) ? $detail['daftartindakan_id'] : null,
                    'useprice' => isset($detail['useprice']) ? $detail['useprice'] : false,
                    'harga' => isset($detail['harga']) ? $detail['harga'] : 0,
                    'persencyto_tindakan' => isset($detail['persencyto_tindakan']) ? $detail['persencyto_tindakan'] : 0,
                    'persen_penyulit' => isset($detail['persen_penyulit']) ? $detail['persen_penyulit'] : 0,
                    'harga_cyto' => isset($detail['harga_cyto']) ? $detail['harga_cyto'] : 0,
                    'harga_penyulit' => isset($detail['harga_penyulit']) ? $detail['harga_penyulit'] : 0,
                    'persentase' => isset($detail['persentase']) ? $detail['persentase'] : 100,
                    'total_harga' => isset($detail['total_harga']) ? $detail['total_harga'] : 0,
                    'kamarruangan_id' => isset($detail['kamarruangan_id']) ? $detail['kamarruangan_id'] : null,
                    'is_dokter_operator' => $is_dokter_operator,
                    'parent_tim' => isset($detail['parent_tim']) ? $detail['parent_tim'] : null,
                    'timoperasi_id' => isset($detail['timoperasi_id']) ? $detail['timoperasi_id'] : null,
                    'programterapi_id' => isset($detail['programterapi_id']) ? $detail['programterapi_id'] : null,
                    'programterapidetail_id' => isset($detail['programterapidetail_id']) ? $detail['programterapidetail_id'] : null,
                ];
            // }
        }
        $endpoint = 'api/billing';
        switch (strtolower($type)) {
            case 'tindakan':
                $endpoint = 'api/billing';
                break;
            case 'ambulan':
                $endpoint = 'api/billing-ambulan';
                break;
            case 'penunjang':
                $endpoint = 'api/billing-penunjang';
                break;
            case 'kamar':
                $endpoint = 'api/billing-kamar';
                break;
        }
        if (!empty($resultDetail)) {
            $registrationData['detail_tindakan'] = $resultDetail;
            return $this->post($endpoint, [
                'form_params' => $registrationData,
                'success' => function ($data) use ($registrationData, $endpoint) {
                    \Yii::error(
                        'Message : API BILLING SUKSES--||--Line : NULL --||--File : KasirService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($registrationData),
                        'server-error'
                    );
                },
                'failed' => function ($data) use ($registrationData, $endpoint) {
                    \Yii::error(
                        'Message : API BILLING GAGAL--||--Line : NULL --||--File : KasirService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($registrationData),
                        'server-error'
                    );
                }
            ]);
        } else {
            \Yii::error([
                "Message" => 'Integrasi Billing gagal! Detail tindakan untuk API Billing tidak boleh kosong.'
            ]);
            return false;
        }
    }

    /**
     * This function will submit billing for ambulance
     *
     * @param Array $registrationData
     * @param Array $actionDetails
     * @return Array/Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function tagihanAmbulan($registrationData, $actionDetails)
    {
        return $this->tagihan($registrationData, $actionDetails, 'ambulan');
    }

    /**
     * This function will submit billing for penunjang
     *
     * @param Array $registrationData
     * @param Array $actionDetails
     * @return Array/Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function tagihanPenunjang($registrationData, $actionDetails)
    {
        return $this->tagihan($registrationData, $actionDetails, 'penunjang');
    }

    /**
     * This function will submit billing for kamar
     *
     * @param Array $registrationData
     * @param Array $actionDetails
     * @return Array/Boolean
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function tagihanKamar($registrationData, $actionDetails)
    {
        return $this->tagihan($registrationData, $actionDetails, 'kamar');
    }

    /**
     * This function for hit cancel procedure
     *
     * @param Array $registrationData
     * @param Array $procedureDetails
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

     public function batalTindakan($registrationData, $procedureDetails = [])
     {
        $registrationData['ruangan_id'] = isset($registrationData['ruangan_id']) && !empty($registrationData['ruangan_id']) ? $registrationData['ruangan_id'] : Yii::$app->jwt->ruangan_id;
        $registrationData['instalasi_id'] = isset($registrationData['instalasi_id']) && !empty($registrationData['instalasi_id']) ? $registrationData['instalasi_id'] : Yii::$app->jwt->instalasi_id;
        $registrationData['tgl_transaksi'] = date("Y-m-d H:i:s");
        if (!empty($procedureDetails)) {
            $registrationData['detail_tindakan'] = $procedureDetails;
        }
        $endpoint = 'api/batal-tagihan';
        return $this->post($endpoint, [
            'form_params' => $registrationData,
            'success' => function ($data) use ($registrationData, $endpoint) {
                \Yii::error(
                    'Message : API Batal Tindakan SUKSES--||--Line : NULL --||--File : KasirService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($registrationData),
                    'server-error'
                );
            },
            'failed' => function ($data) use ($registrationData, $endpoint) {
                \Yii::error(
                    'Message : API Batal Tindakan GAGAL--||--Line : NULL --||--File : KasirService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($registrationData),
                    'server-error'
                );
            }
        ]);
    }

    public function batalTagihan($registrationData, $procedureDetails = [], $reason = null)
    {
        $registrationData['ruangan_id'] = isset($registrationData['ruangan_id']) && !empty($registrationData['ruangan_id']) ? $registrationData['ruangan_id'] : Yii::$app->jwt->ruangan_id;
        $registrationData['instalasi_id'] = isset($registrationData['instalasi_id']) && !empty($registrationData['instalasi_id']) ? $registrationData['instalasi_id'] : Yii::$app->jwt->instalasi_id;
        $registrationData['tgl_transaksi'] = date("Y-m-d H:i:s");
        if (!empty($procedureDetails)) {
            $registrationData['detail_tindakan'] = ArrayHelper::getValue($procedureDetails,'tindakan');
            $registrationData['detail_obat'] = ArrayHelper::getValue($procedureDetails,'obat');
        }
        $registrationData['alasan_batal'] = $reason;
        $endpoint = 'api/batal-tagihan';
        return $this->post($endpoint, [
            'form_params' => $registrationData,
            'success' => function ($data) use ($registrationData, $endpoint) {
                \Yii::error(
                    'Message : API Batal Tindakan SUKSES--||--Line : NULL --||--File : KasirService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($registrationData),
                    'server-error'
                );
            },
            'failed' => function ($data) use ($registrationData, $endpoint) {
                \Yii::error(
                    'Message : API Batal Tindakan GAGAL--||--Line : NULL --||--File : KasirService.php --||--API URL : ' . $endpoint . '--||--Method : POST--||--Payload : ' . json_encode($registrationData),
                    'server-error'
                );
            }
        ]);
    }

    /**
     * This function for hit cancel procedure
     *
     * @param String $registrationNumber
     * @param Array $details
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

    public function editTindakan($registrationNumber, $details)
    {
        $payload  = [
            'no_pendaftaran' => $registrationNumber,
            'detail_tindakan' => $details
        ];
        return $this->post('api/edit-tindakan', [
            'form_params' => $payload,
            'success' => function () use ($payload) {
                \Yii::error(
                    'Message : API EDIT TINDAKAN SUKSES--||--Line : NULL --||--File : KasirService.php --||--API URL : /api/edit-tindakan--||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            },
            'failed' => function () use ($payload) {
                \Yii::error(
                    'Message : API EDIT TINDAKAN GAGAL--||--Line : NULL --||--File : KasirService.php --||--API URL : /api/edit-tindakan--||--Method : POST--||--Payload : ' . json_encode($payload),
                    'server-error'
                );
            }
        ]);
    }
}

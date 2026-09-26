<?php

namespace app\modules\gizi\components\traits;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Yii;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

trait CpptGiziTrait {
    public function actionCpptGizi()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id');

        $get_sort = !empty($request->get('sort')) ? $request->get('sort') : 'sort_asc';

        return $this->renderAjax('cppt-gizi/index', compact('get_sort', 'pendaftaran_id'));
    }

    public function actionCpptGiziFormAdime()
    {
        $cppt = Yii::$app->request->post('data',[]);
        return $this->renderAjax('cppt-gizi/_form_adime',compact('cppt'));
    }

    public function actionGetDataAsesmentCppt() {
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $pendaftaran_id = $request->get('id');

        $get_sort = !empty($request->get('sort')) ? $request->get('sort') : 'sort_asc';

        $sort_status = ($get_sort == 'sort_asc') ? SORT_ASC : SORT_DESC;

        $getCppt = $this->helper->guzzleExec($this->_restGizi, [
            'url' => 'cppt-gizi/get-cppt-data',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                    'pelayanan' => true
                ],
            ]
        ]);

        $cpptData = isset($getCppt['data']) ? $getCppt['data'] : [];

        if (isset($getCppt['data_pelayanan'])) {
            $dataPelayanan = [];
            foreach ($getCppt['data_pelayanan'] as $key => $value) {
                $dataPelayanan[] = [
                    'sort' => date('Y-m-d H:i:s', strtotime($value['tgl_cppt'])),
                    'tgl_cppt' => date('d-F-Y H:i:s', strtotime($value['tgl_cppt'])) . '<br>' . $value['ruangan_nama'] . ' ' . $value['no_tempattidur'] . ' ' . $value['kamarruangan_nokamar'],
                    'pegawai_nama' => $value['nama_pegawai'],
                    'hasil_asesmen' => $this->getCpptPelayanan($value),
                    'instruksi_ppa' => '-',
                    'verifikasi' => ''
                ];
            }

            $cpptData = array_merge($cpptData, $dataPelayanan);
            array_multisort(array_column($cpptData, 'sort'), $sort_status, $cpptData);
        }

        $query = $cpptData;

        $data_query = [];
        $no = 1;
        foreach ($query as $key => $value) {
            $data_query[] = [
                'rowNum'          => $no,
                'sort'            => isset($value['sort']) ? $value['sort'] : null,
                'tgl_cppt'        => isset($value['tgl_cppt']) ? $value['tgl_cppt'] : null,
                'pegawai_nama'    => isset($value['pegawai_nama']) ? $value['pegawai_nama'] : null,
                'hasil_asesmen'   => isset($value['hasil_asesmen']) ? $value['hasil_asesmen'] : null,
                'instruksi_ppa'   => isset($value['instruksi_ppa']) ? $value['instruksi_ppa'] : null,
                'verifikasi'      => isset($value['verifikasi']) ? $value['verifikasi'] : null,
                'form_asesmen'    => isset($value['form_asesmen']) ? $value['form_asesmen'] : null
            ];

            $no++;
        }

        $count = count($query);
        $result = json_encode([
            'draw' => $draw,
            'recordsFiltered' => $count,
            'recordsTotal' => $count,
            'data' => $data_query
        ]);

        return $result;
    }

    private function getCpptPelayanan($data)
    {
        $html = '<div class="wrapper"><table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">';

        // Cek subjek
        if (isset($data['subject']) && $data['subject'] != '') {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Subjektif :</b><br/>' . $data['subject'] . '</td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">'.$data['subject'].'</td>';
            // $html .= '</tr>';
        }

        // Cek objek
        if (isset($data['object']) && $data['object'] != '') {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Objektif :</b><br/>' . $data['object'] . '</td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">'.$data['object'].'</td>';
            // $html .= '</tr>';
        }

        // Cek subject
        if (($data['subject'] != '') && ($data['object'] != '') && ($data['planning'] != '')) {
            // Cek asesmen
            if (isset($data['a_diag_utama']) && $data['a_diag_utama'] != '') {
                $diag_utama = json_decode($data['a_diag_utama'],TRUE);
                // $diag_utama = $data['a_diag_utama'];
                // Set html
                $html .= '<tr style="line-height:130%;">';
                $html .= '<td><b>Asesmen Diagnosa Utama :</b><br/>' . @$diag_utama['text'] . '</td>';
                $html .= '</tr>';
                // $html .= '<tr>';
                // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">'.@$diag_utama['text'].'</td>';
                // $html .= '</tr>';
            }

            // Cek asesmen
            if (isset($data['a_diag_penyerta']) && $data['a_diag_penyerta'] != '') {
                // Encode
                $diagnosaPenyerta = json_decode($data['a_diag_penyerta'],TRUE);
                // $diagnosaPenyerta = $data['a_diag_penyerta'];

                // Cek diagnosa
                if ($diagnosaPenyerta != '') {
                    // Inisialisasi counter
                    $counter = 0;

                    // Loop
                    $html .= '<tr style="line-height:130%;">';
                    $html .= '<td><b>' . Yii::t('fe', 'Diagnosa Penyerta') . '</b><br/>';
                    foreach ($diagnosaPenyerta as $valueDiagnosaPenyerta) {
                        // Cek counter
                        $html .= '&nbsp;&nbsp;&nbsp;- ' . @$valueDiagnosaPenyerta['text'] . '<br/>';
                        // $html .= '<td style="padding-left:8px; display: inline-block; word-break: break-word; white-space: initial;">- '.@$valueDiagnosaPenyerta['text'].'</td>';
                        // $html .= '</tr>';

                        // Plus the counter
                        $counter++;
                    }
                    $html .= '</td></tr>';
                }
                else {
                    // Set strip
                    $html .= '<td>-</td>';
                }

                // Close tag
                $html .= '</tr>';
            }
        }
        else {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">' . $data['instruksi'] . '<br>' . $data['pegawai_instruksi'] . '</td>';
            $html .= '</tr>';
        }

        // Cek planning
        if (isset($data['planning']) && $data['planning'] != '') {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Planning</b><br/>' . $data['planning'] . '</td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">'.$data['planning'].'</td>';
            // $html .= '</tr>';
        }

        // Cek catatan dokter
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != null) {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="vertical-align: top;">' . Yii::t('fe', 'Catatan') . '<br/>' . $data['catatan_dokter'] . '</td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">'.$data['catatan_dokter'].'</td>';
            // $html .= '</tr>';
        }

        // Cek catatan perawat
        if (isset($data['catatan_perawat']) && $data['catatan_perawat'] != null) {
            // Set html
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td style="vertical-align: top;">' . Yii::t('fe', 'Catatan') . '<br/>' . $data['catatan_perawat'] . '</td>';
            $html .= '</tr>';
            // $html .= '<tr>';
            // $html .= '<td style="display: inline-block; word-break: break-word; white-space: initial;">'.$data['catatan_perawat'].'</td>';
            // $html .= '</tr>';
        }

        // Set end tag html
        $html .= '</table></div>';

        // Return
        return $html;
    }

    public function actionSimpanAdime()
    {
        $request = Yii::$app->request;
        if(Yii::$app->request->post()) {
            $pendaftaran_id = DocoHelpers::decrypt($request->get('id','MQ'));
            $post = $request->post();
            $post['asesmen_gizi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'asesmen_gizi'));
            $post['diagnosa_gizi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'diagnosa_gizi'));
            $post['intervensi_gizi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'intervensi_gizi'));
            $post['monitoring'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'monitoring'));
            $post['evaluasi'] = DocoHelpers::purifyText(ArrayHelper::getValue($post, 'evaluasi'));
            $post['pendaftaran_id'] = $pendaftaran_id;
            try{
                $result = $this->_restGizi->post('cppt-gizi/simpan-adime',[
                    'form_params'=>[
                        'formdata' => $post
                    ]
                ]);
                $result = json_decode($result->getBody(),TRUE);
                return DocoHelpers::response($result, false);
            }catch(RequestException $e){
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            }
        }
    }
}

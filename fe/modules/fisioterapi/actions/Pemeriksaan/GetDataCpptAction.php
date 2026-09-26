<?php

namespace Doco\fisioterapi\actions\Pemeriksaan;

use Yii;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;

class GetDataCpptAction extends BaseCurrentAction
{
    public static function generateHtmlPenatalaksanaan($data)
    {
        $aDiagUtama = ArrayHelper::getValue($data, 'a_diag_utama');
        $aDiagPenyerta = ArrayHelper::getValue($data, 'a_diag_penyerta');
        $aDiagUtama = json_decode($aDiagUtama, true);
        $aDiagPenyerta = json_decode($aDiagPenyerta, true);
        $diagnosaFungsi = ArrayHelper::getValue($data, 'diagnosa_fungsi', '{}');
        $diagnosaFungsi = json_decode($diagnosaFungsi, true);
        $html = '<div class="wrapper"><table border="0" cellpadding="0" cellspacing="0" style="width: 100%; border-collapse: collapse;">';
        if (isset($data['subject']) && $data['subject'] != '') {
            $purifiedData = DocoHelpers::purifyHtml($data['subject']);
            $html .= '<tr style="line-height:130%;">';
            $html .= "<td><b>Subjektif :</b><br>$purifiedData</td>";
            $html .= '</tr>';
        }
        if (isset($data['object']) && $data['object'] != '') {
            $purifiedData = DocoHelpers::purifyHtml($data['object']);
            $html .= '<tr style="line-height:130%;">';
            $html .= "<td><b>Objektif :</b><br/>$purifiedData</td>";
            $html .= '</tr>';
        }
        if ($aDiagUtama || $aDiagPenyerta || $diagnosaFungsi) {
            $aDiagUtamaText = ArrayHelper::getValue($aDiagUtama, 'text', '-');
            $purifiedData = DocoHelpers::purifyHtml($data['assesment']);
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Assesment :</b> <br/>';
            $html .= "Diagnosa Utama : <br/> $aDiagUtamaText <br/>";
            $html .= "Diagnosa Penyerta : </br>";
            if (!$aDiagPenyerta) $aDiagPenyerta = [];
            foreach ($aDiagPenyerta as $key => $value) {
                $aDiagPenyertaText = ArrayHelper::getValue($value, 'text', '-');
                $html .= "$aDiagPenyertaText </br>";
            }
            $html .= "Diagnosa Fungsi : </br>";
            if (!$diagnosaFungsi) $diagnosaFungsi = [];
            foreach ($diagnosaFungsi as $key => $value) {
                $diagnosaFungsiText = ArrayHelper::getValue($value, 'text', '-');
                $html .= "$diagnosaFungsiText </br>";
            }
            $html .= '</td></tr>';
        }
        if (isset($data['planning']) && $data['planning'] != '') {
            $purifiedData = DocoHelpers::purifyHtml($data['planning']);
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Planning :</b> <br/>';
            $html .= $purifiedData;
            $html .= '</td></tr>';
        }
        if(isset($data['prosedur_kerja']) && $data['prosedur_kerja'] != '') {
            $prosedurKerja = ArrayHelper::getValue($data, 'prosedur_kerja', '{}');
            $prosedurKerja = json_decode($prosedurKerja, true);
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Tindakan/Prosedur:</b> <br/>';
            foreach ($prosedurKerja as $key => $value) {
                $prosedurKerjaText = ArrayHelper::getValue($value, 'text', '-');
                $html .= "$prosedurKerjaText </br>";
            }
            $html .= '</td></tr>';
        }
        if (isset($data['goal']) && $data['goal'] != '') {
            $purifiedData = DocoHelpers::purifyHtml($data['goal']);
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Goal:</b> <br/>';
            $html .= $purifiedData;
            $html .= '</td></tr>';
        }
        if (isset($data['catatan_dokter']) && $data['catatan_dokter'] != '') {
            $purifiedData = DocoHelpers::purifyHtml($data['catatan_dokter']);
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Catatan Penunjang :</b> <br/>';
            $html .= $purifiedData;
            $html .= '</td></tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $program_terapi_id = $request->get('program_terapi_id');
        $isReadOnly = $request->get('readonly');
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $urlGet = 'soap/get-soap?' . http_build_query($yiiRestfulParams);
        $urlGet .= '&pendaftaran_id=' . $pendaftaran_id;
        $urlGet .= '&program_terapi_id=' . $program_terapi_id;
        try {
            $response = Yii::$app->docoRest->fisioterapi->get($urlGet);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $index = $no - 1;
                $primaryKey = DocoHelpers::encrypt($value['soapfisioterapi_id']);
                $value['no'] = $no;
                $value['soapfisioterapi_id'] = $primaryKey;
                $soapfisioterapi_id_enc = $primaryKey;
                $value['ruangan'] = $value['ruangan_nama'] . '<br>' . $value['tgl_soapfisioterapi'] . '<br>' . $value['terapis_nama'];
                $value['penatalaksanaan'] = self::generateHtmlPenatalaksanaan($value);
                $value['verifikasi'] = '';
                $value['rowNum'] = $no;
                $isEdit = ArrayHelper::getValue($value, 'is_edit');
                $terapisId = ArrayHelper::getValue($value, 'terapis_id');
                $lastModifiedByName = ArrayHelper::getValue($value, 'last_modified_by_name');
                $lastModifiedByDate = ArrayHelper::getValue($value, 'last_modified_date');
                $isFisio = ArrayHelper::getValue($value, 'is_fisio');
                $instalasiId = ArrayHelper::getValue($value, 'instalasi_id');
                $paramAction = [
                    'soapfisioterapi_id_enc' => $soapfisioterapi_id_enc,
                    'pendaftaran_id' => $pendaftaran_id,
                    'program_terapi_id' => $program_terapi_id,
                    'index' => $index,
                    'primaryKey' => $primaryKey,
                    'pendaftaran_id' => $pendaftaran_id,
                    'program_terapi_id' => $program_terapi_id,
                    'isReadOnly' => $isReadOnly,
                    'isEdit' => $isEdit,
                    'lastModifiedByName' => $lastModifiedByName,
                    'lastModifiedByDate' => $lastModifiedByDate,
                    'terapisId' => $terapisId,
                    'isFisio' => $isFisio,
                    'instalasiId' => DocoHelpers::encrypt($instalasiId)
                ];
                $value['action'] = self::permissionAction($paramAction);
                $aDiagUtama = ArrayHelper::getValue($value, 'a_diag_utama');
                $aDiagPenyerta = ArrayHelper::getValue($value, 'a_diag_penyerta');
                $value['a_diag_utama'] = json_decode($aDiagUtama, true);
                $value['a_diag_penyerta'] = json_decode($aDiagPenyerta, true);
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = count($data);
            $result['recordsFiltered'] = count($data);
            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            Yii::error($e->getTrace());
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    /**
     * @method permissionAction
     * @param Array $paramAction
     * @return String
     */
    private static function permissionAction($paramAction)
    {
        $soapfisioterapi_id_enc = $paramAction['soapfisioterapi_id_enc'];
        $pendaftaran_id = $paramAction['pendaftaran_id'];
        $program_terapi_id = $paramAction['program_terapi_id'];
        $index = $paramAction['index'];
        $primaryKey = $paramAction['primaryKey'];
        $pendaftaran_id = $paramAction['pendaftaran_id'];
        $program_terapi_id = $paramAction['program_terapi_id'];
        $isReadOnly = $paramAction['isReadOnly'];
        $isEdit = $paramAction['isEdit'];
        $user = Yii::$app->user->getIdentity();
        $loginPemakaiId = ArrayHelper::getValue($user, 'id_pegawai');
        $lastModifiedByName = $paramAction['lastModifiedByName'];
        $lastModifiedByDate = $paramAction['lastModifiedByDate'];
        $terapisId = $paramAction['terapisId'];
        $isFisio = $paramAction['isFisio'];
        $instalasiId = $paramAction['instalasiId'];

        $urlEdit = "/fisioterapi/pemeriksaan/update-soap?soapfisioterapi_id=$soapfisioterapi_id_enc";
        $urlBatalEdit = "/fisioterapi/pemeriksaan/store-soap?pendaftaran_id=$pendaftaran_id&program_terapi_id=$program_terapi_id";        
        $buttonBatalEdit = "<button class='btn btn-danger btn-labeled btn-xs action-cppt batal-edit-cppt hidden' data-index='$index' data-soapfisioterapi_id='$primaryKey' data-url-target='$urlBatalEdit' data-pendaftaran_id='$pendaftaran_id' data-program_terapi_id='$program_terapi_id'><b><i class='fa fa-close'></i></b> Batal Edit</button>";
        
        if ($isReadOnly) {
            $action = "<div class='tooltip-wrapper' data-instalasi='$instalasiId' data-toggle='tooltip' title='Aktif Ketika Diperiksa'><button class='btn btn-info btn-labeled btn-xs disabled'><b><i class='fa fa-edit'></i></b> Edit</button></div>";
        } else if ($loginPemakaiId != $terapisId) {
            $action = "<div class='tooltip-wrapper' data-instalasi='$instalasiId' data-toggle='tooltip' title='Anda Tidak Punya Akses'><button class='btn btn-info btn-labeled btn-xs disabled'><b><i class='fa fa-edit'></i></b> Edit</button></div>";
        } else if ($loginPemakaiId == $terapisId) {
            if ($isFisio) {
                $action = "<button class='btn btn-info btn-labeled btn-xs action-cppt edit-cppt' data-instalasi='$instalasiId' data-index='$index' data-soapfisioterapi_id='$primaryKey' data-url-target='$urlEdit'><b><i class='fa fa-edit'></i></b> Edit</button>";
            } else {
                $action = "<div class='tooltip-wrapper' data-instalasi='$instalasiId' data-toggle='tooltip' title='Anda Tidak Punya Akses'><button class='btn btn-info btn-labeled btn-xs disabled'><b><i class='fa fa-edit'></i></b> Edit</button></div>";
            }
        } else {
            $action = "<button class='btn btn-info btn-labeled btn-xs action-cppt edit-cppt' data-instalasi='$instalasiId' data-index='$index' data-soapfisioterapi_id='$primaryKey' data-url-target='$urlEdit'><b><i class='fa fa-edit'></i></b> Edit</button>";
        }

        if ($isEdit) {
            $action = 'Data sudah diubah oleh <b>' . $lastModifiedByName . '</b> - ' . date('d/m/Y / H:i:s', strtotime($lastModifiedByDate));
        }

        return "<div>$action $buttonBatalEdit</div>";
    }
}

<?php

namespace Doco\fisioterapi\actions\PemeriksaanRanap;

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
        $aDiagUtama = ArrayHelper::getValue($data, 'a_diag_utama', '{}');
        $aDiagPenyerta = ArrayHelper::getValue($data, 'a_diag_penyerta', '{}');
        $diagnosaFungsi = ArrayHelper::getValue($data, 'diagnosa_fungsi', '{}');
        $diagnosaFungsi = json_decode($diagnosaFungsi, true);
        
        $aDiagUtama = json_decode($aDiagUtama, true);
        $aDiagPenyerta = json_decode($aDiagPenyerta, true);
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
            $html .= '<td><b>Assesment:</b> <br/>';
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
            
        }
        if (isset($data['planning']) && $data['planning'] != '') {
            $purifiedData = DocoHelpers::purifyHtml($data['planning']);
            $html .= '<tr style="line-height:130%;">';
            $html .= '<td><b>Planning:</b> <br/>';
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
            $html .= '<td><b>Catatan Penunjang:</b> <br/>';
            $html .= $purifiedData;
            $html .= '</td></tr>';
        }
        $html .= '</table></div>';
        return $html;
    }

    private function getData($pendaftaranId, $programTerapiIds)
    {
        $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'method' => 'GET',
            'url' => 'soap-ranap/get-soap',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId,
                    'program_terapi_ids' => $programTerapiIds
                ]
            ],

        ]);
        return ArrayHelper::getValue($response, 'data');
    }

    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaran_id_enc = $request->get('pendaftaran_id');
        $program_terapi_ids_enc = $request->get('program_terapi_ids');
        $program_terapi_ids_enc_arr = explode(',', $program_terapi_ids_enc);
        $program_terapi_ids_arr = [];
        $program_terapi_ids = "";
        foreach ($program_terapi_ids_enc_arr as $key => $value) {
            $valueDecrypted = DocoHelpers::decrypt($value);
            $program_terapi_ids_arr[] = $valueDecrypted;
            $program_terapi_ids = $program_terapi_ids . $valueDecrypted . ",";
        }
        $program_terapi_ids = rtrim($program_terapi_ids, ',');
        $isReadOnly = $request->get('readonly');
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id_enc);
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $resultBody = $this->getData($pendaftaran_id, $program_terapi_ids_arr);
            $no = $request->get('start', 1);
            $queryRj = ArrayHelper::getValue($resultBody, 'rajal.query', []);
            $queryRi = ArrayHelper::getValue($resultBody, 'ranap.query', []);
            if (count($queryRj) > 0) {
                foreach ($queryRj as $k => $v) {
                    $queryRj[$k]['type'] = 'FIS-RJ';
                }
            }
            if (count($queryRi) > 0) {
                foreach ($queryRi as $ke => $va) {
                    $queryRi[$ke]['type'] = 'FIS-RI';
                }
            }
            $datas = array_merge($queryRi, $queryRj);
            foreach ($datas as $key => $value) {
                $no++;
                $value['no'] = $no;
                $index = $no - 1;
                $soapFisioterapiId = ArrayHelper::getValue($value, 'soapfisioterapi_id');
                $primaryKey = DocoHelpers::encrypt($soapFisioterapiId);
                $programTerapiId = ArrayHelper::getValue($value, 'programterapi_id');
                $programTerapiIdEnc = DocoHelpers::encrypt($programTerapiId);
                $soapfisioterapi_id_enc = $primaryKey;
                $ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama', '-');
                $tglSoapFisioterapi = ArrayHelper::getValue($value, 'tgl_soapfisioterapi', '-');
                $terapisNama = ArrayHelper::getValue($value, 'terapis_nama');
                $value['soapfisioterapi_id'] = $primaryKey;
                $value['ruangan'] = $ruanganNama . '<br>' . $tglSoapFisioterapi . '<br>' . $terapisNama;
                $value['penatalaksanaan'] = self::generateHtmlPenatalaksanaan($value);
                $value['verifikasi'] = '';
                $value['rowNum'] = $no;
                $isEdit = ArrayHelper::getValue($value, 'is_edit');
                $terapisId = ArrayHelper::getValue($value, 'terapis_id');
                $lastModifiedByName = ArrayHelper::getValue($value, 'last_modified_by_name');
                $lastModifiedByDate = ArrayHelper::getValue($value, 'last_modified_date');
                $isFisio = ArrayHelper::getValue($value, 'is_fisio');
                $paramAction = [
                    'soapfisioterapi_id_enc' => $soapfisioterapi_id_enc,
                    'pendaftaran_id_enc' => $pendaftaran_id_enc,
                    'program_terapi_id_enc' => $programTerapiIdEnc,
                    'index' => $index,
                    'primaryKey' => $primaryKey,
                    'pendaftaran_id' => $pendaftaran_id,
                    'program_terapi_id' => $programTerapiId,
                    'isReadOnly' => $isReadOnly,
                    'isEdit' => $isEdit,
                    'lastModifiedByName' => $lastModifiedByName,
                    'lastModifiedByDate' => $lastModifiedByDate,
                    'terapisId' => $terapisId,
                    'isFisio' => $isFisio
                ];
                if (!empty($value['type'])) {
                    if ($value['type'] == 'FIS-RI') {
                        $value['action'] = self::permissionAction($paramAction);
                    } else {
                        $value['action'] = '-';
                    }
                }
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
        $pendaftaran_id_enc = $paramAction['pendaftaran_id_enc'];
        $program_terapi_id_enc = $paramAction['program_terapi_id_enc'];
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

        $urlEdit = "/fisioterapi/pemeriksaan-ranap/update-soap?soapfisioterapi_id=$soapfisioterapi_id_enc";
        $urlBatalEdit = "/fisioterapi/pemeriksaan-ranap/store-soap?pendaftaran_id=$pendaftaran_id_enc&program_terapi_id=$program_terapi_id_enc";
        $buttonBatalEdit = "<button class='btn btn-danger btn-labeled btn-xs action-cppt batal-edit-cppt hidden' data-index='$index' data-soapfisioterapi_id='$primaryKey' data-url-target='$urlBatalEdit' data-pendaftaran_id='$pendaftaran_id' data-program_terapi_id='$program_terapi_id'><b><i class='fa fa-close'></i></b> Batal Edit</button>";

        if ($isReadOnly) {
            $action = "<div class='tooltip-wrapper' data-toggle='tooltip' title='Aktif Ketika Diperiksa'><button class='btn btn-info btn-labeled btn-xs disabled'><b><i class='fa fa-edit'></i></b> Edit</button></div>";
        } else if ($loginPemakaiId != $terapisId) {
            $action = "<div class='tooltip-wrapper' data-toggle='tooltip' title='Anda Tidak Punya Akses'><button class='btn btn-info btn-labeled btn-xs disabled'><b><i class='fa fa-edit'></i></b> Edit</button></div>";
        } else if ($loginPemakaiId == $terapisId) {
            if ($isFisio) {
                $action = "<button class='btn btn-info btn-labeled btn-xs action-cppt edit-cppt' data-index='$index' data-soapfisioterapi_id='$primaryKey' data-url-target='$urlEdit'><b><i class='fa fa-edit'></i></b> Edit</button>";                
            } else {
                $action = "<div class='tooltip-wrapper' data-toggle='tooltip' title='Anda Tidak Punya Akses'><button class='btn btn-info btn-labeled btn-xs disabled'><b><i class='fa fa-edit'></i></b> Edit</button></div>";
            }
        } 
        else {
            $action = "<button class='btn btn-info btn-labeled btn-xs action-cppt edit-cppt' data-index='$index' data-soapfisioterapi_id='$primaryKey' data-url-target='$urlEdit'><b><i class='fa fa-edit'></i></b> Edit</button>";
        }

        if ($isEdit) {
            $action = 'Data sudah diubah oleh <b>' . $lastModifiedByName . '</b> - ' . date('d/m/Y / H:i:s', strtotime($lastModifiedByDate));
        }

        return "<div>$action $buttonBatalEdit</div>";
    }
}

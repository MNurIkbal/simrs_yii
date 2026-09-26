<?php

/**
 * @author : Setyabudi Dwisandi Arifin
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;

use Doco\payload\Reseptur as PayloadReseptur;
use Doco\exceptions\ValidationException;

use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use Doco\models\Antrian;
use Doco\models\Racikan;
use Doco\models\Reseptur;
use Doco\models\ResepturDetail;
use Doco\models\ResepturRacikan;
use Doco\models\SignaObat;
use Doco\models\InformasiResepturView;
use Doco\Notifications\FarmasiNotification;
use Doco\models\InfoStokObatAlkesAllRuanganFnr;
use Doco\models\HargaObatAlkesFn;
use Doco\models\Pendaftaran;
use GuzzleHttp\Exception\RequestException;

class ResepturProcess extends \Doco\components\DocoBaseProcessExtension
{

    /**
     * Doco\payload\Reseptur
     * @var object
     */
    protected $dataReseptur;

    protected $reseptur;

    protected $dataPasien;

    /**
     * @var [array]
     */
    protected $resepturDetail;

    /**
     * [$antrianId antrian id]
     * @var integer
     */
    protected $antrianId;

    protected $listRacikan;

    protected $racikanFreetext;

    protected $resepturId;

    protected $masterSigna;

    protected $noReseptur;
    
    /**
     * @return void
     * @throws Doco\exceptions\ValidationException
     */
    protected function validation()
    {
        $dataResep = $this->_requestData->post('data_reseptur',[]);
        $resepDetail = $this->_requestData->post('data_resepturdetail',[]);
        $payload = new PayloadReseptur;
        $payload->attributes = $dataResep;
        foreach ($resepDetail as $key => $value) {
            if($resepDetail[$key]['detail_type'] == 'racikan_detail') $resepDetail[$key]['satuan_racikan_id'] = intval($value['satuan_racikan_id']);
        }
        $payload->kategori_resep = (isset($dataResep['is_ranap']) && $dataResep['is_ranap'] == 1 && !empty($dataResep['kategori_resep'])) ? $dataResep['kategori_resep'] : NULL;
        unset($dataResep['is_ranap']);

        if (empty($resepDetail)) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Data Obat tidak boleh kosong'
            ]);
        }

        if (!$payload->validate()) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_errorValidation, [
                'data' => $payload->errors
            ]);
        }

        $this->dataReseptur = $dataResep;
        $this->resepturDetail = $resepDetail;
    }

    /**
     * @return void
     */
    protected function populateData()
    {
        $this->dataPasien = Yii::$app->db->createCommand("
                SELECT 
                pendaftaran.pendaftaran_id,
                CASE 
                    WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
                        CASE
                            WHEN pasienadmisi_t.is_pasientitipan IS TRUE THEN pasienadmisi_t.kelas_ditagihkan_id
                            ELSE pasienadmisi_t.kelaspelayanan_id
                        END
                    ELSE
                        pendaftaran.kelaspelayanan_id 
                    END AS kelaspelayanan_id,
                CASE
                    WHEN pasienadmisi_t.pasienadmisi_id IS NULL THEN pendaftaran.penjamin_id
                    ELSE pasienadmisi_t.penjamin_id
                END AS penjamin_id
                FROM pendaftaran_t pendaftaran
                    LEFT JOIN ( 
                        SELECT 
                            pasienadmisi.pasienadmisi_id,
                            pasienadmisi.penjamin_id,
                            pasienadmisi.kelaspelayanan_id,
                            pasienadmisi.kelas_ditagihkan_id,
                            pasienadmisi.is_pasientitipan
                            FROM pasienadmisi_t pasienadmisi
                    ) pasienadmisi_t ON pendaftaran.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                WHERE pendaftaran.pendaftaran_id = {$this->dataReseptur['pendaftaran_id']}
        ")->queryOne();

        if (!empty($this->dataReseptur) && !empty($this->resepturDetail)) {
            $inputRacikanFreetext = $inputNonRacikan = [];
            $is_freetext = false;
            foreach ($this->resepturDetail as $key => $value) {
                $racikanKode[] = $value['racikan_id'];
                $list_id[] = isset($value['obatalkes_id']) ? $value['obatalkes_id'] : null;
                
                // memisahkan obat racikan freetext dan non-racikan
                if(ArrayHelper::getValue($value, 'detail_type') == 'racikan_freetext') {
                    $inputRacikanFreetext[] = $value;
                    $is_freetext = true;
                } else {
                    $inputNonRacikan[] = $value;
                }
            }

            $this->racikanFreetext = $inputRacikanFreetext;
            if($is_freetext) {
                $this->resepturDetail = $inputNonRacikan;
            }

            $racikanType = "NR";
            $racikanKode = array_unique($racikanKode);
            if(count($racikanKode) > 1 || $racikanKode[0] == "OR"){
                $racikanType = "OR";
            }

            $list_racikan = Racikan::find()->asArray()->all();
            $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');
            $this->listRacikan = $list_racikan;
            $this->dataReseptur['racikan_id'] = isset($list_racikan[$racikanType]) ? $list_racikan[$racikanType] : null;
        }

        $this->masterSigna = SignaObat::find()->select([
            'signa_id', 'signa_nama', 'signa_kode'
        ])->asArray()->all();
    }

    /**
     * [createAntrian description]
     * @return [type] [description]
     */
    protected function createAntrian()
    {
        $fungsiId = ($this->dataReseptur['racikan_id'] == 2) ? DocoConstants::VAR_FA_NR : DocoConstants::VAR_FA_R;

        $model = new Antrian;
        $model->ruangan_id = $this->dataReseptur['ruangan_id'];
        $model->tgl_antrian = date('Y-m-d H:i:s');
        $model->pendaftaran_id = $this->dataReseptur['pendaftaran_id'];
        $model->jenisantrian_id = DocoConstants::VAR_JA_F;
        $model->racikan_id = $this->dataReseptur['racikan_id'];
        $model->fungsiantrian_id = $fungsiId;
        if (!$model->save(false)) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Simpan Antrian gagal.'
            ]);
        }
        $this->antrianId = $model->antrian_id;
    }

    protected function saveResep()
    {
        $model = new Reseptur;
        $model->attributes = $this->dataReseptur;
        $model->tglreseptur = date('Y-m-d H:i:s');
        $model->status_reseptur = 346;
        $model->antrian_id = $this->antrianId;
        $model->created_date = date('Y-m-d H:i:s');
        $model->pasienadmisi_id = $this->_requestData->post('pasienadmisi_id', null);
        $model->instruksi_id = $this->_requestData->post('instruksi_id', null);

        if (!$model->save(false)) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Simpan Reseptur gagal.'
            ]);
        }

        $detail = $tmpRute = $countRke = [];

        // untuk menghitung jumlah obat di masing-masing racikan
        $group_rke = ArrayHelper::index($this->resepturDetail, null, 'rke');
        foreach($group_rke as $rke => $obatRacikan) {
            $countRke[$rke] = count($obatRacikan);
        }

        foreach ($this->resepturDetail as $key => $value) {
            $row = $value;
            $row['reseptur_id'] = $model->reseptur_id;
            $row['racikan_id'] = $this->listRacikan[$value['racikan_id']];
            
            // rizal
            $qty_reseptur = isset($value['qty_reseptur']) ? $value['qty_reseptur'] : 0;
            $qty_konversi = isset($value['qty_konversi']) ? $value['qty_konversi'] : 0;
            $qty_rounded = ceil($qty_reseptur);
            $nilai_konversi = $qty_konversi / $qty_reseptur;
            $hargaNetto = isset($value['harganetto_reseptur']) ? $value['harganetto_reseptur'] : 0;

            $infoObat = (new HargaObatAlkesFn([
                'extParam' => [
                (string) $this->dataPasien['penjamin_id'],
                (string) $this->dataPasien['kelaspelayanan_id']
                ]
            ]))
            ->find()->where([
                'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id')
            ])->asArray()->one();

            $row['qty_reseptur'] = $qty_rounded;
            $row['harganetto_reseptur'] = $hargaNetto;
            $row['hargasatuan_reseptur'] = $this->calculateHargaObat($infoObat, $value, $qty_rounded, $countRke);
            $row['hargajual_reseptur'] = $row['hargasatuan_reseptur'] * $qty_rounded;
            $row['created_date'] = date('Y-m-d H:i:s');
            $row['status_implementasi'] = 454;
            $row['rke'] = isset($row['rke']) && !empty($row['rke']) ? $row['rke'] : null;
            $row['r'] = ArrayHelper::getValue($row, 'r', null);
            $row['signa_id'] = isset($value['signa_id']) ? $value['signa_id'] : null;
            if(!empty($value['signa_id'])) {
                $signa_index = array_search($value['signa_id'], array_column($this->masterSigna, 'signa_id'));
                $signa_json = json_encode([
                    'id' => $value['signa_id'],
                    'text' => $this->masterSigna[$signa_index]['signa_nama'],
                    'kode' => $this->masterSigna[$signa_index]['signa_kode']
                ]);
            } else {
                $signa_json = json_encode([
                    'id' => null,
                    'text' => $value['signa'],
                    'kode' => null
                ]);
            }
            
            $row['signa'] = $signa_json;
            $row['tgl_resepturdetail'] = date('Y-m-d H:i:s');
            $row['qty_medis'] = $qty_reseptur;
            $row['qty_konversi'] = $qty_rounded * $nilai_konversi;
            $row['nama_racikan'] = ArrayHelper::getValue($value,'nama_racikan');
            $row['qty_racikan'] = ArrayHelper::getValue($value,'qty_racikan');
            $row['satuan_racikan_id'] = isset($value['satuan_racikan_id']) ? ArrayHelper::getValue($value,'satuan_racikan_id') : null;
            $row['etiket'] = ArrayHelper::getValue($value, 'etiket', NULL);
            $row['hari'] = !empty(ArrayHelper::getValue($value, 'hari')) ? ArrayHelper::getValue($value, 'hari') : null;
            $detail[] = $row;
        }
        ResepturDetail::batchInsert($detail);   
        $model->refresh();
        $this->dataReseptur['reseptur_id'] = $model->reseptur_id;
        $this->resepturId = $model->reseptur_id;
        $this->noReseptur = $model->noresep;
    }

    protected function calculateHargaObat($infoObat, $detail, $qty_rounded, $countRke) {
        /** Rumus Harga Obat:
         * Harga Netto + Margin - Diskon + PPn + Embalase
         * Embalase Obat Non-Racikan: Embalase / Qty
         * Embalase Obat Racikan: Embalase / Jumlah detail obat dalam satu racikan (bukan qty)
         */
        
        $hargasatuan = 0;
        // $harganetto = $infoObat['harganetto'];

        $harganetto = ArrayHelper::getValue($infoObat,'harganetto');
        if(is_null($harganetto)){
            $infoObat = (new HargaObatAlkesFn([
                'extParam' => [
                    (string) $this->dataPasien['penjamin_id'],
                    (string) $this->dataPasien['kelaspelayanan_id']
                    ]
                ]))
            ->find()->where([
                    'obatalkes_id' => ArrayHelper::getValue($detail, 'obatalkes_id')
                ])->asArray()->one();
            $harganetto = ArrayHelper::getValue($infoObat,'harganetto',0);
        }

        $nett_p_margin = $harganetto + (($infoObat['margin'] * $harganetto)/100); // harga netto - margin
        $nett_m_disc = $nett_p_margin - (($infoObat['disc'] * $nett_p_margin)/100); // harga netto setelah margin - diskon
        $nett_p_ppn = ceil($nett_m_disc + (($infoObat['ppn'] * $nett_m_disc)/100)); // harga netto setelah diskon + ppn
        if($detail['racikan_id'] == "OR") { // obat racikan
            $hargasatuan = ceil($nett_p_ppn + ($infoObat['embalase_racikan'] / $countRke[$detail['rke']]));
        } else { // obat non-racikan
            $hargasatuan = ceil($nett_p_ppn + ($infoObat['embalase_nonracikan'] / $qty_rounded));
        }

        return $hargasatuan;
    }

    protected function insertRacikanFreetext() {
        // insert obat racikan freetext
        $countRacikan = count($this->racikanFreetext);
        if($countRacikan> 0) {
            $racikan = [];
            foreach ($this->racikanFreetext as $key => $value) {
                $racikan[] = [
                    'reseptur_id' => $this->dataReseptur['reseptur_id'],
                    'rke' => $value['rke'],
                    'racikan' => $value['racikan_text'],
                    'type' => $value['racikan_id']
                ];
            }
            ResepturRacikan::batchInsert($racikan);
        }
    }

    protected function afterSave() {
        FarmasiNotification::newResep($this->reseptur);
        \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
    }

    protected function getReseptur($reseptur_id) {
        $this->reseptur = InformasiResepturView::find()->where(['reseptur_id' => $reseptur_id])->one();
    }

    protected function processFlow()
    {
        try {
            $this->validation();
            $this->populateData();

            $this->startDBTransaction();

            $this->createAntrian();
            $this->saveResep();
            $this->insertRacikanFreetext();
            
            $this->commitDBTransaction();
            
            $this->getReseptur($this->dataReseptur['reseptur_id']);
            $this->afterSave();

            return [
                'message' => 'Data Berhasil di simpan',
                'data' => [
                    'reseptur_id' => $this->resepturId,
                    'pendaftaran_id' => ArrayHelper::getValue($this->dataReseptur, 'pendaftaran_id'),
                    'from' => 'reseptur_process'
                ]
            ];
        } catch(RequestException $e){
            return $this->responseJson(422, 'Error Request Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        } catch(\Exception $e){
            return $this->responseJson(422, 'Error Exception', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
        }
    }

}
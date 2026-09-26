<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\models\InfoStokObatAlkesFnr;
use app\components\ApotekComponent;
use app\modules\v1\payloads\ResepturPayload;
use app\modules\v1\payloads\ResepturDetailPayload;
use app\modules\v1\models\Antrian;
use app\modules\v1\models\Racikan;
use app\modules\v1\models\Reseptur as ResepturModel;
use app\modules\v1\models\SignaObat;
use app\modules\v1\models\ResepturDetail;
use app\modules\v1\models\ResepturRacikan;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\InfoStokObatAlkesAllRuanganFnr;
use Doco\models\HargaObatAlkesFn;
use Doco\exceptions\ValidationException;
use Exception;
use yii\db\Exception as DbException;

class Reseptur
{
	protected $_reseptur;
	protected $_resepturDetail;
    protected $_resepturRacikanDetail;
	protected $_modelReseptur;
	protected $_antrian;
    protected $dataPasien;
    protected $processFailed = 0;
    const ATTEMPT = 1;

	public function __get($name)
	{
		if (array_key_exists($name, $this->_reseptur)) {
            return $this->_reseptur[$name];
        }

        return null;
	}

    protected $_pendaftaran = [];
    protected $_mapRacikan = [];
    protected $_resepturDetailRacikans = [];
    protected $_resepturRacikanFreetext = [];
    protected $_konfigAntrian = [];
    public $listHargaObatAlkes = [];
    public $listSignaObat = [];
    public $attributes = [];

    const CACHE_DURATION = 4 * 60 * 60;
    const CACHE_NAME_MAP_RACIKAN = 'map_racikan';
    const CACHE_NAME_LIST_SIGNA_OBAT = 'list_signa_obat';

    public function __construct($input = [])
    {
        if (!empty($input)) {
            $resepturPayload = new ResepturPayload;
            $resepturPayload->attributes = $input;
            if (!$resepturPayload->validate()) throw new Exception('Validasi Gagal: ' . json_encode($resepturPayload->errors), 422);
            $this->_reseptur = $resepturPayload->attributes;
        }
        return $this;
    }

    public function setDataPendaftaran($pendaftaran)
    {
        $this->_pendaftaran = $pendaftaran ? $pendaftaran : [];
        return $this;
    }

    public function populateDetail($details = [])
    {
        if (empty($details) || !is_array($details)) throw new Exception('Validasi Gagal: Obat/Alkes tidak boleh kosong!', 422);

        $this->_mapRacikan = Yii::$app->cache->getOrSet(self::CACHE_NAME_MAP_RACIKAN, function () {
            $racikans = Racikan::find()->asArray()->all();
            return ArrayHelper::map($racikans, 'racikan_singkatan', 'racikan_id');
        }, self::CACHE_DURATION);

        $obatalkes_ids = $signa_ids = $racikan_kodes = $racikanFreetexts = $nonRacikanFreetexts = $group_racikan = [];
        foreach ($details as $idx => $attr) {
            foreach ($attr as $k => $v) {
                if ($v == 'null') $attr[$k] = null;
            }
            $row = $attr;
            // $row['racikan_id'] = isset($racikans_map[$attr['racikan_id']]) ? $racikans_map[$attr['racikan_id']] : null;
            $row['reseptur_id'] = null;
            $row['qty_medis'] = isset($attr['qty_reseptur']) ? floatval($attr['qty_reseptur']) : null;
            $row['obatalkes_id'] = isset($attr['obatalkes_id']) ? intval($attr['obatalkes_id']) : null;

            $detail_type = ArrayHelper::getValue($attr, 'detail_type');
            if ($detail_type == 'racikan_detail') {
                $row['satuan_racikan_id'] = intval($attr['satuan_racikan_id']);
                $group_racikan[$attr['rke']][] = $row;
            }

            $racikan_kodes[] = $attr['racikan_id'];
            if (!empty($row['obatalkes_id'])) {
                $obatalkes_ids[] = $row['obatalkes_id'];
            }
            if (!empty($attr['signa_id'])) {
                $signa_ids[] = $attr['signa_id'];
            }

            /* memisahkan obat racikan freetext */
            if ($detail_type == 'racikan_freetext') {
                $racikanFreetexts[] = $row;
            } else {
                $nonRacikanFreetexts[] = $row;
            }
        }
        $this->_resepturDetailRacikans = $group_racikan;

        $this->_resepturDetail = $nonRacikanFreetexts;
        $this->_resepturRacikanFreetext = $racikanFreetexts;

        $hargaObatAlkes = $this->getHargaObatAlkes($obatalkes_ids, $this->_pendaftaran);
        $this->listHargaObatAlkes = ArrayHelper::index($hargaObatAlkes, 'obatalkes_id');

        $this->listSignaObat = Yii::$app->cache->getOrSet(self::CACHE_NAME_LIST_SIGNA_OBAT, function () {
            $signas = SignaObat::find()->asArray()->all();
            return ArrayHelper::index($signas, 'signa_id');
        }, self::CACHE_DURATION);

        $racikan_kodes = array_unique($racikan_kodes);
        $racikan_type = (count($racikan_kodes) > 1 || $racikan_kodes[0] == 'OR') ? 'OR' : 'NR';
        $this->_reseptur['racikan_id'] = ArrayHelper::getValue($this->_mapRacikan, $racikan_type);
        $this->_konfigAntrian = $this->getKonfigAntrian($this->_reseptur);
        return $this;
    }

    protected function getAntrian($reseptur, $konfig_antrian)
    {
        $racikan_id = ArrayHelper::getValue($reseptur, 'racikan_id');
        $fungsiId = $racikan_id == 2 ? DocoConstants::VAR_FA_NR : DocoConstants::VAR_FA_R;

        $model = new Antrian;
        $model->ruangan_id = ArrayHelper::getValue($reseptur, 'ruangan_id');
        $model->tgl_antrian = date('Y-m-d H:i:s');
        $model->jenisantrian_id = DocoConstants::VAR_JA_F;
        $model->racikan_id = $racikan_id;
        $model->fungsiantrian_id = $fungsiId;
        $model->pendaftaran_id = ArrayHelper::getValue($reseptur, 'pendaftaran_id');

        $kuota_antrian = ArrayHelper::getValue($konfig_antrian, 'kuota_antrian');
        $antrian_global = ArrayHelper::getValue($konfig_antrian, 'additional_value');
        if ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK && !filter_var($antrian_global, FILTER_VALIDATE_BOOLEAN)) {
            $model->no_antrian = ArrayHelper::getValue($konfig_antrian, 'no_antrian');
        }

        if (!$model->save(false)) throw new Exception('Validasi Gagal: ' . json_encode($model->errors), 422);
        $this->_antrian = $model->attributes;
    }

    public function saveReseptur()
    {
        $this->getAntrian($this->_reseptur, $this->_konfigAntrian);

        $model = new ResepturModel;
		$model->attributes = $this->_reseptur;
		$model->antrian_id = $this->_antrian['antrian_id'];
        $model->status_reseptur = DocoConstants::VAR_AR;

        Yii::$app->db->createCommand('SAVEPOINT start_save_reseptur;')->execute();
        $message = null;
        while ($this->processFailed < self::ATTEMPT) {
            try {
                $model->save();
                $this->processFailed = self::ATTEMPT;
            } catch (DbException $de) {
                $message = $de->getMessage();
                if ($de->getCode() != 23505) {
                    throw new Exception('Simpan Reseptur Gagal, ' . $message, 500);
                }
                Yii::$app->db->createCommand('ROLLBACK TO SAVEPOINT start_save_reseptur;')->execute();
                Yii::$app->db->createCommand('SELECT get_sequence_reseptur_t();')->execute();
                $this->processFailed++;
            }
        }
        Yii::$app->db->createCommand('RELEASE SAVEPOINT start_save_reseptur;')->execute();
        if (is_null($model->getPrimaryKey())) {
            Yii::error(['Simpan Reseptur Gagal' => $message]);
            throw new Exception('System sedang sibuk, Silahkan disimpan kembali.', 422);
        }

        $model->refresh();
        $this->saveResepturDetail($model, $this->_resepturDetail, $this->_resepturDetailRacikans, $this->_mapRacikan, $this->listHargaObatAlkes, $this->listSignaObat);
        $this->saveRacikanFreeText($model, $this->_resepturRacikanFreetext);

        $this->_modelReseptur = $model;
        $this->_resepturDetail = ResepturDetail::find()->where(['reseptur_id' => $model->getPrimaryKey()])->asArray()->all();
        $this->distibuteData($model, $this->_resepturDetail);
        return $this;
    }

    protected function saveResepturDetail($model, $resepturDetail, $resepturDetailRacikan, $mapRacikan, $listHargaObatAlkes, $signas)
    {
        /* menghitung jumlah obat di masing-masing racikan */
        $countRke = [];
        $totalRke = [];
        foreach ($resepturDetailRacikan as $rke => $racikan) {
            if (is_array($racikan)) {
                foreach ($racikan as $totalQtys) {
                    $totalQty = ArrayHelper::getValue($totalQtys, 'qty_reseptur', 0);
                    if (empty($totalRke[$rke])) {
                        $totalRke[$rke] = ceil($totalQty);
                    } else {
                        $totalRke[$rke] += ceil($totalQty);
                    }
                }
            }

            $countRke[$rke] = count($racikan);
        }

        $countRke = $totalRke;
        $detail = [];
        foreach ($resepturDetail as $key => $value) {
            if($value['qty_reseptur'] <= 0) throw new Exception('Validasi Gagal: Qty Obat/Alkes harus lebih dari 0', 422);

            $signaObat = ArrayHelper::getValue($signas, ArrayHelper::getValue($value, 'signa_id'), []);
            $hargaObatAlkes = ArrayHelper::getValue($listHargaObatAlkes, ArrayHelper::getValue($value, 'obatalkes_id'), []);

            $qty_reseptur = ArrayHelper::getValue($value, 'qty_reseptur', 0);
            $qty_konversi = ArrayHelper::getValue($value, 'qty_konversi', 0);
            $qty_rounded = ceil($qty_reseptur);
            $nilai_konversi = $qty_konversi / $qty_reseptur;
            $hargaNetto = ArrayHelper::getValue($value, 'harganetto_reseptur', 0);
            $hargaJual = $qty_rounded * $hargaNetto;

            $row = $value;
            $row['racikan_id'] = ArrayHelper::getValue($mapRacikan, $value['racikan_id']);
            $row['reseptur_id'] = $model->getPrimaryKey();
            $row['qty_reseptur'] = $qty_rounded;
            $row['qty_medis'] = $qty_reseptur;
            $row['qty_konversi'] = $qty_rounded * $nilai_konversi;
            $row['harganetto_reseptur'] = $hargaNetto;
            $row['hargasatuan_reseptur'] = $this->calculateHargaObat($hargaObatAlkes, $value, $qty_rounded, $countRke);
            $row['hargajual_reseptur'] = $row['hargasatuan_reseptur'] * $qty_rounded;
            $row['created_date'] = date('Y-m-d H:i:s');
            $row['status_implementasi'] = 454;
            $row['rke'] = isset($row['rke']) && !empty($row['rke']) ? $row['rke'] : null;
            $row['r'] = ArrayHelper::getValue($value, 'r', null);

            $signa_json = null;
            if(!empty($value['signa_id'])) {
                $row['signa_id'] = $value['signa_id'];
                $signa_json = json_encode([
                    'id' => $value['signa_id'],
                    'text' => ArrayHelper::getValue($signaObat, 'signa_nama', $value['signa']),
                    'kode' => ArrayHelper::getValue($signaObat, 'signa_kode', null),
                ]);
            } else {
                $row['signa_id'] = null;
                $signa_json = json_encode([
                    'id' => null,
                    'text' => $value['signa'],
                    'kode' => null
                ]);
            }

            $row['signa'] = $signa_json;
            $row['tgl_resepturdetail'] = date('Y-m-d H:i:s');
            $row['is_kronis'] = filter_var($value['is_kronis'], FILTER_VALIDATE_BOOLEAN);
            $row['hari'] = empty($value['hari']) ? null : $value['hari'];
            $detail[] = $row;
        }
        ResepturDetail::batchInsert($detail);
    }

    protected function saveRacikanFreeText($model, $racikanFreetext)
    {
        if(!empty($racikanFreetext)) {
            $racikan = [];
            foreach ($racikanFreetext as $value) {
                $racikan[] = [
                    'reseptur_id' => $model->getPrimaryKey(),
                    'rke' => $value['rke'],
                    'racikan' => $value['racikan_text'],
                    'type' => $value['racikan_id']
                ];
            }
            ResepturRacikan::batchInsert($racikan);
        }
    }

    protected function getHargaObatAlkes($obatalkes_ids, $pendaftaran)
    {
        return (new HargaObatAlkesFn([
            'extParam' => [
                (string) ArrayHelper::getValue($pendaftaran, 'penjamin_id', 0),
                (string) ArrayHelper::getValue($pendaftaran, 'kelaspelayanan_id', 0)
            ]
        ]))->find()->where([
            'obatalkes_id' => $obatalkes_ids
        ])->asArray()->all();
    }

    protected function getKonfigAntrian($resepturPayload)
    {
        $params = [
            ':KONFIG_ID' => DocoConstants::KONFIG_ID,
            ':pendaftaran_id' => ArrayHelper::getValue($resepturPayload, 'pendaftaran_id'),
            ':jenisantrian_id' => DocoConstants::VAR_JA_P,
        ];
        return Yii::$app->db->createCommand("
            SELECT
                antrian_t.no_antrian,
                konfigsystem_k.kuota_antrian,
                lookuptransaksi_m.additional_value
            FROM antrian_t
            LEFT JOIN konfigsystem_k ON konfigsystem_k.konfigsystem_id = :KONFIG_ID
            LEFT JOIN lookuptransaksi_m ON lookuptransaksi_m.kode_transaksi = 'konfig_antrian_prefix_dokter'
            WHERE pendaftaran_id = :pendaftaran_id AND jenisantrian_id = :jenisantrian_id;
        ")->bindValues($params)->queryOne();
    }

    protected function distibuteData($model, $details)
    {
        $this->_reseptur = $this->attributes = $model->attributes;
        $this->_reseptur['details'] = $details;
    }

	public function load($inputReseptur,$inputResepturDetail)
	{

		$resepturPayload = new ResepturPayload;
    	$resepturPayload->attributes = $inputReseptur;
    	if(!$resepturPayload->validate()) throw new \Exception("Error Processing Request", 1);
    	$this->_reseptur = $resepturPayload->attributes;

    	if(!is_array($inputResepturDetail)) throw new \Exception("Data Detail Harus Berupa Array", 1);

        if(count($inputResepturDetail) > 0) {
        	foreach ($inputResepturDetail as $_kinput => $_vdetail) {
                foreach ($_vdetail as $_atrk => $_atrv){
                    if($_atrv == "null") $inputResepturDetail[$_kinput][$_atrk] = null;
                }
            }
            foreach ($inputResepturDetail as $key => $value) {
                $racikanKode[] = $value['racikan_id'];
            }

            $racikanType = "NR";
            $racikanKode = array_unique($racikanKode);
            if(count($racikanKode) > 1 || $racikanKode[0] == "OR"){
                $racikanType = "OR";
            }

            $list_racikan = Racikan::find()->asArray()->all();
            $list_racikan = ArrayHelper::map($list_racikan, 'racikan_singkatan', 'racikan_id');

            $this->_reseptur['racikan_id'] = isset($list_racikan[$racikanType]) ? $list_racikan[$racikanType] : null;
            foreach ($inputResepturDetail as $k => $v) {
                $inputResepturDetail[$k]['racikan_id'] = isset($list_racikan[$v['racikan_id']]) ? $list_racikan[$v['racikan_id']] : null;
                $inputResepturDetail[$k]['reseptur_id'] = 0;
                $inputResepturDetail[$k]['qty_medis'] = floatval($v['qty_reseptur']);
            }

            foreach($inputResepturDetail as $_resepturDetail){
                $resepturDetailPayload = new ResepturDetailPayload;
                $resepturDetailPayload->attributes = $_resepturDetail;
                if(!$resepturDetailPayload->validate())
                    throw new \Exception(json_encode($resepturDetailPayload->errors), 1);
            }
        }

    	$this->_resepturDetail = $inputResepturDetail;
        $this->dataPasien = Pendaftaran::find()
        ->select(['penjamin_id', 'kelaspelayanan_id'])
        ->where(['pendaftaran_id' => ArrayHelper::getValue($this->_reseptur, 'pendaftaran_id')])
        ->asArray()->one();
    	return $this;
	}

    public function loadResepturRacikan($racikan) {
        $this->_resepturRacikanDetail = $racikan;
        return $this;
    }

    public function loadById($reseptur_id)
    {
        $reseptur = ResepturModel::find()->where(['reseptur_id'=>$reseptur_id])->asArray()->one();
        if(empty($reseptur)) throw new \Exception("Error Processing Request", 1);

        $this->_reseptur = $reseptur;
        return $this;
    }

	public function createAntrian()
	{
        $ruangan_id = $this->_reseptur['ruangan_id'];
		$racikan_id = $this->_reseptur['racikan_id'];
		$fungsiId = ($racikan_id == 2) ? DocoConstants::VAR_FA_NR : DocoConstants::VAR_FA_R;

        $model = new Antrian;
        $model->ruangan_id = $ruangan_id;
        $model->tgl_antrian = date('Y-m-d H:i:s');
        $model->jenisantrian_id = DocoConstants::VAR_JA_F;
        $model->racikan_id = $racikan_id;
        $model->fungsiantrian_id = $fungsiId;
        $model->pendaftaran_id = $this->_reseptur['pendaftaran_id'];

        $konfig = AntrianPoliLogic::isKonfig();

        if($konfig) {
            $noAntrian = Antrian::find()
                ->select([
                    'no_antrian'
                ])->where([
                    'pendaftaran_id' => $this->_reseptur['pendaftaran_id'],
                    'jenisantrian_id' => DocoConstants::VAR_JA_P
                ])->asArray()->one();
            $noAntrian = ArrayHelper::getValue($noAntrian, 'no_antrian');
            $model->no_antrian = $noAntrian;
        }

        if(!$model->save(false)) throw new \Exception("Error Processing Request", 1);
        $this->_antrian = $model->attributes;
	}

	public function save()
	{
		if(empty($this->_reseptur)) throw new \Exception("Error Processing Request1", 1);
		$model = new ResepturModel;
		$model->attributes = $this->_reseptur;
		$model->antrian_id = $this->_antrian['antrian_id'];
        $model->status_reseptur = DocoConstants::VAR_AR;
        if($model->validate()){
            if(!$model->save()) throw new \Exception("Error Processing Request2", 1);
        }else{
            $errorText = $model->getErrors();
            if(isset($errorText["noresep"])){
                if(preg_match('/\bdipergunakan\b/i', $errorText["noresep"][0])) {
                    if($this->processFailed < self::ATTEMPT) {
                        $this->processFailed++;
                        $this->save();
                    } else {
                        return [
                            'status' => 422,
                            'title' => 'Proses Gagal!',
                            'text' => $errorText,
                        ];
                    }
                } else {
                    return [
                        'status' => 422,
                        'title' => 'Proses Gagal!',
                        'text' => $errorText,
                    ];
                }
            }
        }
		$this->_reseptur = $model->attributes;
		$this->_modelReseptur = $model;

        $rawSigna = SignaObat::find()->asArray()->all();
        if(count($rawSigna)>0)
            $signaObat = array_column($rawSigna, 'signa_nama','signa_id');

        // untuk menghitung jumlah obat di masing-masing racikan
        $group_rke = ArrayHelper::index($this->_resepturDetail, null, 'rke');
        foreach($group_rke as $rke => $obatRacikan) {
            $countRke[$rke] = count($obatRacikan);
        }

        $listInfoObatR = $this->listInfoObatR();
        $listInfoObatR = ArrayHelper::index($listInfoObatR, 'obatalkes_id');

        if(count($this->_resepturDetail) > 0) {
    		foreach ($this->_resepturDetail as $key => $value) {
                $signa_index = array_search($value['signa_id'], array_column($rawSigna, 'signa_id'));
                if($value['qty_reseptur'] <= 0) throw new \Exception("Qty harus lebih dari 0", 1);

                $qty_reseptur = isset($value['qty_reseptur']) ? $value['qty_reseptur'] : 0;
                $qty_konversi = isset($value['qty_konversi']) ? $value['qty_konversi'] : 0;
                $qty_rounded = ceil($qty_reseptur);
                $row = $value;
                $infoObatR = isset($listInfoObatR[ArrayHelper::getValue($row, 'obatalkes_id')]) ? $listInfoObatR[ArrayHelper::getValue($row, 'obatalkes_id')] : [];
                
                $row['reseptur_id'] = $model->getPrimaryKey();
                $hargaNetto = $value['harganetto_reseptur'];
                $hargaJual = $qty_rounded * $hargaNetto;
                $nilai_konversi = $qty_konversi / $qty_reseptur;
                $row['qty_reseptur'] = $qty_rounded;
                $row['qty_medis'] = $qty_reseptur;
                $row['qty_konversi'] = $qty_rounded * $nilai_konversi;
                $row['harganetto_reseptur'] = $hargaNetto;
                $row['hargasatuan_reseptur'] = $this->calculateHargaObat($infoObatR, $value, $qty_rounded, $countRke);
                $row['hargajual_reseptur'] = $row['hargasatuan_reseptur'] * $qty_rounded;
                $row['created_date'] = date('Y-m-d H:i:s');
                $row['status_implementasi'] = 454;
                $row['rke'] = isset($row['rke']) && !empty($row['rke']) ? $row['rke'] : null;
                $row['signa_id'] = !empty($value['signa_id']) ? $value['signa_id'] : null;
                $row['signa'] = isset($signaObat[$value['signa_id']]) && $value['signa'] != $value['signa_id'] ? 
                            json_encode(['id' => $value['signa_id'], 'text' => $rawSigna[$signa_index]['signa_nama'], 'kode' => $rawSigna[$signa_index]['signa_kode']]) : 
                            json_encode(['id' => null, 'text' => $value['signa'], 'kode' => null]);
                $row['tgl_resepturdetail'] = date('Y-m-d H:i:s');
                $row['is_kronis'] = $value['is_kronis'] ? true : false;
                $row['hari'] = empty($value['hari']) || $value['hari'] == '' ? null : $value['hari'];
                $detail[] = $row;
            }

            ResepturDetail::batchInsert($detail);
        }

        if($this->_resepturRacikanDetail && count($this->_resepturRacikanDetail) > 0) {
            $racikan = [];
            foreach ($this->_resepturRacikanDetail as $key => $value) {
                $racikan[] = [
                    'reseptur_id' => $model->getPrimaryKey(),
                    'rke' => $value['rke'],
                    'racikan' => $value['racikan_text'],
                    'type' => $value['racikan_id']
                ];
            }
            ResepturRacikan::batchInsert($racikan);
        }

        $this->_resepturDetail = ResepturDetail::find()->where(['reseptur_id'=>$model->getPrimaryKey()])->asArray()->all();
	}

    public function getResepturHeader() {
        return $this->_modelReseptur;
    }

	public function getResepturDetail()
	{
		return $this->_resepturDetail;
	}

    public function updatePenjualan($penjualanresep_id)
    {
        $this->_modelReseptur->status_reseptur = DocoConstants::VAR_AR;
        $this->_modelReseptur->penjualanresep_id = $penjualanresep_id;
        if(!$this->_modelReseptur->save()) throw new \Exception("Error Processing Request", 1);

        $this->distibuteData($this->_modelReseptur, $this->_resepturDetail);
        return $this;
    }

    public function deleteOrAddDetail($inputListObat, $inputHeader)
    {
        $processedTime = date('Y-m-d H:i:s');
        $userLogin = Yii::$app->user->identity->pegawai_id;

        // update header reseptur
        if(empty($this->_reseptur)) throw new \Exception("Error Processing Request1", 1);
        $model = ResepturModel::findOne($this->_reseptur['reseptur_id']);
        $ruangan_id = $model->ruangan_id;
        $model->biaya_administrasi = $inputHeader['biaya_administrasi'];
        if(!$model->save()) throw new \Exception("Error Processing Request Reseptur", 1);

        foreach ($inputListObat as $_input) {
            if(isset($_input['det']) && is_int($_input['det']) && $_input['det'] > $_input['qty']){
                throw new \Exception("det tidak boleh melebihi qty", 1);
            }
        }

        //get detail reseptur
        $resepturDetail = [];
        $resepturDetails = ResepturDetail::find()->where(['reseptur_id'=>$this->_reseptur['reseptur_id']])->asArray()->all();
        foreach ($resepturDetails as $_valueResepturDetail) {
            $resepturDetail[$_valueResepturDetail['resepturdetail_id']] = $_valueResepturDetail;
        }

        //separate existing reseptur & obat baru ditambahkan
        $currentObat = $newObat = $deletedObatIds = $updateIds = [];
        foreach ($inputListObat as $_detailList) {
            if(isset($_detailList['resepturdetail_id']) && !empty($_detailList['resepturdetail_id'])){
                if($_detailList['is_deleted']) {
                    $deletedObatIds[] = $_detailList['resepturdetail_id'];
                } else {
                    $updateIds[] = $_detailList['resepturdetail_id'];
                    $currentObat[$_detailList['resepturdetail_id']] = $_detailList;
                }
            }else{
                if(!$_detailList['is_deleted']){
                    $newObat[] = $_detailList;
                }
            }
        }

        if(count($deletedObatIds) > 0) {
            $delete_detail = ResepturDetail::updateAll(
                [
                    'is_deleted' => TRUE, 
                    'is_active' => FALSE,
                    'deleted_date' => $processedTime,
                    'deleted_by' => $userLogin
                ], 
                ['in', 'resepturdetail_id', $deletedObatIds]
            );
            
            if($delete_detail <= 0) throw new \Exception("Gagal Hapus Reseptur", 1);
        }

        $reseptur_detail = ResepturDetail::find()->where(['in', 'resepturdetail_id', $updateIds])->asArray()->all();
        $reseptur_detail = ArrayHelper::index($reseptur_detail, 'resepturdetail_id');

        $obatReseptur = [];
        $resepturdetail_ids = $det = $det_konversi = $det_medis = $harganetto_reseptur = $hargajual_reseptur = $hargasatuan_reseptur = $signaId = $signa = [];
        
        foreach ($currentObat as $key => $_currObat) {
            if($_currObat['is_deleted'] == TRUE) continue;

            $rdId = $_currObat['resepturdetail_id'];
            $additional_data = json_decode($reseptur_detail[$rdId]['additional_data'], true);
            $nilai_konversi = is_null($additional_data['nilai_konversi']) ? 1 : floatval($additional_data['nilai_konversi']);
            $old_det = $reseptur_detail[$rdId]['det'];

            $resepturdetail_ids[] = $rdId;
            $signaId[] = floatval(ceil($_currObat['signa_id']));
            $det[] = floatval(ceil($_currObat['det']));
            $det_medis[] = floatval($_currObat['det']);
            $det_konversi[] = floatval(ceil($_currObat['det'])) * $nilai_konversi;
            $harganetto_reseptur[] = $_currObat['harganetto'] * ceil($_currObat['det']);
            $hargajual_reseptur[] = $_currObat['harga'] * ceil($_currObat['det']);
            $hargasatuan_reseptur[] = floatval($_currObat['harga']);
            $kronis[] = $_currObat['is_kronis'] == 'true' ? true : false;
            $lastModifiedDate[] = $processedTime."::timestamp";
            $lastModifiedBy[] = $userLogin;

            $obatReseptur[$key] = $_currObat;
            $obatReseptur[$key]['det'] = ceil($_currObat['det']);
            $obatReseptur[$key]['det_medis'] = $_currObat['det'];
            $obatReseptur[$key]['det_konversi'] = ceil($_currObat['det']) * $nilai_konversi;
            $obatReseptur[$key]['last_modified_date'] = $processedTime;
            $obatReseptur[$key]['last_modified_by'] = $userLogin;

            $textSigna = str_replace("'","",$_currObat['signa']);
            $dataSigna = json_encode(['id' =>$_currObat['signa_id'], 'text' => $textSigna, 'kode' => 'null']);
            $query = 
                "UPDATE resepturdetail_t 
                SET signa = '{$dataSigna}' 
                WHERE resepturdetail_id = {$rdId} AND is_deleted = false;";
            \Yii::$app->db->createCommand($query)->execute();         
        }
        
        if(count($resepturdetail_ids) > 0) {
            $detailUpdate = [
                'signa_id' => $signaId,
                'det' => $det,
                'det_konversi' => $det_konversi,
                'det_medis' => $det_medis,
                'harganetto_reseptur' => $harganetto_reseptur,
                'hargajual_reseptur' => $hargajual_reseptur,
                'hargasatuan_reseptur' => $hargasatuan_reseptur,
                'is_kronis' => $kronis,
                'last_modified_by' => $lastModifiedBy
            ];
            $updateCondition = [
                'resepturdetail_id' => $resepturdetail_ids
            ];
            
            $updateDetail = ApotekComponent::updateMultiple('resepturdetail_t', $detailUpdate, $updateCondition);
            if(!$updateDetail || \Yii::$app->response->statusCode != 200) throw new \Exception("Gagal update detail reseptur", 1);
        }

        $signaObat = [];
        $rawSigna = SignaObat::find()->asArray()->all();
        if(count($rawSigna)>0)
            $signaObat = array_column($rawSigna, 'signa_nama','signa_id');
            
        $newResepturDetail = [];
        foreach ($newObat as $_newObat) {
            $signa_index = array_search($_newObat['signa_id'], array_column($rawSigna, 'signa_id'));
            $det = floatval($_newObat['det']);
            $nilai_konversi = floatval($_newObat['nilai_konversi']);
            $harganetto = floatval($_newObat['harganetto']) * $nilai_konversi;

            $hargasatuan_ = floatval($_newObat['harga']);
            $qty_hitung = isset($_newObat['det']) && !is_null($_newObat['det']) ? floatval(ceil($_newObat['det'])) : floatval(ceil($_newObat['qty']));
            $hargajual_ = $hargasatuan_ * $qty_hitung;

            $newResepturDetail[] = [
                'obatalkes_id' => $_newObat['obatalkes_id'],
                'racikan_id' => $_newObat['racikan_id'],
                'satuankecil_id' => $_newObat['satuankecil_id'],
                'reseptur_id' => $this->_reseptur['reseptur_id'],
                'r' => !empty($_newObat['racikan_id']) && $_newObat['racikan_id'] == 1 ? 'r' : null,
                'rke' => is_int($_newObat['r_ke']) ? $_newObat['r_ke'] : null,
                'kekuatan_reseptur' => null,
                'satuankekuatan' => null,
                'qty_reseptur' => floatval(ceil($_newObat['qty'])),
                'qty_medis' => floatval($_newObat['qty']),
                'hargasatuan_reseptur' => $hargasatuan_,
                'harganetto_reseptur' => floatval(ceil($det)) * $harganetto,
                'hargajual_reseptur' => $hargajual_,
                'etiket' => @$_newObat['catatan'],
                'iter' => null,
                'signa_id' => isset($signaObat[$_newObat['signa_id']]) && $_newObat['signa'] != $_newObat['signa_id'] ? $_newObat['signa_id'] : null,
                'signa' => isset($signaObat[$_newObat['signa_id']]) && $_newObat['signa'] != $_newObat['signa_id'] ? 
                            json_encode(['id' => $_newObat['signa_id'], 'text' => $rawSigna[$signa_index]['signa_nama'], 'kode' => $rawSigna[$signa_index]['signa_kode']]) : 
                            json_encode(['id' => null, 'text' => $_newObat['signa'], 'kode' => null]),
                'status_implementasi' => 454,
                'tgl_resepturdetail' => date('Y-m-d H:i:s'),
                'qty_konversi' => floatval(ceil($_newObat['qty'])) * floatval($_newObat['nilai_konversi']),
                'det' => ceil($det),
                'det_konversi' => ceil($det) * @floatval($_newObat['nilai_konversi']),
                'det_medis' => $det,
                'is_kronis' => $_newObat['is_kronis'] == 'true' ? true : false, 
                'additional_data' => json_encode([
                    "satuaninput_id" => @$_newObat['satuaninput_id'],
                    "satuan_input" => @$_newObat['satuan_input'],
                    "satuankonversi_id" => @$_newObat['satuankonversi_id'],
                    "satuan_konversi" => @$_newObat['satuan_konversi'],
                    "harga_konversi" => @$_newObat['harga'],
                    "qty_input" => @$_newObat['qty'],
                    "posisi" => @$_newObat['posisi'],
                    "nilai_konversi" => @$_newObat['nilai_konversi']
                ]),

                // kebutuhan obatalkespasien_t
                'qty_oa' => floatval($_newObat['qty_konversi']),
                'hargajual_oa' => empty($_newObat['subtotal']) ? 0 : $_newObat['subtotal'],
                'harganetto_oa' => empty($_newObat['harganetto']) ? 0 : $harganetto,
                'hargasatuan_oa' => empty($_newObat['harga']) ? 0 : $_newObat['harga'],
                'signa_oa' => isset($signaObat[$_newObat['signa_id']]) && $_newObat['signa'] != $_newObat['signa_id'] ? $_newObat['signa_id'] : 0,
                'nama_racikan' => !empty($_newObat['nama_racikan']) ? $_newObat['nama_racikan'] : null,
                'qty_racikan' => !empty($_newObat['qty_racikan']) ? $_newObat['qty_racikan'] : null,
                'satuan_racikan_id' => !empty($_newObat['satuan_racikan_id']) ? $_newObat['satuan_racikan_id'] : null,
            ];
        }

        if(count($newResepturDetail)>0) ResepturDetail::batchInsert($newResepturDetail);

        return [
            'obatReseptur'  => $obatReseptur,
            'newObat'       => $newResepturDetail
        ];
    }

    public function updateApprove($reseptur_id,$penjualanresep_id) {
        $modelReseptur = ResepturModel::find()->where(['reseptur_id' => $reseptur_id])->one();
        $modelReseptur->status_reseptur = DocoConstants::RESEPTUR_SUDAH_DIPROSES;
        $modelReseptur->penjualanresep_id = $penjualanresep_id;
        if(!$modelReseptur->save()) throw new \Exception("Error Processing Request Reseptur", 1);
        return true;
    }

    public function getResepturDetailById($reseptur_id)
	{
		return ResepturDetail::find()->where(['reseptur_id'=>$reseptur_id])->asArray()->all();
	}

    /** Rumus Harga Obat:
     * Harga Netto + Margin - Diskon + PPn + Embalase
     * Embalase Obat Non-Racikan: Embalase / Qty
     * Embalase Obat Racikan: Embalase / Jumlah detail obat dalam satu racikan (bukan qty)
     */
    protected function calculateHargaObat($infoObat, $detail, $qty_rounded, $countRke) {
        $hargasatuan = 0;

        /**
         * Rumus harga obat dirubah ke Function.
         */
        // $harganetto = ArrayHelper::getValue($infoObat, 'harganetto',0);
        // $nett_p_margin = $harganetto + ((ArrayHelper::getValue($infoObat, 'margin', 0) * $harganetto)/100); // harga netto - margin
        // $nett_m_disc = $nett_p_margin - ((ArrayHelper::getValue($infoObat, 'disc', 0) * $nett_p_margin)/100); // harga netto setelah margin - diskon
        // $nett_p_ppn = ceil($nett_m_disc + ((ArrayHelper::getValue($infoObat, 'ppn', 0) * $nett_m_disc)/100)); //harga netto setelah diskon + ppn

        $nett_p_ppn = isset($infoObat['hargaygdipakai']) ? ceil($infoObat['hargaygdipakai']) : 0;
        $embalase_racikan = isset($infoObat['embalase_racikan']) ? $infoObat['embalase_racikan'] : 0;
        $embalase_nonracikan = isset($infoObat['embalase_nonracikan']) ? $infoObat['embalase_nonracikan'] : 0;
        if($detail['racikan_id'] == "OR") { // obat racikan
            $hargasatuan = ceil($nett_p_ppn + ($embalase_racikan / $countRke[$detail['rke']]));
        } else { // obat non-racikan
            $hargasatuan = ceil($nett_p_ppn + ($embalase_nonracikan / $qty_rounded));
        }
        return $hargasatuan;
    }

    protected function listInfoObatR()
    {
        $obatalkesIds = ArrayHelper::getColumn($this->_resepturDetail, 'obatalkes_id');
        /* deprecated function
        return (new InfoStokObatAlkesAllRuanganFnr([
            'extParam' => [
                (string) ArrayHelper::getValue($this->dataPasien, 'penjamin_id'),
                (string) ArrayHelper::getValue($this->dataPasien, 'kelaspelayanan_id')
            ]
        ]))
        ->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();

        return (new InfoStokObatAlkesFnr([
            'extParam' => [
                (string) ArrayHelper::getValue($this->dataPasien, 'penjamin_id'),
                (string) ArrayHelper::getValue($this->dataPasien, 'kelaspelayanan_id'),
                (string) ArrayHelper::getValue($this->_reseptur, 'ruangan_id')
            ]
        ]))
        ->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();
        */

        return (new HargaObatAlkesFn([
            'extParam' => [
                (string) ArrayHelper::getValue($this->dataPasien, 'penjamin_id'),
                (string) ArrayHelper::getValue($this->dataPasien, 'kelaspelayanan_id')
            ]
        ]))
        ->find()->where([
            'obatalkes_id' => $obatalkesIds
        ])->asArray()->all();
    }

    public function validasiCloseBill()
    {
        $reseptur = $this->_reseptur;
        $pendaftaranId = ArrayHelper::getValue($reseptur, 'pendaftaran_id');
        if(!empty($pendaftaranId)) {
            $dataPendaftaran = Pendaftaran::findOne($pendaftaranId);
            $isCloseBill = ArrayHelper::getValue($dataPendaftaran, 'is_close_bill', false);
            if($isCloseBill) {
                \Yii::$app->response->statusCode = 422;
                throw new \Exception('Pasien sudah dilakukan proses Lock Bill.', 1);
            }
        }
    }
}

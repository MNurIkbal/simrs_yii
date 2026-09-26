<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\models\Diagnosa;

class AsesmenKeperawatanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoRiwayatPasienView';

    /**
     *
     * @see Fungsi get pasien
     * @return array, activeQueryRecords data pasien
     *
     */
    public function actionGetPasien()
    {
        // $cache = Yii::$app->cache;
        // $data = $cache->get('$key');
        try {
            $request = Yii::$app->request;

            $query = $this->getDataPasien($request->get('id'));

            return [
                'data' => $query->asArray()->one()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * @see Fungsi get data pasienpendaftaran pendaftaran_t
     * @var params integer id = primary key pendaftaran_id
     * @return array, activeQueryRecords
     *
     */
    private function getDataPasien($id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pendaftaran_t.pasien_id as pasien_id,
                    pendaftaran_t.pendaftaran_id as pendaftaran_id,
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pasien_m.alamat_pasien AS alamat_pasien,
                    pendaftaran_t.tgl_pendaftaran,
                    pendaftaran_t.no_pendaftaran,
                    pasien_m.nama_pasien AS nama_pasien,
                    lookup_m.lookup_name AS jeniskelamin,
                    pendaftaran_t.jeniskasuspenyakit_id AS jeniskasuspenyakit_id,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS jeniskasuspenyakit_nama,
                    pasien_m.tanggal_lahir AS tanggal_lahir,
                    pendaftaran_t.golonganumur_id AS golonganumur_id,
                    pegawai_m.nama_pegawai AS nama_pegawai,
                    pendaftaran_t.kelaspelayanan_id,
                    pendaftaran_t.pegawai_id,
                    pendaftaran_t.penjamin_id,
                    pendaftaran_t.carabayar_id,
                    pendaftaran_t.umur,
                    ruangan_m.ruangan_nama as poliklinik,
                    kelaspelayanan_m.kelaspelayanan_nama AS kelaspelayanan_nama
                FROM
                    pendaftaran_t
                LEFT JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                LEFT JOIN lookup_m ON lookup_m.lookup_id = pasien_m.jeniskelamin::INT
                LEFT JOIN jeniskasuspenyakit_m ON jeniskasuspenyakit_m.jeniskasuspenyakit_id = pendaftaran_t.jeniskasuspenyakit_id
                LEFT JOIN pegawai_m ON pegawai_m.pegawai_id = pendaftaran_t.pegawai_id
                LEFT JOIN kelaspelayanan_m ON kelaspelayanan_m.kelaspelayanan_id = pendaftaran_t.kelaspelayanan_id
                LEFT JOIN ruangan_m ON ruangan_m.ruangan_id = pendaftaran_t.ruangan_id
                WHERE
                    pendaftaran_t.is_deleted = FALSE
            ";

        // filter
        if ($id) {
            $sql .= " AND pendaftaran_t.pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        $result = Pendaftaran::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }

    public function actionGetBundleDataAsesmenNyeri()
    {
    	return [
    		'list_asesmen_diambildari' => ArrayHelper::map($this->getLookupKeperawatanByType('asmen_dari')->asArray()->all(), 'lookupkeperawatan_id', 'lookup_name'),
    		'list_masuk_dengan' => ArrayHelper::map($this->getLookupKeperawatanByType('asmen_masukdengan')->asArray()->all(), 'lookupkeperawatan_id', 'lookup_name'),
    		'list_obat_darirumah' => ArrayHelper::map($this->getLookupKeperawatanByType('asmen_masukdengan')->asArray()->all(), 'lookupkeperawatan_id', 'lookup_name'),
            'list_ketergantungan' => ArrayHelper::map($this->getLookupKeperawatanByType('asmen_ketergantungan')->asArray()->all(), 'lookupkeperawatan_id', 'lookup_name'),
            'list_r_penyakit_kel' => ArrayHelper::map($this->getLookupKeperawatanByType('asmen_penyakit_kel')->asArray()->all(), 'lookupkeperawatan_id', 'lookup_name'),
    	];
    }

    public function getLookupKeperawatanByType($type = null)
    {
        $result = LookupKeperawatan::find();

        if ($type){
            $result->where(['lookup_type' => $type]);
        }

        return $result;
    }

    public function actionViewDiagnosisMasuk()
    {
        $request = Yii::$app->request;
        $post = $request->post();

        $result = Diagnosa::find();

        $result->select(['diagnosa_id','diagnosa_kode','CONCAT(diagnosa_kode,\' - \',diagnosa_nama) as diagnosa_nama']);
        if(!empty($post['keyword'])){
            $term = $post['keyword'];
            $result->andWhere(['like', 'LOWER(diagnosa_kode)', $term]);
            $result->orWhere(['like', 'LOWER(diagnosa_nama)', $term]);
        }
        return $result->asArray()->all();
    }
}
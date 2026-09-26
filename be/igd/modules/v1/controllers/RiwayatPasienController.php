<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use yii\db\Expression;
use yii\helpers\Html;

use Doco\Traits\HistoryPatientTrait;
use Doco\Traits\TindakanBmhpTrait;
use Doco\Traits\HistoryFisioTrait;

use app\modules\v1\models\InfoRiwayatPasienView;
use app\modules\v1\models\InfoResepturView;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\LaporanPemeriksaanLabView;
use app\modules\v1\models\InfoPasienLabView;
use app\modules\v1\models\InfoPasienLabDetailView;
use app\modules\v1\models\InputHasilLabView;
use app\modules\v1\models\NilaiPemeriksaanLabView;
use app\modules\v1\models\NilaiPemeriksaanLabDetailView;
use app\modules\v1\models\InfoPasienPindah;
use app\modules\v1\models\InfoPasienRadView;
use app\modules\v1\models\LaporanVisiteDokterView;
use app\modules\v1\models\PegawaiView;
use app\modules\v1\models\RiwayatTindakanView;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\InfoPermintaanKonsulView;
use app\modules\v1\models\AsuhanGiziView;
use app\modules\v1\models\FGetinstruksi;
use app\modules\v1\models\RiwayatPemeriksaanFisik;

use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\ProfilRumahSakitView;
use app\modules\v1\models\HasilPemeriksaanRad;
use app\modules\v1\models\InfoResepDetailView;
use app\modules\v1\models\InfoResepView;
use DateTime;
use Doco\components\DocoConstansId;
use Doco\Services\Cache;
use Doco\Traits\TerraMedikTrait;

// Class
class RiwayatPasienController extends DocoActiveController
{
    use HistoryPatientTrait {
        HistoryPatientTrait::actionDataHistoryPatient as private RiwayatDataPasien;
    }
    // use HistoryPatientTrait;
    use TindakanBmhpTrait;
    use HistoryFisioTrait;
    use TerraMedikTrait;

    // Model class
    public $modelClass = 'app\modules\v1\models\InfoRiwayatPasienView';


    public $InfoInstruksiModel;

    public function init()
    {
        parent::init();
        $this->InfoInstruksiModel = (new InfoInstruksiView);
    }

    // Verbs
    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);

        // Return
        return $actions;
    }
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $norm = $request->get('norm');
            $pendaftaran_id = $request->get('pendaftaran_id');
            $model = new InfoRiwayatPasienView;
            $query = $model::find();
            $query->where(['no_rekam_medik' => $norm]);
            $query->orderBy(['pendaftaran_id' => SORT_DESC]);
            if (isset($pendaftaran_id)) {
                $query->select([$pendaftaran_id]);
                $query->groupBy([$pendaftaran_id]);
                $query->having('count(*) > 1');
            }

            $filter  = $request->get('filter');
            $page    = $filter['page'];
            $perpage = $filter['per-page'];
            $offset  = ($page - 1) * $perpage;
            $record  = $query->limit($perpage)->offset($offset)->asArray()->all();

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $totalRecord = $query->count();

            return [
                'recordsFiltered' => $totalRecord,
                'recordsTotal' => $totalRecord,
                'data' => $record,
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

    public function actionListReseptur($pendaftaran_id)
    {
        $mReseptur = new InfoResepDetailView;
        $data_reseptur = $mReseptur->find()
            ->select([
                'reseptur_id',
                'noresep',
                'tglreseptur',
                'status_reseptur',
                'status_reseptur_id',
                'penjualanresep_id',
                // 'noresep_penjualan',
                'racikan_nama',
                'rke',
                'obatalkes_nama',
                'satuan_kecil',
                'signa_nama',
                'qty_reseptur',
                'iter',
                'status_periksa_nama',
                // 'signa',
            ])
            ->where(['pendaftaran_id' => $pendaftaran_id])
            ->orderBy([
                'tglreseptur' => SORT_DESC,
                'reseptur_id' => SORT_DESC,
                'rke' => SORT_ASC,
                'obatalkes_nama' => SORT_ASC
            ]);

        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'pendaftaran_t.umur'
            ])
            ->from('pendaftaran_t')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])
            ->one();
        return [
            'data_reseptur' => $data_reseptur->all(),
            'data_pasien' => $data_pasien
        ];
    }

    /**
     * @controller actionCetakResepturSatuan
     * @attribute #inf_alamatpasien# => Informasi alamt Pasien : Nama alamat Pasien
     * @attribute #inf_umur# => Informasi Pasien : Umur
     * @attribute #inf_namapasien# => Informasi Pasien : Nama Pasien
     * @attribute #inf_norekammedik# => Informasi Pasien : No Rekam Medik
     * @attribute #inf_tgllahir# => Informasi Pasien : Tanggal Lahir
     * @attribute #inf_jeniskelamin# => Informasi Pasien : Jenis Kelamin
     * @attribute #reseptur_noresep# => Informasi Reseptur : No Resep
     * @attribute #reseptur_tglresep# => Informasi Reseptur : Tanggal Resep
     * @attribute #reseptur_depotujuan# => Informasi Reseptur : Depo Tujuan
     * @attribute #reseptur_dokterperujuk# => Informasi Reseptur : Dokter Perujuk
     * @attribute #table_reseptur# => table detail obat
     **/
    public function actionCetakResepturSatuan()
    {
        $no_resep = Yii::$app->request->get('no_resep', '0');

        if (empty($no_resep)) {
            $no_resep = '0';
        }
        $reseptur = (new \yii\db\Query())
            ->select([
                'reseptur_t.noresep',
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'jeniskelamin.lookup_name AS jenis_kelamin',
                'pendaftaran_t.umur',
                'pasien_m.tanggal_lahir',
                'reseptur_t.tglreseptur',
                'depo.ruangan_nama AS depo_tujuan',
                'dokter.nama_pegawai AS dokter_perujuk'
            ])
            ->from('reseptur_t')
            ->leftJoin('pendaftaran_t', 'reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->leftJoin('lookup_m jeniskelamin', 'jeniskelamin.lookup_id = pasien_m.jeniskelamin::integer')
            ->leftJoin('ruangan_m depo', 'reseptur_t.ruangan_id = depo.ruangan_id')
            ->leftJoin('pegawai_m dokter', 'reseptur_t.pegawai_id = dokter.pegawai_id')
            ->where(['reseptur_t.noresep' => $no_resep])
            ->one();
        if ($reseptur == false) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => 'Resep Tidak Ditemukan'
            ];
        }

        $data_resep = InfoResepturDetailView::find()->where(['noresep' => $no_resep])->asArray()->all();

        $print = new DocoPrint();

        $print->attributes = [
            '#inf_alamatpasien#' => isset($reseptur['alamat_pasien']) ? $reseptur['alamat_pasien'] : '',
            '#inf_umur#' => isset($reseptur['umur']) ? $reseptur['umur'] : '',
            '#inf_namapasien#' => isset($reseptur['nama_pasien']) ? $reseptur['nama_pasien'] : '',
            '#inf_norekammedik#' => isset($reseptur['no_rekam_medik']) ? $reseptur['no_rekam_medik'] : '',
            '#inf_tgllahir#' => isset($reseptur['tanggal_lahir']) ? $reseptur['tanggal_lahir'] : '',
            '#inf_jeniskelamin#' => isset($reseptur['jenis_kelamin']) ? $reseptur['jenis_kelamin'] : '',
            '#reseptur_noresep#' => isset($reseptur['noresep']) ? $reseptur['noresep'] : '',
            '#reseptur_tglresep#' => isset($reseptur['tglreseptur']) ? date('d F Y', strtotime($reseptur['tglreseptur'])) : '',
            '#reseptur_depotujuan#' => isset($reseptur['depo_tujuan']) ? $reseptur['depo_tujuan'] : '',
            '#reseptur_dokterperujuk#' => isset($reseptur['dokter_perujuk']) ? $reseptur['dokter_perujuk'] : '',
            '#inf_namapasien#' => isset($reseptur['nama_pasien']) ? $reseptur['nama_pasien'] : '',
            '#inf_norekammedik#' => isset($reseptur['no_rekam_medik']) ? $reseptur['no_rekam_medik'] : '',
            '#inf_tgllahir#' => isset($reseptur['tanggal_lahir']) ? $reseptur['tanggal_lahir'] : '',
            '#inf_jeniskelamin#' => isset($reseptur['jenis_kelamin']) ? $reseptur['jenis_kelamin'] : '',
            '#reseptur_noresep#' => isset($reseptur['noresep']) ? $reseptur['noresep'] : '',
            '#reseptur_tglresep#' => isset($reseptur['tglreseptur']) ? $reseptur['tglreseptur'] : '',
            '#reseptur_depotujuan#' => isset($reseptur['depo_tujuan']) ? $reseptur['depo_tujuan'] : '',
            '#reseptur_dokterperujuk#' => isset($reseptur['dokter_perujuk']) ? $reseptur['dokter_perujuk'] : '',
            '#table_reseptur#' => $this->renderPartial('table_reseptur', [
                'data_resep' => $data_resep,
            ]),
        ];

        $print->Output();
    }

    public function actionListPenunjangLaboratorium($pendaftaran_id)
    {
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
        $condition = [
            'infopasienlab_v.pendaftaran_id' => $pendaftaran_id,
            // 'infopasienlab_v.pasienadmisi_id' => $pasienadmisi_id,
        ];
        $data_laboratorium = (new \yii\db\Query())
            ->select([
                'infopasienlab_v.no_rujukan',
                'inputhasillab_v.daftartindakan_nama', 
                'infopasienlab_v.tglmasukpenunjang',
                'infopasienlab_v.dokter_penunjang',
                'infopasienlab_v.ruangan_nama',
                'infopasienlab_v.no_pendaftaran',
                'infopasienlab_v.no_masukpenunjang',
                'infopasienlab_v.pasienmasukpenunjang_id',
                'infopasienlab_v.status_periksa_nama as stat_penunjang',
                'infopasienlab_v.status_periksa_nama',
                'infopasienlab_v.status_periksa_nama AS stat_periksa',
                'inputhasillab_v.tipe',
                'infopasienlab_v.is_hasil'
            ])
            ->from('infopasienlab_v')
            ->leftJoin("(select tipe, pasienmasukpenunjang_id,daftartindakan_nama from inputhasillab_v) inputhasillab_v", "infopasienlab_v.pasienmasukpenunjang_id = inputhasillab_v.pasienmasukpenunjang_id")
            ->where($condition)
            ->all();

        $infoPasienLab = InfoPasienLabView::find()
        ->where($condition)
        ->asArray()->all();
        $listRujukan = [];
        $dataLab = [];
        if(sizeof($infoPasienLab) > 1){
            // $listRujukan = ArrayHelper::getColumn($infoPasienLab, 'no_rujukan');
            foreach ($data_laboratorium as $val){
                // if(isset($val['no_rujukan']) && in_array($val['no_rujukan'], $listRujukan)){
                    $dataLab[] = $val;
                // }
            }
        }else{
            // $no_rujukan = !empty($infoPasienLab[0]['no_rujukan']) ? $infoPasienLab[0]['no_rujukan'] : null;
            foreach ($data_laboratorium as $val){
                // if (isset($val['no_rujukan']) && $val['no_rujukan'] == $no_rujukan){
                    $dataLab[] = $val;
                // }
            }
        }

        $data_pasien = $this->getDataPasien($pendaftaran_id, $pasienadmisi_id);

        return [
            'data_laboratorium' => $dataLab,
            'data_pasien' => $data_pasien
        ];
    }

    /**
     * @controller actionCetakHasilLaboratorium
     * @attribute #inf_namapasien# => Informasi Pasien : Nama Pasien
     * @attribute #inf_norekammedik# => Informasi Pasien : No Rekam Medik
     * @attribute #inf_umur# => Informasi Pasien : Umur
     * @attribute #inf_tgllahir# => Informasi Pasien : Tanggal Lahir
     * @attribute #inf_jeniskelamin# => Informasi Pasien : Jenis Kelamin
     * @attribute #inf_nopendaftaran# => Informasi Pasien : No Pendaftaran
     * @attribute #inf_carabayar# => Informasi Pasien : Cara Bayar
     * @attribute #inf_penjamin# => Informasi Pasien : Penjamin
     * @attribute #inf_kelaspelayanan# => Informasi Pasien : Kelas Pelayanan
     * @attribute #inf_ruanganasal# => Informasi Pasien : Ruangan Asal
     * @attribute #rujukan_norujukan# => Informasi Rujukan : No Rujukan
     * @attribute #hasillab_nohasil# => Hasil Lab: No Hasil
     * @attribute #hasillab_nolab# => Hasil Lab: No Laboratorium
     * @attribute #hasillab_tanggalhasil# => Hasil Lab: Tanggal Hasil
     * @attribute #hasillab_pj_lab# => Hasil Lab: Penanggung Jawab Laboratorium
     * @attribute #hasillab_dokterlab# => Hasil Lab: Dokter Laboratorium
     * @attribute #hasillab_kodespesimen# => Hasil Lab: Kode Spesimen
     * @attribute #hasillab_namaspesimen# => Hasil Lab: Nama Spesimen
     * @attribute #hasillab_expertise# => Hasil Lab: Expertise
     * @attribute #table_laboratorium# => table laboratorium
     **/
    public function actionCetakHasilLaboratorium()
    {
        $no_rujukan = Yii::$app->request->get('no_rujukan', '0');
        $no_masukpenunjang = Yii::$app->request->get('no_lab', '0');

        if (empty($no_rujukan)) {
            $no_rujukan = '0';
        }
        if (empty($no_masukpenunjang)) {
            $no_masukpenunjang = '0';
        }

        // $id = $get['id'];
        // $penunjang_id = $get['penunjang_id'];
        $data_pasien = InfoPasienLabView::find()->where(['no_masukpenunjang' => $no_masukpenunjang])->one();
        $data_hasil_lab = $this->getHasilLab($penunjang_id, $id);
        $data_hasil = $this->getHasilPemeriksaan($penunjang_id, $id);
        $detail_gol_mur = $this->getDetailNilaiRujukan($penunjang_id, $id);
        $nama_sample = !empty($data_hasil_lab['nama_sample'])
            ? $data_hasil_lab['nama_sample']
            : '';
        $tanggal_pemeriksaan = !empty($data_hasil['tgl_hasilpemeriksaanlab'])
            ? $data_hasil['tgl_hasilpemeriksaanlab']
            : date('Y-m-d H:i:s');
        $nohasilperiksalab = !empty($data_hasil['nohasilperiksalab'])
            ? $data_hasil['nohasilperiksalab']
            : '';
        if (!empty($data_hasil['pegawailab_id'])) {
            $pegawai = Pegawai::findOne($data_hasil['pegawailab_id']);
            $nama_pegawai = $pegawai->nama_pegawai;
        } else {
            $nama_pegawai = '';
        }
        $expertise = !empty($data_hasil['expertise'])
            ? $data_hasil['expertise']
            : '';
        $source_laboratorium = (new \yii\db\Query())
            ->select([
                'infoorderanlab_v.no_rujukan',
                'inputhasillab_v.daftartindakan_nama',
                'infopasienlab_v.tglmasukpenunjang',
                'hasilpemeriksaanlab_t.is_kritis',
                'infoorderanlab_v.status_penunjang',
                'infoorderanlab_v.stat_penunjang',
                'infoorderanlab_v.status_periksa',
                'statusperiksa_lab.lookup_name AS stat_periksa',
                'inputhasillab_v.jumlah',
                'inputhasillab_v.satuanlab_nama',
                'pemeriksaanlab_m.pemeriksaanlab_kode',
                'pemeriksaanlab_m.pemeriksaanlab_nama',
                'jenispemeriksaanlab_m.jenispemeriksaanlab_kode',
                'jenispemeriksaanlab_m.jenispemeriksaanlab_nama',
                'kelompokpemeriksaanlab_m.kode_kelompok',
                'kelompokpemeriksaanlab_m.nama_kelompok',
                'pegawailab.nama_pegawai AS pegawai_lab'
            ])
            ->from('infopasienlab_v')
            ->innerJoin('infoorderanlab_v', 'infopasienlab_v.pasienkirimkeunitlain_id = infoorderanlab_v.pasienkirimkeunitlain_id')
            ->rightJoin('inputhasillab_v', 'infopasienlab_v.pasienmasukpenunjang_id = inputhasillab_v.pasienmasukpenunjang_id')
            ->leftJoin('hasilpemeriksaanlab_t', 'inputhasillab_v.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id')
            ->leftJoin('lookup_m statusperiksa_lab', 'infoorderanlab_v.status_periksa::integer = statusperiksa_lab.lookup_id')
            ->leftJoin('tindakanpelayanan_t', 'inputhasillab_v.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id')
            ->leftJoin('pemeriksaanlab_m', 'tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id')
            ->leftJoin('jenispemeriksaanlab_m', 'pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id')
            ->leftJoin('kelompokpemeriksaanlab_m', 'jenispemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id')
            ->leftJoin('pegawai_m pegawailab', 'hasilpemeriksaanlab_t.pegawailab_id = pegawailab.pegawai_id')
            ->where(['infoorderanlab_v.no_rujukan' => $no_rujukan])
            ->orderBy(['tglmasukpenunjang' => SORT_DESC, 'daftartindakan_nama' => SORT_ASC])
            ->all();
        $data_laboratorium = [];
        foreach ($source_laboratorium as $val_source_lab) {
            $data_laboratorium[$val_source_lab['jenispemeriksaanlab_nama']][] = $val_source_lab;
        }

        $data_rujukan = (new \yii\db\Query())
            ->select([
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'pendaftaran_t.umur',
                'ruangan_m.ruangan_nama',
                'pendaftaran_t.no_pendaftaran',
                'jeniskelamin.lookup_name AS jenis_kelamin',
                'pasien_m.tanggal_lahir',
                'carabayar_m.carabayar_nama',
                'penjamin_m.penjamin_nama',
                'infoorderanlab_v.no_rujukan',
                'pendaftaran_t.no_pendaftaran',
                'kelaspelayanan_m.kelaspelayanan_nama',
                'hasilpemeriksaanlab_t.expertise',
                'hasilpemeriksaanlab_t.nohasilperiksalab',
                'hasilpemeriksaanlab_t.tgl_hasilpemeriksaanlab',
                'pasienmasukpenunjang_t.no_masukpenunjang',
                'dok_penunjang.nama_pegawai AS dokter_lab',
                'pj_lab.nama_pegawai AS penanggungjawab_lab',
                'spesimen.kode_sample',
                'spesimen.nama_sample'
            ])
            ->from('infoorderanlab_v')
            ->leftJoin('pendaftaran_t', 'infoorderanlab_v.pendaftaran_id = pendaftaran_t.pendaftaran_id')
            ->leftJoin('infopasienlab_v', 'infopasienlab_v.pasienkirimkeunitlain_id = infoorderanlab_v.pasienkirimkeunitlain_id')
            ->leftJoin('hasilpemeriksaanlab_t', 'infopasienlab_v.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id')
            ->leftJoin('samplelab_m spesimen', 'hasilpemeriksaanlab_t.samplelab_id = spesimen.samplelab_id')
            ->leftJoin('pasienmasukpenunjang_t', 'infopasienlab_v.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
            ->leftJoin('pegawai_m dok_penunjang', 'pasienmasukpenunjang_t.pegawai_id = dok_penunjang.pegawai_id')
            ->leftJoin('pegawai_m pj_lab', 'hasilpemeriksaanlab_t.pegawailab_id = pj_lab.pegawai_id')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->leftJoin('lookup_m jeniskelamin', 'jeniskelamin.lookup_id = pasien_m.jeniskelamin::integer')
            ->leftJoin('ruangan_m', 'pendaftaran_t.ruangan_id = ruangan_m.ruangan_id')
            ->leftJoin('carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
            ->leftJoin('penjamin_m', 'penjamin_m.penjamin_id = pendaftaran_t.penjamin_id')
            ->leftJoin('kelaspelayanan_m', 'pendaftaran_t.kelaspelayanan_id=kelaspelayanan_m.kelaspelayanan_id')
            ->where(['infoorderanlab_v.no_rujukan' => $no_rujukan])
            ->one();

        $print = new DocoPrint();

        $print->attributes = [
            '#inf_namapasien#' => isset($data_rujukan['nama_pasien']) ? $data_rujukan['nama_pasien'] : '',
            '#inf_norekammedik#' => isset($data_rujukan['no_rekam_medik']) ? $data_rujukan['no_rekam_medik'] : '',
            '#inf_umur#' => isset($data_rujukan['umur']) ? $data_rujukan['umur'] : '',
            '#inf_tgllahir#' => isset($data_rujukan['tanggal_lahir']) ? date('d F Y', strtotime($data_rujukan['tanggal_lahir'])) : '',
            '#inf_jeniskelamin#' => isset($data_rujukan['jenis_kelamin']) ? $data_rujukan['jenis_kelamin'] : '',
            '#inf_nopendaftaran#' => isset($data_rujukan['no_pendaftaran']) ? $data_rujukan['no_pendaftaran'] : '',
            '#inf_carabayar#' => isset($data_rujukan['carabayar_nama']) ? $data_rujukan['carabayar_nama'] : '',
            '#inf_penjamin#' => isset($data_rujukan['penjamin_nama']) ? $data_rujukan['penjamin_nama'] : '',
            '#inf_kelaspelayanan#' => isset($data_rujukan['kelaspelayanan_nama']) ? $data_rujukan['kelaspelayanan_nama'] : '',
            '#inf_ruanganasal#' => isset($data_rujukan['ruangan_nama']) ? $data_rujukan['ruangan_nama'] : '',
            '#hasillab_nohasil#' => isset($data_rujukan['nohasilperiksalab']) ? $data_rujukan['nohasilperiksalab'] : '',
            '#hasillab_nolab#' => isset($data_rujukan['no_masukpenunjang']) ? $data_rujukan['no_masukpenunjang'] : '',
            '#hasillab_tanggalhasil#' => isset($data_rujukan['tgl_hasilpemeriksaanlab']) ? date('d F Y - H:i:s', strtotime($data_rujukan['tgl_hasilpemeriksaanlab'])) : '',
            '#hasillab_pj_lab#' => isset($data_rujukan['penanggungjawab_lab']) ? $data_rujukan['penanggungjawab_lab'] : '',
            '#hasillab_dokterlab#' => isset($data_rujukan['dokter_lab']) ? $data_rujukan['dokter_lab'] : '',
            '#hasillab_kodespesimen#' => isset($data_rujukan['kode_sample']) ? $data_rujukan['kode_sample'] : '',
            '#hasillab_namaspesimen#' => isset($data_rujukan['nama_sample']) ? $data_rujukan['nama_sample'] : '',
            '#hasillab_expertise#' => isset($data_rujukan['expertise']) ? $data_rujukan['expertise'] : '',
            '#rujukan_norujukan#' => isset($data_rujukan['no_rujukan']) ? $data_rujukan['no_rujukan'] : '',
            '#table_laboratorium#' => $this->renderPartial('table_laboratorium', [
                'data_laboratorium' => $data_laboratorium,
            ]),
        ];
        $print->Output();
    }

    public function actionListPenunjangRadiologi($pendaftaran_id, $no_rekam_medik = null, $is_cppt = false)
    {
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
        $temp_rad = $temp_order = $conditions = [];

        if (Yii::$app->request->get('type') == 'riwayat') {
            $conditions['infopasienradiologi_v.pendaftaran_id'] = $pendaftaran_id;
            if (!is_null($pasienadmisi_id)) {
                // $conditions['infopasienradiologi_v.pasienadmisi_id'] = $pasienadmisi_id;
            } else if (!is_null(Yii::$app->request->get('instalasi_id')) && is_null($pasienadmisi_id)) {
                // $conditions['asalrujukan_id'] = Yii::$app->request->get('instalasi_id');
            }
        } else {
            $conditions['infopasienradiologi_v.pendaftaran_id'] = $pendaftaran_id;
        }
    	$data_radiologi = (new \yii\db\Query())
                ->select([
                    'infopasienradiologi_v.pendaftaran_id',
                    'infopasienradiologi_v.pasienmasukpenunjang_id',
                    'infopasienradiologi_v.daftartindakan_id',
                    'infopasienradiologi_v.tindakanpelayanan_id',
                    'infopasienradiologi_v.tglmasukpenunjang',
                    'infopasienradiologi_v.no_rujukan',
                    'infopasienradiologi_v.daftartindakan_nama',
                    'infopasienradiologi_v.tgl_verifikasi',
                    'hasilpemeriksaanrad_t.is_deleted',
                    'hasilpemeriksaanrad_t.hasilpemeriksaanrad_id',
                    'hasilpemeriksaanrad_t.is_hasilkritis',
                    'hasilpemeriksaanrad_t.no_hasilrad',
                    'infopasienradiologi_v.status_penunjang',
                    'statusperiksa_penunjangan.lookup_name AS stat_penunjang',
                    'infopasienradiologi_v.status_periksa',
                    'statusperiksa_rad.lookup_name AS stat_periksa',
                    'infopasienradiologi_v.no_pendaftaran',
                    'infopasienradiologi_v.dokter_penunjang',
                    'infopasienradiologi_v.ruangan_nama',
                    'infopasienradiologi_v.is_hasil',
                    'infopasienradiologi_v.tgl_verifikasi',
                    'infopasienradiologi_v.status_batal',
                    'infopasienradiologi_v.is_read',
                    'hasilbridgingradiologi_t.image_link',
                ])
                ->from('infopasienradiologi_v')
                ->leftJoin('hasilpemeriksaanrad_t','infopasienradiologi_v.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND infopasienradiologi_v.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id AND hasilpemeriksaanrad_t.is_deleted is false')
                ->leftJoin('lookup_m statusperiksa_rad','infopasienradiologi_v.status_periksa::integer = statusperiksa_rad.lookup_id')
                ->leftJoin('lookup_m statusperiksa_penunjangan','infopasienradiologi_v.status_penunjang::integer = statusperiksa_penunjangan.lookup_id')
                ->leftJoin('hasilbridgingradiologi_t','hasilbridgingradiologi_t.order_no = (infopasienradiologi_v.no_masukpenunjang || \'-\' || infopasienradiologi_v.tindakanpelayanan_id)')
                ->leftJoin('pasienmasukpenunjang_t', 'infopasienradiologi_v.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
                ->andWhere($conditions)
                ->groupBy(
                    'infopasienradiologi_v.pendaftaran_id,
                    infopasienradiologi_v.pasienmasukpenunjang_id,
                    infopasienradiologi_v.daftartindakan_id,
                    infopasienradiologi_v.tindakanpelayanan_id,
                    infopasienradiologi_v.tglmasukpenunjang,
                    infopasienradiologi_v.no_rujukan,
                    infopasienradiologi_v.tgl_verifikasi,
                    infopasienradiologi_v.daftartindakan_nama,
                    hasilpemeriksaanrad_t.is_deleted,
                    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.is_hasilkritis,
                    infopasienradiologi_v.status_penunjang,
                    infopasienradiologi_v.status_penunjang ,
                    infopasienradiologi_v.status_periksa,
                    statusperiksa_rad.lookup_name ,
                    infopasienradiologi_v.no_pendaftaran,
                    infopasienradiologi_v.dokter_penunjang,
                    infopasienradiologi_v.ruangan_nama,
                    infopasienradiologi_v.is_hasil,
                    infopasienradiologi_v.tgl_verifikasi,
                    infopasienradiologi_v.status_batal,
                    statusperiksa_penunjangan.lookup_name,
                    hasilbridgingradiologi_t.image_link,
                    infopasienradiologi_v.is_read'
                    
            )
            ->orderBy([
                'infopasienradiologi_v.tglmasukpenunjang' => SORT_DESC,
                'infopasienradiologi_v.daftartindakan_nama' => SORT_ASC
            ])
            ->all();
        $total_belum_baca = 0;
        foreach($data_radiologi as $key => $val){
            array_push($temp_rad,$val['no_rujukan']);
            if($val['is_hasil'] && $val['is_read'] == false && $val['tgl_verifikasi'] != null){
                $total_belum_baca++;
            }
        }

        if($is_cppt){
            return [
                'total_belum_baca' => $total_belum_baca,
            ];
        }

        $data_order_rad = (new \yii\db\Query())
        ->select([
            'pendaftaran_id',
            'no_rujukan',
            'tgl_rujukan as tglmasukpenunjang',
            'nama_pemeriksaan as daftartindakan_nama',
            'status_periksa',
            'status_penunjang',
            'stat_penunjang',
            'no_pendaftaran',
            'ruangan_nama',
        ])
        ->andWhere(['infoorderanrad_v.pendaftaran_id' => $pendaftaran_id])
        ->andWhere(['NOT IN', 'no_rujukan', $temp_rad])
        ->from('infoorderanrad_v')
        ->all();

        foreach ($data_order_rad as $key => $v) {
            $v['pasienmasukpenunjang_id'] = isset($v['pasienmasukpenunjang_id']) ? $v['pasienmasukpenunjang_id'] : null;
            $v['daftartindakan_id'] = isset($v['daftartindakan_id']) ? $v['daftartindakan_id'] : null;
            $v['tindakanpelayanan_id'] = isset($v['tindakanpelayanan_id']) ? $v['tindakanpelayanan_id'] : null;
            $v['tgl_verifikasi'] = isset($v['tgl_verifikasi']) ? $v['tgl_verifikasi'] : null;
            $v['is_deleted'] = isset($v['is_deleted']) ? $v['is_deleted'] : false;
            $v['no_hasilrad'] = isset($v['no_hasilrad']) ? $v['no_hasilrad'] : null;
            $v['is_hasilkritis'] = isset($v['is_hasilkritis']) ? $v['is_hasilkritis'] : null;
            $v['hasilpemeriksaanrad_id'] = isset($v['hasilpemeriksaanrad_id']) ? $v['hasilpemeriksaanrad_id'] : null;
            $v['stat_periksa'] = isset($v['stat_periksa']) ? $v['stat_periksa'] : ' - ';
            $v['dokter_penunjang'] = isset($v['dokter_penunjang']) ? $v['dokter_penunjang'] : ' - ';
            $v['image_link'] = isset($v['image_link']) ? $v['image_link'] : null;
            $temp_order[$key] = $v;
         }

        $data = array_merge($temp_order,$data_radiologi);
        $data_pasien = $this->getDataPasien($pendaftaran_id, $pasienadmisi_id);

        if($data_pasien['dokter_id'] == null && isset($data_pasien['dokter_admisi_id'])){
            $data_pasien['dokter_id'] = $data_pasien['dokter_admisi_id'];
        }

        $konfig_disable_button = (new DocoConstansId)->actionGetId(DocoConstants::KONFIG_DISABLE_BUTTON_HASIL_RADIOLOGI);

        return [
            'data_radiologi' => $data,
            'data_pasien' => $data_pasien,
            'total_belum_baca' => $total_belum_baca,
            'konfig_disable_button' => $konfig_disable_button
        ];
    }

    public function actionReadCetakanRadiologi()
    {
        $post = Yii::$app->request->get('post', null);
        if (!empty($post)) {
            HasilPemeriksaanRad::updateAll([
                'is_read' => true,
                ],
                [
                    'pasienmasukpenunjang_id' => $post['pasienmasukpenunjang_id'],
                    'daftartindakan_id' => $post['daftartindakan_id'],
                    'tindakanpelayanan_id' => $post['tindakanpelayanan_id'],
                ]);
            return $this->responseJson(200, 'data berhasil di update');
        } else {
            return $this->responseJson(400, 'params tidak boleh kosong');
        }
    }

    public function actionListLaporanTerapi($pendaftaran_id, $no_rekam_medik = null)
    {
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);
        $conditions = [
            'hasilpemeriksaanrad_t.is_deleted' => false
        ];
        if (Yii::$app->request->get('type') == 'riwayat') {
            $conditions['infopasienradiologi_v.pendaftaran_id'] = $pendaftaran_id;
            if (!is_null($pasienadmisi_id)) {
                $conditions['infopasienradiologi_v.pasienadmisi_id'] = $pasienadmisi_id;
            } else if (!is_null(Yii::$app->request->get('instalasi_id')) && is_null($pasienadmisi_id)) {
                $conditions['asalrujukan_id'] = Yii::$app->request->get('instalasi_id');
            }
        } else {
            $conditions['infopasienradiologi_v.no_rekam_medik'] = $no_rekam_medik;
        }
        $data_radiologi = (new \yii\db\Query())
            ->select([
                'infopasienradiologi_v.pendaftaran_id',
                'infopasienradiologi_v.pasienmasukpenunjang_id',
                'infopasienradiologi_v.daftartindakan_id',
                'infopasienradiologi_v.tindakanpelayanan_id',
                'infopasienradiologi_v.tglmasukpenunjang',
                'infopasienradiologi_v.no_rujukan',
                'infopasienradiologi_v.daftartindakan_nama',
                'hasilpemeriksaanrad_t.is_deleted',
                'hasilpemeriksaanrad_t.hasilpemeriksaanrad_id',
                'hasilpemeriksaanrad_t.is_hasilkritis',
                'infopasienradiologi_v.status_penunjang',
                'statusperiksa_penunjangan.lookup_name AS stat_penunjang',
                'infopasienradiologi_v.status_periksa',
                'statusperiksa_rad.lookup_name AS stat_periksa',
                'infopasienradiologi_v.no_pendaftaran',
                'infopasienradiologi_v.dokter_penunjang',
                'infopasienradiologi_v.ruangan_nama',
            ])
            ->from('infopasienradiologi_v')
            ->join('JOIN', 'hasilpemeriksaanrad_t', 'infopasienradiologi_v.pasienmasukpenunjang_id = hasilpemeriksaanrad_t.pasienmasukpenunjang_id AND infopasienradiologi_v.tindakanpelayanan_id = hasilpemeriksaanrad_t.tindakanpelayanan_id')
            ->leftJoin('lookup_m statusperiksa_rad', 'infopasienradiologi_v.status_periksa::integer = statusperiksa_rad.lookup_id')
            ->leftJoin('lookup_m statusperiksa_penunjangan', 'infopasienradiologi_v.status_penunjang::integer = statusperiksa_penunjangan.lookup_id')
            ->andWhere($conditions)
            ->andWhere(['IS NOT', 'infopasienradiologi_v.tgl_verifikasi', null])
            ->groupBy(
                'infopasienradiologi_v.pendaftaran_id,
                    infopasienradiologi_v.pasienmasukpenunjang_id,
                    infopasienradiologi_v.daftartindakan_id,
                    infopasienradiologi_v.tindakanpelayanan_id,
                    infopasienradiologi_v.tglmasukpenunjang,
                    infopasienradiologi_v.no_rujukan,
                    infopasienradiologi_v.daftartindakan_nama,
                    hasilpemeriksaanrad_t.is_deleted,
                    hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
                    hasilpemeriksaanrad_t.is_hasilkritis,
                    infopasienradiologi_v.status_penunjang,
                    infopasienradiologi_v.status_penunjang ,
                    infopasienradiologi_v.status_periksa,
                    statusperiksa_rad.lookup_name ,
                    infopasienradiologi_v.no_pendaftaran,
                    infopasienradiologi_v.dokter_penunjang,
                    infopasienradiologi_v.ruangan_nama,
                    statusperiksa_penunjangan.lookup_name,'
            )
            ->orderBy([
                'infopasienradiologi_v.tglmasukpenunjang' => SORT_DESC,
                'infopasienradiologi_v.daftartindakan_nama' => SORT_ASC
            ])
            ->all();

        $data_pasien = $this->getDataPasien($pendaftaran_id);
        return [
            'data_radiologi' => $data_radiologi,
            'data_pasien' => $data_pasien,
        ];
    }

    /**
     * @controller actionCetakHasilRadiologi
     * @attribute #inf_namapasien# => Informasi Pasien : Nama Pasien
     * @attribute #inf_norekammedik# => Informasi Pasien : No Rekam Medik
     * @attribute #inf_tgllahir# => Informasi Pasien : Tanggal Lahir
     * @attribute #inf_jeniskelamin# => Informasi Pasien : Jenis Kelamin
     * @attribute #inf_nopendaftaran# => Informasi Pasien : No Pendaftaran
     * @attribute #inf_carabayar# => Informasi Pasien : Cara Bayar
     * @attribute #inf_penjamin# => Informasi Pasien : Penjamin
     * @attribute #inf_kelaspelayanan# => Informasi Pasien : Kelas Pelayanan
     * @attribute #inf_ruanganasal# => Informasi Pasien : Ruangan Asal
     * @attribute #rujukan_norujukan# => Informasi Rujukan : No Rujukan
     * @attribute #table_radiologi# => table radiologi
     **/
    public function actionCetakHasilRadiologi()
    {
        $no_rujukan = Yii::$app->request->get('no_rujukan', '0');

        if (empty($no_rujukan)) {
            $no_rujukan = '0';
        }
        $data_radiologi = (new \yii\db\Query())
            ->select([
                'infoorderanrad_v.no_rujukan',
                'hasilpemeriksaanrad_v.daftartindakan_nama',
                'infopasienrad_v.tglmasukpenunjang',
                'hasilpemeriksaanrad_v.is_hasilkritis',
                'infoorderanrad_v.status_penunjang',
                'infoorderanrad_v.stat_penunjang',
                'infoorderanrad_v.status_periksa',
                'statusperiksa_rad.lookup_name AS stat_periksa'
            ])
            ->from('infopasienrad_v')
            ->innerJoin('infoorderanrad_v', 'infopasienrad_v.pasienkirimkeunitlain_id = infoorderanrad_v.pasienkirimkeunitlain_id')
            ->rightJoin('hasilpemeriksaanrad_v', 'infopasienrad_v.pasienmasukpenunjang_id = hasilpemeriksaanrad_v.pasienmasukpenunjang_id')
            ->leftJoin('lookup_m statusperiksa_rad', 'infoorderanrad_v.status_periksa::integer = statusperiksa_rad.lookup_id')
            ->where(['infoorderanrad_v.no_rujukan' => $no_rujukan])
            ->orderBy(['infopasienrad_v.tglmasukpenunjang' => SORT_DESC, 'hasilpemeriksaanrad_v.daftartindakan_nama' => SORT_ASC])
            ->all();

        $data_rujukan = (new \yii\db\Query())
            ->select([
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'pendaftaran_t.umur',
                'ruangan_m.ruangan_nama',
                'pendaftaran_t.no_pendaftaran',
                'jeniskelamin.lookup_name AS jenis_kelamin',
                'pasien_m.tanggal_lahir',
                'carabayar_m.carabayar_nama',
                'penjamin_m.penjamin_nama',
                'infoorderanlab_v.no_rujukan',
                'pendaftaran_t.no_pendaftaran',
                'kelaspelayanan_m.kelaspelayanan_nama'
            ])
            ->from('infoorderanlab_v')
            ->leftJoin('pendaftaran_t', 'infoorderanlab_v.pendaftaran_id = pendaftaran_t.pendaftaran_id')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->leftJoin('lookup_m jeniskelamin', 'jeniskelamin.lookup_id = pasien_m.jeniskelamin::integer')
            ->leftJoin('ruangan_m', 'pendaftaran_t.ruangan_id = ruangan_m.ruangan_id')
            ->leftJoin('carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
            ->leftJoin('penjamin_m', 'penjamin_m.penjamin_id = pendaftaran_t.penjamin_id')
            ->leftJoin('kelaspelayanan_m', 'pendaftaran_t.kelaspelayanan_id=kelaspelayanan_m.kelaspelayanan_id')
            ->where(['infoorderanlab_v.no_rujukan' => $no_rujukan])
            ->one();

        $print = new DocoPrint();

        $print->attributes = [
            '#inf_namapasien#' => isset($data_rujukan['nama_pasien']) ? $data_rujukan['nama_pasien'] : '',
            '#inf_norekammedik#' => isset($data_rujukan['no_rekam_medik']) ? $data_rujukan['no_rekam_medik'] : '',
            '#inf_tgllahir#' => isset($data_rujukan['tanggal_lahir']) ? $data_rujukan['tanggal_lahir'] : '',
            '#inf_jeniskelamin#' => isset($data_rujukan['jenis_kelamin']) ? $data_rujukan['jenis_kelamin'] : '',
            '#inf_nopendaftaran#' => isset($data_rujukan['no_pendaftaran']) ? $data_rujukan['no_pendaftaran'] : '',
            '#inf_carabayar#' => isset($data_rujukan['carabayar_nama']) ? $data_rujukan['carabayar_nama'] : '',
            '#inf_penjamin#' => isset($data_rujukan['penjamin_nama']) ? $data_rujukan['penjamin_nama'] : '',
            '#inf_kelaspelayanan#' => isset($data_rujukan['kelaspelayanan_nama']) ? $data_rujukan['kelaspelayanan_nama'] : '',
            '#inf_ruanganasal#' => isset($data_rujukan['ruangan_nama']) ? $data_rujukan['ruangan_nama'] : '',
            '#rujukan_norujukan#' => isset($data_rujukan['no_rujukan']) ? $data_rujukan['no_rujukan'] : '',
            '#table_radiologi#' => $this->renderPartial('table_radiologi', [
                'data_radiologi' => $data_radiologi,
            ]),
        ];

        $print->Output();
    }

    /**
     * @controller actionCetakHasilBedah
     * @attribute #inf_namapasien# => Informasi Pasien : Nama Pasien
     * @attribute #inf_norekammedik# => Informasi Pasien : No Rekam Medik
     * @attribute #inf_tgllahir# => Informasi Pasien : Tanggal Lahir
     * @attribute #inf_jeniskelamin# => Informasi Pasien : Jenis Kelamin
     * @attribute #inf_nopendaftaran# => Informasi Pasien : No Pendaftaran
     * @attribute #inf_carabayar# => Informasi Pasien : Cara Bayar
     * @attribute #inf_penjamin# => Informasi Pasien : Penjamin
     * @attribute #inf_kelaspelayanan# => Informasi Pasien : Kelas Pelayanan
     * @attribute #inf_ruanganasal# => Informasi Pasien : Ruangan Asal
     * @attribute #rujukan_norujukan# => Informasi Rujukan : No Rujukan
     * @attribute #table_radiologi# => table radiologi
     **/
    public function actionCetakHasilBedah()
    {
        $no_rujukan = Yii::$app->request->get('no_rujukan', '0');

        if (empty($no_rujukan)) {
            $no_rujukan = '0';
        }
        $data_bedah = (new \yii\db\Query())
            ->select([
                'infoorderanbedah_v.no_rujukan',
                'infopasienoperasidetail_v.daftartindakan_nama',
                'infopasienoperasi_v.tglmasukpenunjang',
                'infoorderanbedah_v.status_penunjang',
                'infoorderanbedah_v.stat_penunjang',
                'infoorderanbedah_v.status_periksa',
                'statusperiksa_operasi.lookup_name AS stat_periksa'
            ])
            ->from('infopasienoperasi_v')
            ->innerJoin('infoorderanbedah_v', 'infopasienoperasi_v.pasienkirimkeunitlain_id = infoorderanbedah_v.pasienkirimkeunitlain_id')
            ->rightJoin('infopasienoperasidetail_v', 'infopasienoperasi_v.pasienmasukpenunjang_id = infopasienoperasidetail_v.pasienmasukpenunjang_id')
            ->leftJoin('lookup_m statusperiksa_operasi', 'infoorderanbedah_v.status_periksa::integer = statusperiksa_operasi.lookup_id')
            ->where(['infoorderanbedah_v.no_rujukan' => $no_rujukan])
            ->orderBy(['infopasienoperasi_v.tglmasukpenunjang' => SORT_DESC, 'infopasienoperasidetail_v.daftartindakan_nama' => SORT_ASC])
            ->all();

        $data_rujukan = (new \yii\db\Query())
            ->select([
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'pendaftaran_t.umur',
                'ruangan_m.ruangan_nama',
                'pendaftaran_t.no_pendaftaran',
                'jeniskelamin.lookup_name AS jenis_kelamin',
                'pasien_m.tanggal_lahir',
                'carabayar_m.carabayar_nama',
                'penjamin_m.penjamin_nama',
                'infoorderanbedah_v.no_rujukan',
                'pendaftaran_t.no_pendaftaran',
                'kelaspelayanan_m.kelaspelayanan_nama'
            ])
            ->from('infoorderanbedah_v')
            ->leftJoin('pendaftaran_t', 'infoorderanbedah_v.pendaftaran_id = pendaftaran_t.pendaftaran_id')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->leftJoin('lookup_m jeniskelamin', 'jeniskelamin.lookup_id = pasien_m.jeniskelamin::integer')
            ->leftJoin('ruangan_m', 'pendaftaran_t.ruangan_id = ruangan_m.ruangan_id')
            ->leftJoin('carabayar_m', 'carabayar_m.carabayar_id = pendaftaran_t.carabayar_id')
            ->leftJoin('penjamin_m', 'penjamin_m.penjamin_id = pendaftaran_t.penjamin_id')
            ->leftJoin('kelaspelayanan_m', 'pendaftaran_t.kelaspelayanan_id=kelaspelayanan_m.kelaspelayanan_id')
            ->where(['infoorderanbedah_v.no_rujukan' => $no_rujukan])
            ->one();

        $print = new DocoPrint();

        $print->attributes = [
            '#inf_namapasien#' => isset($data_rujukan['nama_pasien']) ? $data_rujukan['nama_pasien'] : '',
            '#inf_norekammedik#' => isset($data_rujukan['no_rekam_medik']) ? $data_rujukan['no_rekam_medik'] : '',
            '#inf_tgllahir#' => isset($data_rujukan['tanggal_lahir']) ? $data_rujukan['tanggal_lahir'] : '',
            '#inf_jeniskelamin#' => isset($data_rujukan['jenis_kelamin']) ? $data_rujukan['jenis_kelamin'] : '',
            '#inf_nopendaftaran#' => isset($data_rujukan['no_pendaftaran']) ? $data_rujukan['no_pendaftaran'] : '',
            '#inf_carabayar#' => isset($data_rujukan['carabayar_nama']) ? $data_rujukan['carabayar_nama'] : '',
            '#inf_penjamin#' => isset($data_rujukan['penjamin_nama']) ? $data_rujukan['penjamin_nama'] : '',
            '#inf_kelaspelayanan#' => isset($data_rujukan['kelaspelayanan_nama']) ? $data_rujukan['kelaspelayanan_nama'] : '',
            '#inf_ruanganasal#' => isset($data_rujukan['ruangan_nama']) ? $data_rujukan['ruangan_nama'] : '',
            '#rujukan_norujukan#' => isset($data_rujukan['no_rujukan']) ? $data_rujukan['no_rujukan'] : '',
            // '#table_radiologi#' => $this->renderPartial('table_radiologi', [
            //     'data_bedah' => $data_bedah,
            // ]),
        ];

        $print->Output();
    }

    public function actionListPenunjangBedah($pendaftaran_id)
    {
        /*
        $data_bedah = (new \yii\db\Query())
            ->select([
                'infopasienoperasi_v.pasienmasukpenunjang_id',
                'infopasienoperasi_v.status_periksa',
                'infoorderanbedah_v.no_rujukan',
                'infopasienoperasidetail_v.daftartindakan_nama',
                'infopasienoperasidetail_v.daftartindakan_id',
                'infopasienoperasi_v.tglmasukpenunjang',
                'infoorderanbedah_v.status_penunjang',
                'infoorderanbedah_v.stat_penunjang',
                'infoorderanbedah_v.status_periksa',
                'infopasienoperasi_v.tgl_operasi',
                'infopasienoperasi_v.dok_operator',
                'infopasienoperasi_v.no_masukpenunjang AS no_operasi',
                'statusperiksa_operasi.lookup_name AS stat_periksa',
                'daftartindakan_m.is_akomodasi'
            ])
            ->from('infopasienoperasi_v')
            ->innerJoin('infoorderanbedah_v', 'infopasienoperasi_v.pasienkirimkeunitlain_id = infoorderanbedah_v.pasienkirimkeunitlain_id')
            ->rightJoin('infopasienoperasidetail_v', 'infopasienoperasi_v.pasienmasukpenunjang_id = infopasienoperasidetail_v.pasienmasukpenunjang_id')
            ->leftJoin('lookup_m statusperiksa_operasi', 'infoorderanbedah_v.status_periksa::integer = statusperiksa_operasi.lookup_id')
            ->leftJoin('daftartindakan_m', 'daftartindakan_m.daftartindakan_id = infopasienoperasidetail_v.daftartindakan_id')
            ->where([
                'infoorderanbedah_v.pendaftaran_id' => $pendaftaran_id,
                'daftartindakan_m.is_akomodasi' => false
                ])
            ->orderBy(['infopasienoperasi_v.tglmasukpenunjang' => SORT_DESC, 'infopasienoperasidetail_v.daftartindakan_nama' => SORT_ASC])
            ->all();
        */

        $data_laporan_operasi = (new \yii\db\Query())
        ->select([
            'laporanoperasi_r.laporanoperasi_id',
            'pasienmasukpenunjang_t.pasienmasukpenunjang_id',
            'pasienkirimkeunitlain_t.no_orderkeunitlain as no_rujukan',
            'pasienmasukpenunjang_t.no_masukpenunjang as no_operasi',
            'rencanaoperasi_t.tgl_permintaan as tgl_operasi',
            'laporanoperasi_r.nama_prosedur',
            'pasienmasukpenunjang_t.status_periksa',
            'laporanoperasi_r.dokter_id',
            'pegawai_m.nama_pegawai as dok_operator'
        ])
        ->from('pasienmasukpenunjang_t')
        ->innerJoin('laporanoperasi_r','laporanoperasi_r.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
        ->leftJoin('rencanaoperasi_t','rencanaoperasi_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
        ->leftJoin('pasienkirimkeunitlain_t','pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id')
        ->leftJoin('pegawai_m','pegawai_m.pegawai_id = laporanoperasi_r.dokter_id')
        ->where([
            'laporanoperasi_r.is_deleted' => FALSE,
            'pasienmasukpenunjang_t.pendaftaran_id' => $pendaftaran_id
        ])
        ->orderBy([
            'pasienmasukpenunjang_t.tglmasukpenunjang' => SORT_DESC,
            new \yii\db\Expression('laporanoperasi_r.nama_prosedur ASC NULLS LAST')
        ])
        ->all();
            
        $data_pasien = $this->getDataPasien($pendaftaran_id);
        return [
            'data_bedah' => $data_laporan_operasi,
            'data_pasien' => $data_pasien
        ];
    }

    public function getDataPasien($pendaftaran_id, $pasienadmisi_id = null)
    {
        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_id',
                'nama_pasien',
                'no_telepon_pasien',
                'alamat_pasien',
                'no_rekam_medik',
                'umur',
                'ruangan_nama',
                'no_pendaftaran',
                'jenis_kelamin',
                'tanggal_lahir',
                'carabayar_nama',
                'nama_pegawai as dokter_nama',
                'pegawai_id as dokter_id',
                'penjamin_nama',
                'pegawai_id as dokter_admisi_id',
                new \yii\db\Expression("CASE WHEN is_pasientitipan = true THEN kelas_ditagihkan_nama ELSE kelaspelayanan_nama END AS kelaspelayanan_nama"),
            ])
            ->from('infokunjunganrs_v')
            ->where(['pendaftaran_id' => $pendaftaran_id]);
        if (!is_null($pasienadmisi_id)) {
            $data_pasien->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
        }
           
        return $data_pasien->one();
    }

    /**
     * @controller actionCetakRiwayatPindahKamar
     * @attribute #lokasi_rs# => Informasi Lokasi RS : Lokasi RS
     * @attribute #dokter_dpjp# => Informasi Dokter DPJP : Nama Dokter DPJP
     * @attribute #nama_pegawai# => Informasi Pegawai : Nama Pegawai
     * @attribute #tanggal_dicetak# => Informasi Pegawai : Tanggal Dicetak
     * @attribute #waktu_dicetak# => Informasi Pegawai : Waktu Dicetak
     * @attribute #table_riwayat_pindah_kamar# => Table Riwayat Pindah Kamar
     **/
    public function actionCetakRiwayatPindahKamar($pendaftaran_id, $nama_pegawai, $no_rekam_medik)
    {
        $data_pindah_kamar = InfoPasienPindah::find()
            ->where([
                'no_rekam_medik' => $no_rekam_medik,
            ])
            ->orderBy([
                'tgl_pindahkamar' => SORT_ASC,
            ])->asArray()->all();

        if (!empty($data_pindah_kamar)) {
            foreach ($data_pindah_kamar as $key => $value) {
                $data_pindah_kamar[$key]['tgl_admisi'] = DocoHelpers::convDateTime($value['tgl_admisi'], false, true);
                $data_pindah_kamar[$key]['tgl_pindahkamar'] = DocoHelpers::convDateTime($value['tgl_pindahkamar'], false, true);
                $data_pindah_kamar[$key]['ruangan_sekarang'] = $value['ruangan_sekarang'] . ' - ' . $value['kamar_sekarang'] . ' - ' . $value['tempattidur_sekarang'];
                $data_pindah_kamar[$key]['ruangan_pindah'] = $value['ruangan_pindah'] . ' - ' . $value['kamar_pindah'] . ' - ' . $value['tempattidur_pindah'];
            }
        }

        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'pendaftaran_t.umur'
            ])
            ->from('pendaftaran_t')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->where(['pendaftaran_t.pendaftaran_id' => $pendaftaran_id])
            ->one();

        $lokasi_rs = Cache::getProfileRs();

        $print = new DocoPrint();
        $print->attributes = [
            '#lokasi_rs#' => ucwords(strtolower($lokasi_rs[0]['kota'])),
            '#dokter_dpjp#' => $data_pindah_kamar[count($data_pindah_kamar)-1]['dokter_admisi'],
            '#nama_pegawai#' => $nama_pegawai,
            '#tanggal_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, false),
            '#waktu_dicetak#' => date('H:i:s', strtotime('NOW')),
            '#table_riwayat_pindah_kamar#' => $this->renderPartial('table_riwayat_pindah_kamar', [
                'data_pindah_kamar' => $data_pindah_kamar,
            ]),
        ];
        $print->Output();
    }

    /**
     * @controller actionCetakRiwayatTindakanBmhp
     * @attribute #nama_pasien# => Informasi Pasien : Nama Pasien
     * @attribute #no_rekam_medik# => Informasi Pasien : Nomor Rekam Medik
     * @attribute #tanggal_lahir# => Informasi Pasien : Tanggal Lahir
     * @attribute #umur# => Informasi Pasien : Umur
     * @attribute #ruangan_kelas# => Informasi Pasien : Ruangan / Kelas
     * @attribute #dokter_dpjp# => Informasi Pasien : Dokter DPJP
     * @attribute #penjamin# => Informasi Pasien : Penjamin
     * @attribute #table_riwayat_tindakan# => Table Riwayat Tindakan
     * @attribute #table_riwayat_bmhp# => Table Riwayat BMHP
     **/
    public function actionCetakRiwayatTindakanBmhp($id, $no_pendaftaran)
    {
        $data_tindakan = (new \yii\db\Query())
            ->select([
                'tgl_tindakan',
                'tindakan_obat',
                'dokter_pemeriksa',
                'dokter_delegasi',
                'perawat_1',
                'perawat_2',
                'qty',
                'stat_implementasi',
                'instruksitindakan_id',
                'tipe_pelayanan',
            ])
            ->from(RiwayatTindakanView::tableName())
            ->where(['no_pendaftaran' => $no_pendaftaran, 'tipe_pelayanan' => 'TINDAKAN'])
            ->all();

        $data_paket = (new \yii\db\Query())
            ->select([
                'riwayat_tindakan.tgl_tindakan',
                'riwayat_tindakan.tindakan_obat',
                'riwayat_tindakan.dokter_pemeriksa',
                'riwayat_tindakan.dokter_delegasi',
                'riwayat_tindakan.perawat_1',
                'riwayat_tindakan.perawat_2',
                'riwayat_tindakan.qty',
                'riwayat_tindakan.stat_implementasi',
                'riwayat_tindakan.instruksitindakan_id',
                'riwayat_tindakan.tipe_pelayanan',
                'paket_detail.daftartindakan_nama',
            ])
            ->from(PaketDetailView::tableName() . ' paket_detail')
            ->andWhere(['no_pendaftaran' => $no_pendaftaran])
            ->andWhere(['riwayat_tindakan.tipe_pelayanan' => 'PAKET'])
            ->leftJoin(RiwayatTindakanView::tableName() . ' riwayat_tindakan', 'riwayat_tindakan.tipepaket_id = paket_detail.tipepaket_id')
            ->all();

        $data_tindakan = array_merge($data_tindakan, $data_paket);

        if (!empty($data_tindakan)) {
            $count_paket = [];

            foreach ($data_tindakan as $key => $value) {
                if ($value['tipe_pelayanan'] == 'PAKET') {
                    if (!isset($count_paket[$value['instruksitindakan_id']])) {
                        $count_paket[$value['instruksitindakan_id']] = 0;
                    }

                    $count_paket[$value['instruksitindakan_id']] = $count_paket[$value['instruksitindakan_id']] + 1;
                }
            }
        }

        $data_bmhp = (new \yii\db\Query())
            ->select(['*'])
            ->from(RiwayatTindakanView::tableName())
            ->where(['no_pendaftaran' => $no_pendaftaran, 'tipe_pelayanan' => 'BMHP'])
            ->all();

        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_v.nama_pasien',
                'pasien_v.no_rekam_medik',
                'pasien_v.tanggal_lahir',
                'pasien_v.jenis_kelamin',
                'infodatapendaftaran_v.umur',
                'infodatapendaftaran_v.ruangan_nama',
                'infodatapendaftaran_v.kelaspelayanan_nama',
                'infodatapendaftaran_v.nama_dok_ri',
                'infodatapendaftaran_v.penjamin_nama',
            ])
            ->from('infodatapendaftaran_v')
            ->leftJoin('pasien_v', 'infodatapendaftaran_v.pasien_id = pasien_v.pasien_id')
            ->where(['infodatapendaftaran_v.pendaftaran_id' => $id])
            ->one();

        $print = new DocoPrint();
        $print->attributes = [
            '#nama_pasien#' => $data_pasien['nama_pasien'],
            '#no_rekam_medik#' => $data_pasien['no_rekam_medik'],
            '#tanggal_lahir#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($data_pasien['tanggal_lahir'])), false, false),
            '#jenis_kelamin#' => $data_pasien['jenis_kelamin'],
            '#umur#' => $data_pasien['umur'],
            '#ruangan_kelas#' => $data_pasien['ruangan_nama'] . ' / ' . $data_pasien['kelaspelayanan_nama'],
            '#dokter_dpjp#' => $data_pasien['nama_dok_ri'],
            '#penjamin#' => $data_pasien['penjamin_nama'],
            '#table_riwayat_tindakan#' => $this->renderPartial('table_riwayat_tindakan', [
                'data_tindakan' => $data_tindakan,
                'count_paket' => $count_paket,
            ]),
            '#table_riwayat_bmhp#' => $this->renderPartial('table_riwayat_bmhp', [
                'data_bmhp' => $data_bmhp,
            ]),
        ];
        $print->Output();
    }

    /**
     * @controller actionCetakRiwayatVisiteDokter
     * @attribute #nama_pegawai# => Informasi Pegawai : Nama Pegawai
     * @attribute #tanggal_dicetak# => Informasi Pegawai : Tanggal Dicetak
     * @attribute #waktu_dicetak# => Informasi Pegawai : Waktu Dicetak
     * @attribute #table_riwayat_visite_dokter# => Table Riwayat Visite Dokter
     **/
    public function actionCetakRiwayatVisiteDokter($id, $norm, $ruangan_id)
    {
        $data_visite_dokter = LaporanVisiteDokterView::find()
            ->where([
                '"No. Rekam Medik"' => $norm,
            ])
            ->orderBy([
                '"Tanggal Visite"' => SORT_ASC,
            ])->asArray()->all();

        $data_kepala_ruangan = PegawaiView::find()->andWhere(['ruangan_id' => $ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();

        $nama_pegawai = '';
        if ($data_kepala_ruangan != '') {
            $nama_pegawai = $data_kepala_ruangan['nama_pegawai'];
        }

        if (!empty($data_visite_dokter)) {
            foreach ($data_visite_dokter as $key => $value) {
                $data_visite_dokter[$key]['Tanggal Admisi'] = DocoHelpers::convDateTime($value['Tanggal Admisi'], false, true);
                $data_visite_dokter[$key]['Tanggal Visite'] = DocoHelpers::convDateTime($value['Tanggal Visite'], false, true);
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#nama_pegawai#' => $nama_pegawai,
            '#tanggal_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, false),
            '#waktu_dicetak#' => date('H:i:s', strtotime('NOW')),
            '#table_riwayat_visite_dokter#' => $this->renderPartial('table_riwayat_visite_dokter', [
                'data_visite_dokter' => $data_visite_dokter,
            ]),
        ];
        $print->Output();
    }

    /**
     * @todo Action untuk mendapatkan data riwayat permintaan konsul
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetRiwayatPermintaanKonsul()
    {
        $params = Yii::$app->request;
        $id = $params->get('id');

        if ($id != '') {
            $model = InfoPermintaanKonsulView::find()->where(['permintaankonsul_id' => $id])->one();

            return $model;
        } else {
            $model = new InfoPermintaanKonsulView();

            $query = $model::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }
    }

    /**
     * @controller actionCetakRiwayatPermintaanKonsul
     * @attribute #nama_pegawai# => Informasi Pegawai : Nama Pegawai
     * @attribute #tanggal_dicetak# => Informasi Pegawai : Tanggal Dicetak
     * @attribute #waktu_dicetak# => Informasi Pegawai : Waktu Dicetak
     * @attribute #table_riwayat_permintaan_konsul# => Table Riwayat Permintaan Konsul
     **/
    public function actionCetakRiwayatPermintaanKonsul($id, $norm, $ruangan_id)
    {
        $data_permintaan_konsul = InfoPermintaanKonsulView::find()
            ->where([
                'no_rekam_medik' => $norm,
            ])
            ->orderBy([
                'waktu_permintaan' => SORT_ASC,
            ])->asArray()->all();

        $data_kepala_ruangan = PegawaiView::find()->andWhere(['ruangan_id' => $ruangan_id, 'jabatan_id' => DocoConstants::VAR_J_K_R])->one();

        $nama_pegawai = '';
        if ($data_kepala_ruangan != '') {
            $nama_pegawai = $data_kepala_ruangan['nama_pegawai'];
        }

        if (!empty($data_permintaan_konsul)) {
            foreach ($data_permintaan_konsul as $key => $value) {
                $data_permintaan_konsul[$key]['waktu'] = date('H:i:s', strtotime($value['waktu_permintaan']));
                $data_permintaan_konsul[$key]['tanggal_permintaan'] = DocoHelpers::convDateTime($value['waktu_permintaan'], false, false);
            }
        }

        $print = new DocoPrint();
        $print->attributes = [
            '#nama_pegawai#' => $nama_pegawai,
            '#tanggal_dicetak#' => DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime('NOW')), false, false),
            '#waktu_dicetak#' => date('H:i:s', strtotime('NOW')),
            '#table_riwayat_permintaan_konsul#' => $this->renderPartial('table_riwayat_permintaan_konsul', [
                'data_permintaan_konsul' => $data_permintaan_konsul,
            ]),
        ];
        $print->Output();
    }

    /**
     * @todo Action untuk mendapatkan data riwayat asuhan gizi
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetRiwayatAsuhanGizi()
    {
        $params = Yii::$app->request;
        $id = $params->get('id');

        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_m.no_rekam_medik',
                'pasien_m.nama_pasien',
                'pasien_m.tanggal_lahir',
                'pasien_m.alamat_pasien',
                'pendaftaran_t.umur',
                'lookup_m.lookup_name AS jenis_kelamin',
                'pegawai_m.nama_pegawai AS dok_dpjp',
            ])
            ->from('pendaftaran_t')
            ->leftJoin('pasien_m', 'pasien_m.pasien_id = pendaftaran_t.pasien_id')
            ->leftJoin('lookup_m', 'lookup_m.lookup_id = (pasien_m.jeniskelamin)::int')
            ->leftJoin('pegawai_m', 'pegawai_m.pegawai_id = pendaftaran_t.pegawai_id')
            ->where(['pendaftaran_t.pendaftaran_id' => $id])
            ->one();

        $data_gizi = (new \yii\db\Query())
            ->select([
                AsuhanGiziView::tableName() . '.*'
            ])
            ->from(AsuhanGiziView::tableName())
            ->where([AsuhanGiziView::tableName() . '.pendaftaran_id' => $id])
            ->orderBy([AsuhanGiziView::tableName() . '.created_date' => SORT_DESC])
            ->all();

        return [
            'data_pasien' => $data_pasien,
            'data_gizi' => $data_gizi
        ];
    }

    /**
     * @controller actionCetakPemeriksaanFisik
     * @attribute #poliklinik# => Untuk Menampilkan nama Poliklinik
     * @attribute #no_pendaftaran# => Untuk Menampilkan No Pendaftaran
     * @attribute #no_rm# => Untuk Menampilkan No Rekam Medik
     * @attribute #nama_pasien# => Untuk Menampilkan nama pasien
     * @attribute #jenis_kelamin# => Untuk Menampilkan nama pasien
     * @attribute #tanggal_lahir# => Untuk Menampilkan Jenis Kelamin
     * @attribute #cara_bayar# => Untuk Menampilkan cara Bayar
     * @attribute #penjamin# => Untuk Menampilkan Penjamin
     * @attribute #dokter_pemeriksa# => Untuk Menampilkan Dokter pemeriksa
     * @attribute #perawat# => Untuk Menampilkan Nama Perawat
     * @attribute #tanggal_periksa# => Untuk Menampilkan Tanggal periksa
     * @attribute #keadaan_umum# => Untuk Menampilkan Keadaan Umum
     * @attribute #tekanan_darah# => Untuk Menampilkan Tekanan Darah
     * @attribute #klasifikasitekanandarah# => Untuk Menampilkan Klasifikasi Tekanan Darah
     * @attribute #mean_arteri_preassure# => Untuk Menampilkan Mean Arteri Preassure
     * @attribute #detak_nadi# => Untuk Menampilkan Detak Nadi
     * @attribute #denyut_jantung# => Untuk Menampilkan Denyut Jantung
     * @attribute #pernafasan# => Untuk Menampilkan Pernafasan
     * @attribute #suhu_tubuh# => Untuk Menampilkan Suhu Tubuh
     * @attribute #tinggi_badan# => Untuk Menampilkan Tinggi Badan
     * @attribute #berat_badan# => Untuk Menampilkan Berat Badan
     * @attribute #massa_index_tubuh# => Untuk Menampilkan massa index tubuh
     * @attribute #kelainan_tubuh# => Untuk Menampilkan Kelainan pada bagian tubuh
     * @attribute #inspeksi# => Untuk Menampilkan Inspeksi
     * @attribute #palpasi# => Untuk Menampilkan Palpasi
     * @attribute #perkusi# => Untuk Menampilkan Perkusi
     * @attribute #auskultasi# => Untuk Menampilkan Auskultasi
     * @attribute #gcs_eye# => Untuk Menampilkan GCS Eye
     * @attribute #metodegcs_eye# => Untuk Menampilkan metode GCS Eye
     * @attribute #nilaigcs_eye# => Untuk Menampilkan nilai GCS Eye
     * @attribute #gcs_verbal# => Untuk Menampilkan GCS Verbal
     * @attribute #metodegcs_verbal# => Untuk Menampilkan metode GCS Verbal
     * @attribute #nilaigcs_verbal# => Untuk Menampilkan nilai GCS Verbal
     * @attribute #gcs_motorik# => Untuk Menampilkan GCS Motorik
     * @attribute #nilaigcs_motorik# => Untuk Menampilkan nilai GCS Motorik
     * @attribute #gcs_is_kapitis# => Untuk Menampilkan is kapitis
     * @attribute #gcs_kategori# => Untuk Menampilkan gsc nama
     * @attribute #hasil_metode_gcs# => Untuk Menampilkan Hasil Metode GCS
     * @attribute #pernapasan_gerakan# => Untuk Menampilkan List PERNAPASAN GERAKAN DADA
     * @attribute #jalan_nafas# => Untuk Menampilkan List JALAN NAFAS DAN PERNAFASAN
     * @attribute #sirkulasi# => Untuk Menampilkan List SIRKULASI
     * @attribute #gambar_anatomi# => Untuk Menampilkan Gambar Anatomi Tubuh
     * @attribute #list_tabel_anatomi# => Untuk Menampilkan List bagian anatomi tubuh
     * @attribute #tanggal_pemeriksaan# => Untuk Menampilkan Tanggal Pemeriksaan
     * @attribute #tgl_cetak# => Untuk Menampilkan Tanggal saat ini dicetak
     * @attribute #imt_kategori# => Untuk Menampilkan imt kategori / bmi_definisi
     * @attribute #umur# => Untuk Menampilkan umur Pasien
     * @attribute #kelaspelayanan_nama# => Untuk Menampilkan kelas pelayanan  Pasien
     * @attribute #tgl_pendaftaran# => Untuk Menampilkan tgl pendaftaran  Pasien
     * @attribute #jeniskasuspenyakit_nama# => Untuk Menampilkan Jenis Penyakit  Pasien
     * @attribute #status_periksa# => Untuk Menampilkan Status Periksa  Pasien
     **/
    public function actionCetakPemeriksaanFisik()
    {
        // Get params
        $id = Yii::$app->request->get('id');

        // Get pemeriksaan fisik
        $result = RiwayatPemeriksaanFisik::find()->where(['pendaftaran_id' => $id])->asArray()->one();
        $resultForTable = RiwayatPemeriksaanFisik::find()->where(['pendaftaran_id' => $id])->asArray()->all();
        $image = Yii::$app->urlManagerFrontend->createUrl('') . "media/img/img-pemeriksaan/bagian_tubuh.jpg";

        // Check model
        if (!empty($result)) {
            // Header
            $header = array(
                Yii::t('app', "Pemeriksaan Fisik") => 'PEMERIKSAAN FISIK',
            );

            // Print
            $print = new DocoPrint();

            // Cek berat badan
            if (isset($result['beratbadan_kg']) && $result['beratbadan_kg'] != '') {
                // Assign
                $beratBadan = $result['beratbadan_kg'];
            } else {
                // Assign
                $beratBadan = 0;
            }

            // Cek tinggi badan
            if (isset($result['tinggibadan_cm']) && $result['tinggibadan_cm'] != '') {
                // Assign
                $tinggiBadan = $result['tinggibadan_cm'];
            } else {
                // Assign
                $tinggiBadan = 0;
            }
            if (($tinggiBadan == 0) || ($beratBadan == 0)) {
                $bmi = 0;
            } else {
                // Menghitung BMI
                $bmi = $beratBadan / (($tinggiBadan / 100) * ($tinggiBadan / 100));
            }

            // Generate tabel pemeriksaan anatomi
            // Variable
            $no = 1;
            $htmlTable = '<table border="1" cellpadding="1" cellspacing="0" style="width:390px">';
            $htmlTable .= '<tbody>';
            $htmlTable .= '<tr>';
            $htmlTable .= '<th>No</th>';
            $htmlTable .= '<th>Bagian Tubuh</th>';
            $htmlTable .= '<th>Catatan</th>';
            $htmlTable .= '</tr>';

            // Loop
            foreach ($resultForTable as $value) {
                // Generate table
                $htmlTable .= '<tr><td>' . $no . '</td><td>' . $value['namabagtubuh'] . '</td><td>' . $value['catatan_tubuh'] . '</td></tr>';

                // Plus counter
                $no++;
            }

            // Close tag
            $htmlTable .= '</tbody>';
            $htmlTable .= '</table>';

            // Set checkbox
            $is_kapitis = isset($result['is_kapitis']) && $result['is_kapitis'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_paten = isset($result['jn_paten']) && $result['jn_paten'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_obstruktifpartial = isset($result['jn_obstruktifpartial']) && $result['jn_obstruktifpartial'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_obstruktifpartial = isset($result['jn_obstruktifpartial']) && $result['jn_obstruktifpartial'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_obstruktifnormal = isset($result['jn_obstruktifnormal']) && $result['jn_obstruktifnormal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_stridor = isset($result['jn_stridor']) && $result['jn_stridor'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $jn_gargling = isset($result['jn_gargling']) && $result['jn_gargling'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_normal = isset($result['pgp_normal']) && $result['pgp_normal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_kussmaul = isset($result['pgp_kussmaul']) && $result['pgp_kussmaul'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_takipnea = isset($result['pgp_takipnea']) && $result['pgp_takipnea'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_retraktif = isset($result['pgp_retraktif']) && $result['pgp_retraktif'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgp_dangkal = isset($result['pgp_dangkal']) && $result['pgp_dangkal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgd_simetri = isset($result['pgd_simetri']) && $result['pgd_simetri'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $pgd_asimetri = isset($result['pgd_asimetri']) && $result['pgd_asimetri'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $cfr_kecil_2 = isset($result['cfr_kecil_2']) && $result['cfr_kecil_2'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $cfr_besar_2 = isset($result['cfr_besar_2']) && $result['cfr_besar_2'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_normal = isset($result['kulit_normal']) && $result['kulit_normal'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_jaundice = isset($result['kulit_jaundice']) && $result['kulit_jaundice'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_cyanosis = isset($result['kulit_cyanosis']) && $result['kulit_cyanosis'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_pucat = isset($result['kulit_pucat']) && $result['kulit_pucat'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';
            $kulit_berkeringat = isset($result['kulit_berkeringat']) && $result['kulit_berkeringat'] == true ? '<input checked="checked" type="checkbox" />' : '<input type="checkbox" />';

            $nilaigcs_eye = isset($result['nilaigcs_eye']) ? $result['nilaigcs_eye'] : null;
            $nilaigcs_verbal = isset($result['nilaigcs_verbal']) ? $result['nilaigcs_verbal'] : null;
            $nilaigcs_motorik = isset($result['nilaigcs_motorik']) ? $result['nilaigcs_motorik'] : null;
            $hasilGcs = $nilaigcs_eye + $nilaigcs_verbal + $nilaigcs_motorik;
            // Assign attributes
            $print->attributes = [
                '#status_periksa#' => isset($result['status_periksa']) ? $result['status_periksa'] : null,
                '#jeniskasuspenyakit_nama#' => isset($result['jeniskasuspenyakit_nama']) ? $result['jeniskasuspenyakit_nama'] : null,
                '#umur#' => isset($result['umur']) ? $result['umur'] : null,
                '#kelaspelayanan_nama#' => isset($result['kelaspelayanan_nama']) ? $result['kelaspelayanan_nama'] : null,
                '#tgl_pendaftaran#' => isset($result['tgl_pendaftaran']) ? date('d F Y H:i:s', strtotime($result['tgl_pendaftaran'])) : null,
                '#poliklinik#' => isset($result['ruangan_nama']) ? $result['ruangan_nama'] : null,
                '#no_pendaftaran#' => isset($result['no_pendaftaran']) ? $result['no_pendaftaran'] : null,
                '#no_rm#' => isset($result['no_rekam_medik']) ? $result['no_rekam_medik'] : null,
                '#nama_pasien#' => isset($result['nama_pasien']) ? $result['nama_pasien'] : null,
                '#jenis_kelamin#' => isset($result['jenis_kelamin']) ? $result['jenis_kelamin'] : null,
                '#tanggal_lahir#' => isset($result['tanggal_lahir']) ? date('d F Y', strtotime($result['tanggal_lahir'])) : null,
                '#cara_bayar#' => isset($result['carabayar_nama']) ? $result['carabayar_nama'] : null,
                '#penjamin#' => isset($result['penjamin_nama']) ? $result['penjamin_nama'] : null,
                '#dokter_pemeriksa#' => isset($result['dokter']) ? $result['dokter'] : null,
                '#perawat#' => isset($result['perawat']) ? $result['perawat'] : null,
                '#tanggal_periksa#' => isset($result['tglperiksafisik']) ? date('d F Y', strtotime($result['tglperiksafisik'])) : null,
                '#keadaan_umum#' => isset($result['keadaanumum']) ? $result['keadaanumum'] : null,
                '#tekanan_darah#' => isset($result['tekanandarah']) ? $result['tekanandarah'] : null,
                '#klasifikasitekanandarah#' => isset($result['klasifikasitekanadarah']) ? $result['klasifikasitekanadarah'] : null,
                '#mean_arteri_preassure#' => isset($result['meanarteripressure']) ? $result['meanarteripressure'] : null,
                '#detak_nadi#' => isset($result['detaknadi']) ? $result['detaknadi'] : null,
                '#denyut_jantung#' => isset($result['denyutjantung']) ? $result['denyutjantung'] : null,
                '#pernafasan#' => isset($result['pernapasan']) ? $result['pernapasan'] : null,
                '#suhu_tubuh#' => isset($result['suhutubuh']) ? $result['suhutubuh'] : null,
                '#tinggi_badan#' => isset($result['tinggibadan_cm']) ? $result['tinggibadan_cm'] : null,
                '#bb_ideal#' => isset($result['bb_ideal']) ? $result['bb_ideal'] : null,
                '#massa_index_tubuh#' => number_format((float)$bmi, 2, '.', ''),
                '#kelainan_tubuh#' => isset($result['kelainanpadabagtubuh']) ? $result['kelainanpadabagtubuh'] : null,
                '#inspeksi#' => isset($result['inspeksi']) ? $result['inspeksi'] : null,
                '#palpasi#' => isset($result['palpasi']) ? $result['palpasi'] : null,
                '#perkusi#' => isset($result['perkusi']) ? $result['perkusi'] : null,
                '#auskultasi#' => isset($result['auskultasi']) ? $result['auskultasi'] : null,
                '#gcs_eye#' => isset($result['gcs_eye']) ? $result['gcs_eye'] : null,
                '#metodegcs_eye#' => isset($result['metodegcs_eye']) ? $result['metodegcs_eye'] : null,
                '#nilaigcs_eye#' => $nilaigcs_eye,
                '#gcs_verbal#' => isset($result['gcs_verbal']) ? $result['gcs_verbal'] : null,
                '#metodegcs_verbal#' => isset($result['metodegcs_verbal']) ? $result['metodegcs_verbal'] : null,
                '#nilaigcs_verbal#' => $nilaigcs_verbal,
                '#gcs_motorik#' => isset($result['gcs_motorik']) ? $result['gcs_motorik'] : null,
                '#metodegcs_motorik#' => isset($result['metodegcs_motorik']) ? $result['metodegcs_motorik'] : null,
                '#nilaigcs_motorik#' => $nilaigcs_motorik,
                '#gcs_is_kapitis#' => $is_kapitis,
                '#gcs_kategori#' => isset($result['gcs_nama']) ? $result['gcs_nama'] : null,
                '#hasil_metode_gcs#' => $hasilGcs,
                '#pernapasan_gerakan#' => isset($result['pernapasan_gerakan']) ? $result['pernapasan_gerakan'] : null,
                '#jalan_nafas#' => isset($result['jalan_nafas']) ? $result['jalan_nafas'] : null,
                '#sirkulasi#' => isset($result['sirkulasi']) ? $result['sirkulasi'] : null,
                '#gambar_anatomi#' => '
                    <div>
                    <img src="' . $image . '" width="350.0334" height="282.26">
                    </div>',
                '#list_tabel_anatomi#' => $htmlTable,
                '#tanggal_pemeriksaan#' => isset($result['tanggal_pemeriksaan']) ? $result['tanggal_pemeriksaan'] : null,
                '#berat_badan#' => isset($result['beratbadan_kg']) ? $result['beratbadan_kg'] : null,
                '#imt_kategori#' => isset($result['bmi_defenisi']) ? $result['bmi_defenisi'] : null,
                '#paten#' => $jn_paten,
                '#obstruktif_partial#' => $jn_obstruktifpartial,
                '#obstruktif_total#' => $jn_obstruktifnormal,
                '#stridor#' => $jn_stridor,
                '#gargling#' => $jn_gargling,
                '#normal#' => $pgp_normal,
                '#kussmaul#' => $pgp_kussmaul,
                '#takipnea#' => $pgp_takipnea,
                '#retraktif#' => $pgp_retraktif,
                '#dangkal#' => $pgp_dangkal,
                '#simetri#' => $pgd_simetri,
                '#asimetri#' => $pgd_asimetri,
                '#sirkulasi_nadicarotis#' => isset($result['sirkulasi_nadicarotis']) ? $result['sirkulasi_nadicarotis'] : null,
                '#sirkulasi_nadiradialis#' => isset($result['sirkulasi_nadiradialis']) ? $result['sirkulasi_nadiradialis'] : null,
                '#cfr_kecil_2#' => $cfr_kecil_2,
                '#cfr_besar_2#' => $cfr_besar_2,
                '#kulit_normal#' => $kulit_normal,
                '#kulit_jaundice#' => $kulit_jaundice,
                '#kulit_cyanosis#' => $kulit_cyanosis,
                '#kulit_pucat#' => $kulit_pucat,
                '#kulit_berkeringat#' => $kulit_berkeringat,
                '#akral#' => isset($result['akral']) ? $result['akral'] : null,
                '#tgl_cetak#' => date('d F Y'),
            ];
            // Print output
            $print->Output();
        }
    }

    public function actionListResepturRajal($id)
    {
        $mReseptur = new InfoResepView;
        $data_reseptur = $mReseptur->find()
            ->select([
                'pendaftaran_id',
                'reseptur_id',
                'resep_id',
                'no_resep',
                'no_reseptur',
                'noresep_penjualan',
                'tglreseptur',
                'tglresep',
                'status_reseptur',
            ])
            ->where(['pendaftaran_id' => $id])
            ->orderBy([
                'tglreseptur' => SORT_DESC,
                'reseptur_id' => SORT_DESC,
            ]);

        $data_pasien = (new \yii\db\Query())
            ->select([
                'pasien_m.nama_pasien',
                'pasien_m.alamat_pasien',
                'pasien_m.no_rekam_medik',
                'pendaftaran_t.umur'
            ])
            ->from('pendaftaran_t')
            ->leftJoin('pasien_m', 'pendaftaran_t.pasien_id = pasien_m.pasien_id')
            ->where(['pendaftaran_t.pendaftaran_id' => $id])
            ->one();

        return [
            'data_reseptur' => $data_reseptur->all(),
            'data_pasien' => $data_pasien
        ];
    }

    public function actionFetchListTindakan()
    {
        $get = Yii::$app->request->get();
        $pasien_id = Yii::$app->request->get('pasien_id');
        $startDate = empty($get['startDate']) ? date('Y-m-d H:i:s', strtotime('1970-01-01 00:00:00')) : DateTime::createFromFormat('d/m/Y', $get['startDate'])->format('Y-m-d') . ' 00:00:00';
        $endDate = empty($get['endDate']) ? date('Y-m-d H:i:s') : DateTime::createFromFormat('d/m/Y', $get['endDate'])->format('Y-m-d') . ' 23:59:59';
        $jenis = empty($get['jenis']) ? new \yii\db\Expression('') : $get['jenis'];
        $instruksi = empty($get['instruksi']) ? new \yii\db\Expression('') : $get['instruksi'];
        $type = ArrayHelper::getValue($get, 'type');
        $pendaftaran_id = $type == 'igdLaporanTerapi' ? new \yii\db\Expression('NULL') : ArrayHelper::getValue($get, 'type');
        $kelompoktindakan_id = "'".DocoConstants::VAR_KEL_KRCS . ',' . DocoConstants::KELOMPOK_MAKANAN."'";
        $limit = ArrayHelper::getValue($get, 'length');
        $offset = ArrayHelper::getValue($get, 'start');

        $data = (new FGetinstruksi([
            'extParam' =>  $pasien_id . ",'" . $startDate . "','" . $endDate . "','" . $jenis . "','" . $instruksi . "'," . $pendaftaran_id . "," . $kelompoktindakan_id . "," . $limit . "," . $offset
        ]))->find()->select([
                'INITCAP(tipe_instruksi) as jenis',
                'INITCAP(grouping_tipe) as grouping_tipe',
                'tanggal_terapi as tgl_tindakan',
                'instruksi',
                "dokter as nama_pegawai",
                'pendaftaran_id',
                'no_pendaftaran',
                'ruangan_pertindakan as ruangan_penunjang_nama',
                'tindakaninstruksi_nama',
                'pasienkirimkeunitlain_id',
                'instalasi_id AS instalasi_penunjang_id',
                'instalasi_nama as instalasi_penunjang_nama',
                'is_bayar',
                'tindakan_deleted as deleted',
                'instruksitindakan_id',
                'instruksi_id',
                'status_implementasi',
                'is_pulang',
                'noresep',
                'qty',
                'satuankecil_nama',
                'status_bmhp_id',
                'is_telah_implementasi',
                'alasan_batal',
                "json_agg(json_build_object('tipe_instruksi',tipe_instruksi, 'instruksi', instruksi, 'tindakaninstruksi_nama', tindakaninstruksi_nama, 'instruksitindakan_id', instruksitindakan_id, 'qty', qty, 'satuankecil_nama', satuankecil_nama)) as json_instruksi"
            ]);

        switch (Yii::$app->request->get('type', 'igd')) {
            case 'igd':
                $data = $data->where([
                        'IS', 'pasienadmisi_id', null
                    ]);
                break;
            case 'igdLaporanTerapi':
                break;
            default:
                $data = $data->where([
                        'IS', 'pasienadmisi_id', null
                    ]);
                break;
        }

        $data = $data->groupBy([
                    'tipe_instruksi',
                    'grouping_tipe',
                    'tanggal_terapi',
                    'instruksi',
                    'instruksi_id',
                    'dokter',
                    'pendaftaran_id',
                    'no_pendaftaran',
                    'ruangan_pertindakan',
                    'tindakaninstruksi_nama',
                    'pasienkirimkeunitlain_id',
                    'instalasi_id',
                    'instalasi_nama',
                    'is_bayar',
                    'tindakan_deleted',
                    'instruksitindakan_id',
                    'status_implementasi',
                    'is_pulang',
                    'noresep',
                    'qty',
                    'satuankecil_nama',
                    'tgl_instruksi',
                    'status_bmhp_id',
                    'is_telah_implementasi',
                    'alasan_batal'
                ])
                ->orderBy([
                    'tgl_instruksi' => SORT_DESC
                ]);

        $listData = $data->asArray()->all();
        $totalData = count($listData);


        $result = [];
        $arr = [];
        foreach($listData as $v) {
            if($v['grouping_tipe'] == 'Reseptur') {
                if(!isset($result[$v["noresep"]])) {
                    $result[$v["noresep"]]["grouping_tipe"] = $v["grouping_tipe"];
                    $result[$v["noresep"]]["tgl_tindakan"] = $v["tgl_tindakan"];
                    $result[$v["noresep"]]["nama_pegawai"] = $v["nama_pegawai"];
                    $result[$v["noresep"]]["pendaftaran_id"] = $v["pendaftaran_id"];
                    $result[$v["noresep"]]["no_pendaftaran"] = $v["no_pendaftaran"];
                    $result[$v["noresep"]]["ruangan_penunjang_nama"] = $v["ruangan_penunjang_nama"];
                    $result[$v["noresep"]]["pasienkirimkeunitlain_id"] = $v["pasienkirimkeunitlain_id"];
                    $result[$v["noresep"]]["instalasi_penunjang_id"] = $v["instalasi_penunjang_id"];
                    $result[$v["noresep"]]["instalasi_penunjang_nama"] = $v["instalasi_penunjang_nama"];
                    $result[$v["noresep"]]["is_bayar"] = $v["is_bayar"];
                    $result[$v["noresep"]]["deleted"] = $v["deleted"];
                    $result[$v["noresep"]]["status_implementasi"] = $v["status_implementasi"];
                    $result[$v["noresep"]]["is_pulang"] = $v["is_pulang"];
                    $result[$v["noresep"]]["noresep"] = $v["noresep"];
                    $result[$v["noresep"]]["jenis"][] = $v["jenis"];
                    $result[$v["noresep"]]["instruksi"][] = $v["instruksi"];
                    $result[$v["noresep"]]["tindakaninstruksi_nama"][] = $v["tindakaninstruksi_nama"];
                    $result[$v["noresep"]]["instruksitindakan_id"][] = $v["instruksitindakan_id"];
                    $result[$v["noresep"]]["qty"][] = $v["qty"];
                    $result[$v["noresep"]]["satuankecil_nama"][] = $v["satuankecil_nama"];
                    $result[$v["noresep"]]["status_bmhp_id"][] = $v["status_bmhp_id"];
                    $result[$v["noresep"]]["is_telah_implementasi"][] = $v["is_telah_implementasi"];
                    
                } else {
                    $result[$v["noresep"]]["grouping_tipe"] = $v["grouping_tipe"];
                    $result[$v["noresep"]]["tgl_tindakan"] = $v["tgl_tindakan"];
                    $result[$v["noresep"]]["nama_pegawai"] = $v["nama_pegawai"];
                    $result[$v["noresep"]]["pendaftaran_id"] = $v["pendaftaran_id"];
                    $result[$v["noresep"]]["no_pendaftaran"] = $v["no_pendaftaran"];
                    $result[$v["noresep"]]["ruangan_penunjang_nama"] = $v["ruangan_penunjang_nama"];
                    $result[$v["noresep"]]["pasienkirimkeunitlain_id"] = $v["pasienkirimkeunitlain_id"];
                    $result[$v["noresep"]]["instalasi_penunjang_id"] = $v["instalasi_penunjang_id"];
                    $result[$v["noresep"]]["instalasi_penunjang_nama"] = $v["instalasi_penunjang_nama"];
                    $result[$v["noresep"]]["is_bayar"] = $v["is_bayar"];
                    $result[$v["noresep"]]["deleted"] = $v["deleted"];
                    $result[$v["noresep"]]["status_implementasi"] = $v["status_implementasi"];
                    $result[$v["noresep"]]["is_pulang"] = $v["is_pulang"];
                    $result[$v["noresep"]]["jenis"][] = $v["jenis"];
                    $result[$v["noresep"]]["instruksi"][] = $v["instruksi"];
                    $result[$v["noresep"]]["tindakaninstruksi_nama"][] = $v["tindakaninstruksi_nama"];
                    $result[$v["noresep"]]["instruksitindakan_id"][] = $v["instruksitindakan_id"];
                    $result[$v["noresep"]]["qty"][] = $v["qty"];
                    $result[$v["noresep"]]["satuankecil_nama"][] = $v["satuankecil_nama"];
                    $result[$v["noresep"]]["status_bmhp_id"][] = $v["status_bmhp_id"];
                    $result[$v["noresep"]]["is_telah_implementasi"][] = $v["is_telah_implementasi"];
                }
            }else {

                $arr[] = $v;
            }
        }
       $data_merge = array_merge($arr,array_values($result));
       usort($data_merge, function($a, $b){
            return $a['tgl_tindakan'] <= $b['tgl_tindakan'];
        });
        return [
            'data' => $data_merge,
            'load_more' => $totalData == $limit ? true : false
        ];
    }

    public function actionDataHistoryPatient()
    {
        return $this->RiwayatDataPasien();
    }
}
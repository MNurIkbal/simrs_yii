<?php

namespace app\modules\v1\actions\LapKunjunganPenunjang;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoKunjunganPenunjang;

class GetDataAction extends BaseCurrentAction
{
    public static function getQueryDataFromReq($advancedFilters)
    {
        $advancedFilters = ArrayHelper::getValue($_GET, 'advanced-filter');
        $model = new InfoKunjunganPenunjang();
        $query = $model::find();

        $startTgl = date('Y-m-d') . ' 00:00:00';
        $endTgl = date('Y-m-d') . ' 23:59:59';

        $tglFilter = ArrayHelper::getValue($advancedFilters, 'tglmasukpenunjang');
        $instalasiFilter = ArrayHelper::getValue($advancedFilters, 'instalasi_nama');
        $ruanganFilter = ArrayHelper::getValue($advancedFilters, 'ruangan_nama');
        $noPendaftaranFilter = ArrayHelper::getValue($advancedFilters, 'no_pendaftaran'); // Auto Filter with helpers
        $caraBayarFilter = ArrayHelper::getValue($advancedFilters, 'carabayar_id'); // Auto Filter with helpers
        $penjaminFilter = ArrayHelper::getValue($advancedFilters, 'penjamin_nama');
        $jenisKegiatanFilter = ArrayHelper::getValue($advancedFilters, 'jeniskegiatantindakan_nama');
        $daftarTindakanFilter = ArrayHelper::getValue($advancedFilters, 'daftartindakan_nama');
        $unitFilter = ArrayHelper::getValue($advancedFilters, 'unit');

        if ($tglFilter) {
            $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
            if (count($explode) == 2) {
                $startTgl = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $endTgl = date('Y-m-d 23:59:00', strtotime($explode[1]));
            }
            unset($_GET['advanced-filter']['tglmasukpenunjang']);
        }
        if ($instalasiFilter) {
            $_GET['advanced-filter']['instalasi_id'] = $_GET['advanced-filter']['instalasi_nama'];
            unset($_GET['advanced-filter']['instalasi_nama']);
        }
        if ($ruanganFilter) {
            $_GET['advanced-filter']['ruangan_id'] = $_GET['advanced-filter']['ruangan_nama'];
            unset($_GET['advanced-filter']['ruangan_nama']);
        }
        if ($penjaminFilter) {
            $_GET['advanced-filter']['penjamin_id'] = $_GET['advanced-filter']['penjamin_nama'];
            unset($_GET['advanced-filter']['penjamin_nama']);
        }
        if ($jenisKegiatanFilter) {
            $jeniskegiatantindakan_nama = $_GET['advanced-filter']['jeniskegiatantindakan_nama'];
            $query->andFilterWhere([
                'and',
                ['ilike', 'jeniskegiatantindakan_nama', $jeniskegiatantindakan_nama],
            ]);
            unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
        }
        if ($daftarTindakanFilter) {
            $daftartindakan_nama = $_GET['advanced-filter']['daftartindakan_nama'];
            $query->andFilterWhere([
                'and',
                ['ilike', 'daftartindakan_nama', $daftartindakan_nama],
            ]);
            unset($_GET['advanced-filter']['daftartindakan_nama']);
        }
        if ($unitFilter) {
            $query->andFilterWhere([
                'and',
                ['ilike', 'unit', $_GET['advanced-filter']['unit']],
            ]);
            unset($_GET['advanced-filter']['unit']);
        }

        $query->andWhere(['between', 'tglmasukpenunjang', $startTgl, $endTgl]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return $query;
    }

    public static function generateFooterTotalRow($isUnset = false)
    {
        $request = Yii::$app->request;
        $conditionQuery = "";

        $start   = date('Y-m-d 00:00:00');
        $end     = date('Y-m-d 23:59:59');

        // return $request->get();
        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglmasukpenunjang'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmasukpenunjang']);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                if ($isUnset) {
                    unset($_GET['advanced-filter']['tglmasukpenunjang']);
                }
            }

            if (isset($_GET['advanced-filter']['no_pendaftaran'])) {
                $conditionQuery .= " AND no_pendaftaran ILIKE '%" . $_GET['advanced-filter']['no_pendaftaran'] . "%'";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['no_pendaftaran']);
                }
            }

            if (isset($_GET['advanced-filter']['no_rekam_medik'])) {
                $conditionQuery .= " AND no_rekam_medik = '" . $_GET['advanced-filter']['no_rekam_medik'] . "' ";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['no_rekam_medik']);
                }
            }

            if (isset($_GET['advanced-filter']['nama_pasien'])) {
                $conditionQuery .= " AND nama_pasien ILIKE '%" . $_GET['advanced-filter']['nama_pasien'] . "%'";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['nama_pasien']);
                }
            }

            if (isset($_GET['advanced-filter']['instalasi_nama'])) {
                $conditionQuery .= " AND instalasi_id = " . $_GET['advanced-filter']['instalasi_nama'] . "";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['instalasi_nama']);
                }
            }

            if (isset($_GET['advanced-filter']['ruangan_id'])) {
                $conditionQuery .= " AND ruangan_id = " . $_GET['advanced-filter']['ruangan_id'] . "";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['ruangan_id']);
                }
            }

            if (isset($_GET['advanced-filter']['carabayar_id'])) {
                $conditionQuery .= " AND carabayar_id = " . $_GET['advanced-filter']['carabayar_id'] . "";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['carabayar_id']);
                }
            }

            if (isset($_GET['advanced-filter']['penjamin_nama']) && $_GET['advanced-filter']['penjamin_nama'] != self::LOADING_VALUE) {
                $conditionQuery .= " AND penjamin_id = " . $_GET['advanced-filter']['penjamin_nama'] . "";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['penjamin_nama']);
                }
            }

            if (isset($_GET['advanced-filter']['jeniskegiatantindakan_nama']) && $_GET['advanced-filter']['jeniskegiatantindakan_nama'] != self::LOADING_VALUE) {
                $conditionQuery .= " AND jeniskegiatantindakan_nama ILIKE '%" . $_GET['advanced-filter']['jeniskegiatantindakan_nama'] . "%'";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['jeniskegiatantindakan_nama']);
                }
            }

            if (isset($_GET['advanced-filter']['daftartindakan_nama'])) {
                $conditionQuery .= " AND daftartindakan_nama ILIKE '%" . $_GET['advanced-filter']['daftartindakan_nama'] . "%'";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['daftartindakan_nama']);
                }
            }

            if (isset($_GET['advanced-filter']['unit']) && $_GET['advanced-filter']['unit'] != self::LOADING_VALUE) {
                $conditionQuery .= " AND unit ILIKE '%" . $_GET['advanced-filter']['unit'] . "%'";
                if ($isUnset) {
                    unset($_GET['advanced-filter']['unit']);
                }
            }
        }

        $sqlJenisKegiatan = "
            SELECT sum(tmp) as hasil_jenis from (
            SELECT 1 as tmp, jeniskegiatantindakan_nama from laporankunjunganpenunjang_v WHERE tglmasukpenunjang between :dateStart and :dateEnd {$conditionQuery} GROUP BY jeniskegiatantindakan_nama) a";
        $tmpJenisKegiatan = Yii::$app->db->createCommand($sqlJenisKegiatan)
            ->bindValue(':dateStart', $start)
            ->bindValue(':dateEnd', $end)
            ->queryOne();

        $sqlDaftarTindakan = "
            SELECT sum(tmp) as hasil_tindakan from (
            SELECT 1 as tmp, daftartindakan_nama from laporankunjunganpenunjang_v WHERE tglmasukpenunjang between :dateStart and :dateEnd {$conditionQuery} GROUP BY daftartindakan_nama) a";
        $tmpDaftarTindakan = Yii::$app->db->createCommand($sqlDaftarTindakan)
            ->bindValue(':dateStart', $start)
            ->bindValue(':dateEnd', $end)
            ->queryOne();

        $sqlJumlahTindakan = "
            SELECT SUM(jumlah_tindakan) as hasil_jumlah_tindakan 
            FROM laporankunjunganpenunjang_v 
            WHERE tglmasukpenunjang between :dateStart and :dateEnd {$conditionQuery}";
        $tmpJumlahTindakan = Yii::$app->db->createCommand($sqlJumlahTindakan)
            ->bindValue(':dateStart', $start)
            ->bindValue(':dateEnd', $end)
            ->queryOne();

        return [
            'jumlah_jeniskegiatan' => $tmpJenisKegiatan['hasil_jenis'] ?: 0,
            'jumlah_tindakan' => $tmpDaftarTindakan['hasil_tindakan'] ?: 0,
            'jumlah_hasil' => $tmpJumlahTindakan['hasil_jumlah_tindakan'] ?: 0
        ];
    }

    public function run()
    {
        try {
            $request = Yii::$app->request;
            $advancedFilters = $request->get('advanced-filter');
            $query = self::getQueryDataFromReq($advancedFilters);
            return new ActiveDataProvider(['query' => $query]);
        } catch (\yii\db\Exception $e) {
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

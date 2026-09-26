<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\ObatAlkes;

class PRRecommendationAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');

        // Next improvement
        // Integration with settings to set month, day of month, day of week and slack
        // 'slack' => jumlah hari keterlambatan pengiriman
        $months = 3;
        $day_of_month = 30;
        $day_of_week = 7;
        $slack = 3;
        $date = date('Y-m-d 00:00:00');
        $start_date = date('Y-m-d 00:00:00', strtotime($date . "-90 days"));
        $end_date = date('Y-m-d 00:00:00');

        $calc = "round(
            (
                (
                    sum(
                        case when obatalkespasien_t.det_konversi is null then
                            coalesce(obatalkespasien_t.qty_konversi, 0)
                        else
                            coalesce(obatalkespasien_t.det_konversi, 0)
                        end
                    ) + 
                    coalesce(tablemutasi.jumlah_mutasi, 0)
                ) / (" . $months . " * " . $day_of_month . ")
            ) * (
                " . $day_of_week . " + " . $slack . " + coalesce(obatalkes_m.lead_time, 0)
            )
        ) - ceil(coalesce(stokobatalkes_r.qty_sisa, 0))";

        $tablemutasi = "(
            select 
                mutasiobatdetail_t.obatalkes_id, 
                ceil(
                    sum(coalesce(mutasiobatdetail_t.jumlah_mutasi, 0))
                ) as jumlah_mutasi 
            from mutasiobatdetail_t
            join mutasiobatruangan_t 
                on mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id 
            join ruangan_m 
                on ruangan_m.ruangan_id = mutasiobatruangan_t.ruangantujuan_id 
            where mutasiobatruangan_t.tglmutasioa between symmetric current_date::timestamp and (current_date - interval '3 months')::timestamp
            and ruangan_m.instalasi_id != '" . DocoConstants::INST_ID_APT . "'
            and mutasiobatruangan_t.status_mutasi = '" . DocoConstants::STATUS_MUTASI_DITERIMA . "'
            group by mutasiobatdetail_t.obatalkes_id
        ) tablemutasi";

        $query = ObatAlkes::find()
            ->select([
                'obatalkes_m.obatalkes_id',
                'obatalkes_m.obatalkes_nama',
                'obatalkes_m.obatalkes_kode',
                'obatalkes_m.satuankecil_id',
                'satuanunit_m.satuanunit_nama',
                'stokobatalkes_r.qty_sisa',
                $calc . ' as qty_rekomendasi'
            ])
            ->join(
                'JOIN', 
                'obatalkespasien_t', 
                'obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id'
            )
            ->join(
                'JOIN',
                'penjualanresep_t',
                'penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id'
            )
            ->join(
                'JOIN',
                'satuanunit_m',
                'satuanunit_m.satuanunit_id = obatalkes_m.satuankecil_id'
            )
            ->join(
                'JOIN',
                'stokobatalkes_r',
                'stokobatalkes_r.obatalkes_id = obatalkes_m.obatalkes_id'
            )
            ->leftJoin(
                $tablemutasi,
                'tablemutasi.obatalkes_id = obatalkes_m.obatalkes_id'
            )

            // >>> uncomment this line if min qty base on kontraksupplier <<<
            // ->join(
            //     'JOIN',
            //     'kontraksupplier_m',
            //     'kontraksupplier_m.supplier_id = obatalkes_m.supplier_id'
            // )
            // ->join(
            //     'JOIN',
            //     'kontraksupplierdetail_m',
            //     'kontraksupplierdetail_m.kontraksupplier_id = kontraksupplier_m.kontraksupplier_id'
            // )
            // ->andHaving(['>', $calc, 'kontraksupplierdetail_m.qty_min'])
            // >>> uncomment this line if min qty base on kontraksupplier <<<

            ->andWhere(['between', 'penjualanresep_t.tglpenjualan', $start_date, $end_date])
            ->andWhere(['penjualanresep_t.status_reseptur' => DocoConstants::RESEPTUR_DISERAHKAN])
            ->andWhere(['stokobatalkes_r.ruangan_id' => $ruangan_id])
            ->groupBy([
                'obatalkes_m.obatalkes_id', 
                'satuanunit_m.satuanunit_nama',
                'stokobatalkes_r.qty_sisa',
                'tablemutasi.jumlah_mutasi'
            ])
            ->andHaving(['>', $calc, 0])
            ->orderBy(['obatalkes_m.obatalkes_id' => SORT_ASC]);

        $data = $query->asArray()->all();

        return $data;
    }
}

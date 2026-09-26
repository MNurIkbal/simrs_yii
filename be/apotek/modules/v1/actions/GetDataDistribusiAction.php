<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoDistribusiObatAlkesView;

class GetDataDistribusiAction extends Action
{
	public $withUnverified = FALSE;

    public function run()
    {
        $model = new InfoDistribusiObatAlkesView;
        $query = (new Query())
                ->select([
                    "infodistribusiobatalkes_v.*",
                    "(SELECT 
                    CASE konfigfarmasi_k.is_verifpemesanan 
                    WHEN TRUE THEN 
                     CASE 
                     WHEN infodistribusiobatalkes_v.statuspesan = '398' AND infodistribusiobatalkes_v.status_verifikasi = 666
                     THEN infodistribusiobatalkes_v.status_verifikasi_nama
                     ELSE infodistribusiobatalkes_v.status_distribusi
                     END
                    ELSE infodistribusiobatalkes_v.status_distribusi
                    END
                    from konfigfarmasi_k LIMIT 1) AS statusdistribusiobat",
                    "(SELECT 
                    CASE konfigfarmasi_k.is_verifpemesanan 
                    WHEN TRUE THEN 
                     CASE 
                     WHEN infodistribusiobatalkes_v.statuspesan = '398' AND infodistribusiobatalkes_v.status_verifikasi = 666
                     THEN infodistribusiobatalkes_v.status_verifikasi
                     ELSE infodistribusiobatalkes_v.status_id
                     END
                    ELSE infodistribusiobatalkes_v.status_id
                    END
                    from konfigfarmasi_k LIMIT 1) AS statusdistribusiobat_id"
                ])
                ->from('infodistribusiobatalkes_v');
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tglpemesanan'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglpemesanan']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglpemesanan']);
                $between = true;
            }

            if(isset($_GET['advanced-filter']['instalasi_ruangan'])){
                $query->andWhere(['ruangan_id'=>$_GET['advanced-filter']['instalasi_ruangan']]);
                unset($_GET['advanced-filter']['instalasi_ruangan']);
            }

            if(isset($_GET['advanced-filter']['nopemesanan'])){
                $query->andWhere(['nopemesanan'=>$_GET['advanced-filter']['nopemesanan']]);
                unset($_GET['advanced-filter']['nopemesanan']);
            }
            
            if(isset($_GET['advanced-filter']['statusdistribusiobat'])){
                $query->having(['ILIKE',"(SELECT 
                    CASE konfigfarmasi_k.is_verifpemesanan 
                    WHEN TRUE THEN 
                     CASE 
                     WHEN infodistribusiobatalkes_v.statuspesan = '398' AND infodistribusiobatalkes_v.status_verifikasi = 666
                     THEN infodistribusiobatalkes_v.status_verifikasi_nama
                     ELSE infodistribusiobatalkes_v.status_distribusi
                     END
                    ELSE infodistribusiobatalkes_v.status_distribusi
                    END
                    from konfigfarmasi_k LIMIT 1)",$_GET['advanced-filter']['statusdistribusiobat']]);
                unset($_GET['advanced-filter']['statusdistribusiobat']);
            }
            
            if(isset($_GET['advanced-filter']['reference'])){
                $query->andWhere(['ILIKE','reference',$_GET['advanced-filter']['reference']]);
                unset($_GET['advanced-filter']['reference']);
            }
        }

        $query->andWhere(['between', 'tglpemesanan', $start, $end]);
        $query->groupBy([
            'pesanobatalkes_id',
            'tglpemesanan',
            'ruangan_id',
            'ruangan_tujuan',
            'instalasi_id',
            'instalasi_tujuan',
            'nopemesanan',
            'ruanganpemesan_id',
            'ruangan_pemesan_id',
            'ruangan_pemesan',
            'instalasi_pemesan_id',
            'instalasi_pemesan',
            'mutasiobatruangan_id',
            'statuspesan',
            'status_pengiriman',
            'tglmintadikirim',
            'keterangan_pesan',
            'nomutasioa',
            'tglmutasioa',
            'status_mutasi',
            'status_penerimaan',
            'noterimamutasi',
            'tglterima',
            'instalasi_ruangan',
            'status_distribusi',
            'reference',
            'status_id',
            'status_verifikasi',
            'status_verifikasi_nama',
            'pemesan',
            'pengirim',
            'penerima'
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        if($this->withUnverified){
        	$query->andWhere(['<>','status_verifikasi',666]);
        }
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
}
<?php
namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfoStokObatAlkesAllFn;

class ListObatPelayananAction extends Action
{
	public function run()
	{
		$request = Yii::$app->request;
        $result = [];
        try {
            $keyword = $request->get('keyword');
            $page = $request->get('page');
            $kelaspelayanan_id = $request->get('kelaspelayanan_id',0);
            $perpage = 10;
            $limit = $request->get('limit',11);
            $withoutLimit = $request->get('withoutLimit','false');
            $isOnlyAvailable = $request->get('isOnlyAvailable',FALSE);
            $result = $this->getData(
                /*Penjamin*/ $request->get('penjamin_id'),
                /*Kelas Pelayanan*/ $kelaspelayanan_id,
                /*Group Jenis Obat*/ $request->get('group_jenisobat'),
                /*Jenis Obat Alkes*/ $request->get('jenisobatalkes_id'),
                /*Keyword*/ $keyword,
                /*Page*/ $page,
                /*Perpage*/ $perpage,
                /*Limit*/ $limit,
                /*Without Limit*/ $withoutLimit,
                /*Only Available Stock*/ $isOnlyAvailable
            );
            return [
                'data' => $result,
                'payload' => $request->get(),
                'totalResult' => count($result)
            ];

        } catch (\yii\db\Exception $e) {
            return [
                'data' => [],
                'payload' => $request->get(),
                'totalResult' => 0,
                'errorMessage' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return $result;
        }
	}

	protected function getData(
        $penjaminId_,
        $kelaspelayananId_,
        $groupJenisObat_ = null,
        $jenisObatAlkesId_ = null,
        $keyword_ = null,
        $page_ = 0,
        $perpage_ = 10,
        $limit_ = 11,
        $withoutlimit_ = false,
        $isOnlyAvailable_ = false
    )
    {
        $cacheDuration = 60 * 3; // insecond
        $result = (new InfoStokObatAlkesAllFn(['extParam'=>[$penjaminId_,$kelaspelayananId_]]))->getDb()->cache(function ($db) use(
                $penjaminId_,
                $kelaspelayananId_,
                $groupJenisObat_,
                $jenisObatAlkesId_,
                $keyword_,
                $page_,
                $perpage_,
                $limit_,
                $withoutlimit_,
                $isOnlyAvailable_
            ) {

            $query = (new InfoStokObatAlkesAllFn(['extParam'=>[$penjaminId_,$kelaspelayananId_]]))->find()
            ->select([
                    'obatalkes_id',
                    'obatalkes_kode',
                    'obatalkes_namalain',
                    'obatalkes_nama',
                    'qty_tersedia',
                    'ppn',
                    'hargaygdipakai as hargajual',
                    'satuankecil_id',
                    'satuankecil_nama',
                    'satuansedang_id',
                    'satuansedang_nama',
                    'satuanbesar_id',
                    'satuanbesar_nama',
                    'harganetto_ygdipakai as harganetto',
                    'hargaygdipakai',
                    'hn_diskon',
                    'hn_ppn',
                    'hn_margin',
                    'disc',
                    'ppn',
                    'margin',
                    'group_jenisobat',
                    'group_jenisobat_nama',
                    'jenisobatalkes_id',
                    'jenisobatalkes_nama']);
            if(!empty($keyword_)){
                $query->andWhere(['like', 'LOWER(obatalkes_nama)', strtolower($keyword_) ]);
            }
            if(!empty($groupJenisObat_)){
                $query->andWhere(['group_jenisobat' => $groupJenisObat_]);
            }
            if(!empty($jenisObatAlkesId_)){
                $query->andWhere(['jenisobatalkes_id' => $jenisObatAlkesId_]);
            }
            if($isOnlyAvailable_ === TRUE || strtolower($isOnlyAvailable_) == 'true' || strtolower($isOnlyAvailable_) == 't' || $isOnlyAvailable_ == 1){
                $query->andWhere(['>','qty_tersedia','0']);
            }
            if($withoutlimit_ == FALSE || strtolower($withoutlimit_) == 'false' || strtolower($withoutlimit_) == 'f'){
                $query->offset(($page_-1)*$perpage_)->limit($limit_);
            }
            $result = $query->all();
            return $result;
        },$cacheDuration);

        return $result;
    }
}
<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use app\modules\v1\models\DetailMutasiObatAlkesView;

class MutasiObatDetail
{
	public static function getListByNoMutasi($nomutasi)
	{
		return DetailMutasiObatAlkesView::find(true)->where(['nomutasioa' => $nomutasi])->orderBy(['obatalkes_nama'=>SORT_ASC])->asArray()->all();
	}
	public static function getListByIdMutasi($mutasiobatruangan_id)
	{
		return DetailMutasiObatAlkesView::find(true)->where(['mutasiobatruangan_id' => $mutasiobatruangan_id])->orderBy(['obatalkes_nama'=>SORT_ASC])->asArray()->all();
	}
	public static function getListById($id)
	{
		return DetailMutasiObatAlkesView::find(true)->where([
                'mutasiobatruangan_id' => $id
            ])->orderBy(['obatalkes_nama'=>SORT_ASC])->asArray()->all();
	}
}
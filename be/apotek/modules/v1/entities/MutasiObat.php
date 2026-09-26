<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\InfoMutasiObatalkesView;
use app\modules\v1\models\MutasiObatRuangan;
use app\modules\v1\models\MutasiObatDetail;

class MutasiObat
{
	protected $_mutasiObat;
	protected $_mutasiDetail;

	public static function getInfoByNoMutasi($nomutasi)
	{
		return InfoMutasiObatalkesView::find()->where(['nomutasioa' => $nomutasi])->one();
	}
	
	public static function getInfoByIdMutasi($mutasiobatruangan_id)
	{
		return InfoMutasiObatalkesView::find()->where(['mutasiobatruangan_id' => $mutasiobatruangan_id])->one();
	}

	public static function getByNoMutasi($nomutasi)
	{
		return MutasiObatRuangan::find()->where(['nomutasioa'=>$nomutasi])->one();
	}

	public function load($inputMutasi,$inputMutasiDetail)
	{
		$mutasiObat = new MutasiObatRuangan;
    	$mutasiObat->attributes = $inputMutasi;
    	if(!$mutasiObat->validate()) throw new \Exception("Error Processing Request", 1);
    	$this->_mutasiObat = $mutasiObat->attributes;

    	if(!is_array($inputMutasiDetail)) throw new \Exception("Data Detail Harus Berupa Array", 1);
    	foreach ($inputMutasiDetail as $_kinput => $_vdetail) {
            foreach ($_vdetail as $_atrk => $_atrv){
                if($_atrv == "null") $inputMutasiDetail[$_kinput][$_atrk] = null;
            }
        }
        foreach ($inputMutasiDetail as $k => $v) {
            $inputMutasiDetail[$k]['mutasiobatruangan_id'] = 0;
        }
        foreach($inputMutasiDetail as $_mutasiDetail){
        	$mutasiObatDetail = new MutasiObatDetail;
        	$mutasiObatDetail->attributes = $_mutasiDetail;
        	if(!$mutasiObatDetail->validate())
                throw new \Exception(json_encode($mutasiObatDetail->errors), 1);
        }
    	$this->_mutasiDetail = $inputMutasiDetail;
    	return $this;
	}

	public function loadMutasiLangsung($inputMutasi,$inputMutasiDetail)
	{
		$mutasiObat = new MutasiObatRuangan;
		$mutasiObat->tglmutasioa = date('Y-m-d H:i:s');
		$mutasiObat->keteranganmutasi = $inputMutasi['keterangan'];
		$mutasiObat->ruanganasal_id = $inputMutasi['ruanganasal_id'];
		$mutasiObat->ruangantujuan_id = $inputMutasi['ruangantujuan_id'];
		$mutasiObat->pegawaimengetahui_id = !empty($inputMutasi['pegawaimengetahui_id']) ? $inputMutasi['pegawaimengetahui_id'] : null;
		$mutasiObat->pegawaimenyetujui_id = !empty($inputMutasi['pegawaimenyetujui_id']) ? $inputMutasi['pegawaimenyetujui_id'] : null;

		$this->_mutasiObat = $mutasiObat->attributes;

		if(!is_array($inputMutasiDetail)) throw new \Exception("Data Detail Harus Berupa Array", 1);

        $mutasiDetail = [];
        foreach($inputMutasiDetail as $_mutasiDetail){
        	$mutasiObatDetail = new MutasiObatDetail;
        	$mutasiObatDetail->mutasiobatruangan_id = 0;
        	$mutasiObatDetail->obatalkes_id = $_mutasiDetail['obatalkes_id'];
        	$mutasiObatDetail->satuankecil_id = $_mutasiDetail['satuankecil_id'];
        	$mutasiObatDetail->satuanbesar_id = $_mutasiDetail['satuanbesar_id'];
        	$mutasiObatDetail->jumlah_pesan = $_mutasiDetail['qty_pesan'];
        	$mutasiObatDetail->jumlah_mutasi = $_mutasiDetail['qty_kecil'];
        	$mutasiObatDetail->jumlah_input = $_mutasiDetail['qty_besar'];
        	if(!$mutasiObatDetail->validate())
                throw new \Exception(json_encode($mutasiObatDetail->errors), 1);
            $mutasiDetail[] = $mutasiObatDetail->attributes;
        }
    	$this->_mutasiDetail = $mutasiDetail;
		return $this;
	}

	public function save()
	{
		$listObat = array_column($this->_mutasiDetail, 'obatalkes_id');

		$getObat = InfoStokObatAlkesView::find()
					->where(['ruangan_id'=>$this->_mutasiObat['ruanganasal_id']])
					->andWhere(['IN','obatalkes_id',$listObat])->asArray()->all();

		$infoObat = [];
		foreach ($getObat as $_getObat) {
			$infoObat[$_getObat['obatalkes_id']] = $_getObat;
		}

		$mutasiObat = new MutasiObatRuangan;
		$mutasiObat->attributes = $this->_mutasiObat;
		$mutasiObat->tglmutasioa = date('Y-m-d H:i:s');
		$mutasiObat->status_mutasi = 401;
		if(empty($mutasiObat->pegawaimengetahui_id)) $mutasiObat->pegawaimengetahui_id = Yii::$app->jwt->user->pegawai_id;;
		if(empty($mutasiObat->pegawaimenyetujui_id)) $mutasiObat->pegawaimenyetujui_id = Yii::$app->jwt->user->pegawai_id;;
		if(!$mutasiObat->save()) throw new \Exception("Error Processing Request", 1);
		
		$dataInsert=[];
		$toth_netto = 0;
		$toth_jual = 0;
		foreach ($this->_mutasiDetail as $key => $value) {
			$toth_netto += $infoObat[$value['obatalkes_id']]['harganetto_ygdipakai'] * $value['jumlah_mutasi'];
			$toth_jual += $infoObat[$value['obatalkes_id']]['hargaygdipakai'] * $value['jumlah_mutasi'];
			$dataInsert[] = [
	            'mutasiobatruangan_id' => $mutasiObat->getPrimaryKey(),
	            'obatalkes_id' => $value['obatalkes_id'],
	            'satuankecil_id' => $value['satuankecil_id'],
	            'jumlah_mutasi' => $value['jumlah_mutasi'],
	            'satuanbesar_id' => $value['satuanbesar_id'],
	            'jumlah_input' => $value['jumlah_input'],
	            'jumlah_pesan' => $value['jumlah_mutasi'],
	            'harga_netto' => $infoObat[$value['obatalkes_id']]['harganetto_ygdipakai'],
	            'harga_jualsatuan' => $infoObat[$value['obatalkes_id']]['hargaygdipakai'],
	            'persen_discount' => $infoObat[$value['obatalkes_id']]['disc'],
	            'total_harga' => ($infoObat[$value['obatalkes_id']]['hargaygdipakai'] - $infoObat[$value['obatalkes_id']]['hn_diskon']) * $value['jumlah_mutasi'],
	            'is_active' => true,
	        ];
	    }
	    MutasiObatDetail::batchInsert($dataInsert);

		$mutasiObat->totalharganettomutasi = $toth_netto;
		$mutasiObat->totalhargajual = $toth_jual;
		if(!$mutasiObat->save()) throw new \Exception("Error Processing Request", 1);
	}

	public function saveLangsung()
	{
		$listObat = array_column($this->_mutasiDetail, 'obatalkes_id');

		$getObat = InfoStokObatAlkesView::find()
					->where(['ruangan_id'=>$this->_mutasiObat['ruanganasal_id']])
					->andWhere(['IN','obatalkes_id',$listObat])->asArray()->all();

		$infoObat = [];
		foreach ($getObat as $_getObat) {
			$infoObat[$_getObat['obatalkes_id']] = $_getObat;
		}

		$mutasiObat = new MutasiObatRuangan;
		$mutasiObat->attributes = $this->_mutasiObat;
		$mutasiObat->status_mutasi = 401;
		if(empty($mutasiObat->pegawaimengetahui_id)) $mutasiObat->pegawaimengetahui_id = Yii::$app->jwt->user->pegawai_id;;
		if(empty($mutasiObat->pegawaimenyetujui_id)) $mutasiObat->pegawaimenyetujui_id = Yii::$app->jwt->user->pegawai_id;;
		if(!$mutasiObat->save()) throw new \Exception("Gagal Simpan Mutasi", 1);
		
		$dataInsert=[];
		$toth_netto = 0;
		$toth_jual = 0;
		foreach ($this->_mutasiDetail as $key => $value) {
			$toth_netto += $infoObat[$value['obatalkes_id']]['harganetto_ygdipakai'] * $value['jumlah_mutasi'];
			$toth_jual += $infoObat[$value['obatalkes_id']]['hargaygdipakai'] * $value['jumlah_mutasi'];
			$h_netto = !is_null($infoObat[$value['obatalkes_id']]['harganetto_ygdipakai']) ? $infoObat[$value['obatalkes_id']]['harganetto_ygdipakai'] : 0;
			$h_jual = !empty($infoObat[$value['obatalkes_id']]['hargaygdipakai']) ? $infoObat[$value['obatalkes_id']]['hargaygdipakai'] : 0;
			$hn_diskon = !empty($infoObat[$value['obatalkes_id']]['hn_diskon']) ? $infoObat[$value['obatalkes_id']]['hn_diskon'] : 0;
			$dataInsert[] = [
	            'mutasiobatruangan_id' => $mutasiObat->getPrimaryKey(),
	            'obatalkes_id' => $value['obatalkes_id'],
	            'satuankecil_id' => $value['satuankecil_id'],
	            'jumlah_mutasi' => $value['jumlah_mutasi'],
	            'satuanbesar_id' => $value['satuanbesar_id'],
	            'jumlah_input' => $value['jumlah_input'],
	            'jumlah_pesan' => $value['jumlah_mutasi'],
	            'harga_netto' => $h_netto,
	            'harga_jualsatuan' => $h_jual,
	            'persen_discount' => $infoObat[$value['obatalkes_id']]['disc'],
	            'total_harga' => ($h_jual - $hn_diskon) * $value['jumlah_mutasi'],
	            'is_active' => true,
	        ];
	    }
	    MutasiObatDetail::batchInsert($dataInsert);

		$mutasiObat->totalharganettomutasi = $toth_netto;
		$mutasiObat->totalhargajual = $toth_jual;
		if(!$mutasiObat->save()) throw new \Exception("Gagal Simpan Total Harga", 1);

		return ['id'=>$mutasiObat->getPrimaryKey(),'nomor'=>@MutasiObatRuangan::findOne($mutasiObat->getPrimaryKey())->nomutasioa];
	}
}
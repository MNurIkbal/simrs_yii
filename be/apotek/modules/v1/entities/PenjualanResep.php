<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use Yii;
use Doco\components\DocoConstants;
use app\modules\v1\models\PenjualanResep as PenjualanResepModel;
use app\modules\v1\models\InformasiResepturView;
use app\modules\v1\models\ResepturDetail as ResepturDetailModel;

class PenjualanResep
{
	protected $_reseptur;
	protected $_penjualanResep;
	protected $_inforesep;
	protected $_totalharganetto = 0;
	protected $_totalhargajual = 0;

	public function __get($name)
	{
		if (array_key_exists($name, $this->_penjualanResep)) {
            return $this->_penjualanResep[$name];
        }

        return null;
	}

	public function loadByResepturData($reseptur, $pendaftaran = [])
	{
		$penjualanResep = new PenjualanResepModel;
		$penjualanResep->attributes = $reseptur->attributes;
		$penjualanResep->kelaspelayanan_id = isset($pendaftaran['kelaspelayanan_id']) ? $pendaftaran['kelaspelayanan_id'] : null;
		$penjualanResep->penjamin_id = isset($pendaftaran['penjamin_id']) ? $pendaftaran['penjamin_id'] : null;
		$penjualanResep->carabayar_id = isset($pendaftaran['carabayar_id']) ? $pendaftaran['carabayar_id'] : null;
		$penjualanResep->created_date = null;
		$penjualanResep->modified_count = null;
		$this->_penjualanResep = $penjualanResep->attributes;

		$this->getTotalHarga($reseptur->details);
		return $this;
	}

	protected function getTotalHarga($reseptur_detail)
	{
		$totalhargajual = $totalharganetto = 0;
		foreach ($reseptur_detail as $detail) {
			$totalhargajual += $detail['hargasatuan_reseptur'] * $detail['qty_reseptur'];
			$totalharganetto += $detail['harganetto_reseptur'] * $detail['qty_reseptur'];
		}
		$this->_totalharganetto = $totalharganetto;
		$this->_totalhargajual = $totalhargajual;
	}

	public function loadByReseptur($reseptur_id)
	{
		$resep = InformasiResepturView::find()->where(['reseptur_id'=>$reseptur_id])->asArray()->one();
		$this->_inforesep = $resep;

		$penjualanResep = new PenjualanResepModel;
		$penjualanResep->attributes = $resep;
		$penjualanResep->status_reseptur = $resep['status_reseptur_id'];
		$this->_penjualanResep = $penjualanResep->attributes;

		$detail = ResepturDetailModel::find()->where(['reseptur_id'=>$reseptur_id])->asArray()->all();

		$totalhargajual = $totalharganetto = 0;
		foreach ($detail as $_detail) {
			$totalhargajual += $_detail['hargasatuan_reseptur'] * $_detail['qty_reseptur'];
			$totalharganetto += $_detail['harganetto_reseptur'] * $_detail['qty_reseptur'];
		}
		$this->_totalharganetto = $totalharganetto;
		$this->_totalhargajual = $totalhargajual;
		return $this;
	}

	public function getInfoResep()
	{
		return $this->_inforesep;
	}

	public function save()
	{
		$now = date('Y-m-d H:i:s');
		$model = new PenjualanResepModel;
		$model->attributes = $this->_penjualanResep;
        // $model->pegawai_id = Yii::$app->user->identity->pegawai_id;
        $model->totharganetto = $this->_totalharganetto;
        $model->totalhargajual = $this->_totalhargajual;
        $model->tglpenjualan = $now;
        $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_RS;
        $model->tglresep = $now;
        $model->created_by = Yii::$app->user->identity->pegawai_id;
        // $model->catatan = $keterangan;
		if(!$model->save()) throw new \Exception("Error Processing Request2", 1);

		$this->_penjualanResep = $model->attributes;
	}

	public function saveApprove($inputHeader)
	{
		$pegawai_login = Yii::$app->user->identity->pegawai_id;
		$now = date('Y-m-d H:i:s');
		$model = new PenjualanResepModel;
		$attribut = array_merge($this->_penjualanResep,$inputHeader);
		$model->attributes = $attribut;
        $model->pegawai_id = $pegawai_login;
        $model->totharganetto = $inputHeader['totharganetto'];
        $model->totalhargajual = $inputHeader['totalhargajual'];
        $model->tglpenjualan = $now;
        $model->jenispenjualan = DocoConstants::PENJUALAN_RESEP_RS;
        $model->tglresep = $now;
        $model->created_by = $pegawai_login;
		$model->pegawai_approve_id = $pegawai_login;
		$model->tgl_approve = $now;
        // $model->catatan = $keterangan;
		if(!$model->save()) throw new \Exception("Error Processing Request2", 1);

		$this->_penjualanResep = $model->attributes;
	}

	public static function findOne($id)
	{
		return PenjualanResepModel::findOne($id);
	}
	
	public static function findReseptur($id)
	{
		return PenjualanResepModel::find($id)->all();
	}

    public function updateTagihan($reseptur_id, $inputHeader) {
        $modelPenjualan = PenjualanResepModel::find()->where(['reseptur_id' => $reseptur_id])->one();
        $modelPenjualan->attributes = $inputHeader;
        if(!$modelPenjualan->save()) throw new \Exception("Error Processing Request Penjualan Resep", 1);
        return true;
    }
}
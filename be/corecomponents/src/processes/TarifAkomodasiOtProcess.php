<?php 
/**
 * @author : Budi
 * Powered by Sirs
 */

namespace Doco\processes;
use Yii;
use Doco\exceptions\ValidationException;
use SirsCore\models\InfoPasienOperasiView;
use Doco\models\TarifTotalKamarFn;

class TarifAkomodasiOtProcess extends \Doco\components\DocoBaseProcessExtension
{
   protected function getTarifDefault()
	{
		$data = $this->_requestData->get('data');
      	$ruangan_id = isset($data['ruangan_id']) ? $data['ruangan_id'] : null;
      	$penjamin_id = isset($data['penjamin_id']) ? $data['penjamin_id'] : null;
      	$kelaspelayanan_id = isset($data['kelaspelayanan_id']) ? $data['kelaspelayanan_id'] : null;
      	$kamarruangan_id = isset($data['kamarruangan_id']) ? $data['kamarruangan_id'] : null;
		return (new TarifTotalKamarFn([
			'extParam' => [
				$ruangan_id,
				$penjamin_id,
				$kelaspelayanan_id,
				'kamar'
			]
		]))
		->find()
		->select([
			'daftartindakan_id',
			'daftartindakan_nama',
			'kamarruangan_nokamar',
			'harga_tariftindakan',
			'persencyto_tindakan',
			'persen_penyulit'
		])
		->where(['kamarruangan_id' => $kamarruangan_id])
		->asArray()->one();
	}

	protected function processFlow() 
	{
      return $this->getTarifDefault();
	}
}

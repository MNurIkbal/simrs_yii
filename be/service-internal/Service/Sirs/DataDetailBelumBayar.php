<?php 

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;

use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;
use Integrasi\Components\DocoRestActiveFilter;
use Integrasi\Components\DocoConstants;
use Integrasi\Service\Sirs\BussinesLogic\TagihanHelper;

class DataDetailBelumBayar extends \Integrasi\Contracts\DocoImplement
{
    
    public function execute()
    {
        $cacheFiles = Yii::$app->cacheFiles;
		
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Menyiapkan data.',
				 'progress' => 10
			 ]),
        ]);
		
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Menyiapkan data.',
				 'progress' => 30
			 ]),
        ]);
        $attributes = $this->getDataAttibutes($this->filter);

		foreach ($attributes as $key => $value) {
			$tindakanObatNama = !empty($value['tindakan_obat_nama']) ? $value['tindakan_obat_nama'] : '-';
			$isObat = !empty($value['is_obat']) ? $value['is_obat'] : false;
			$subTotal = !empty($value['sub_total']) ? $value['sub_total'] : '0';
			$groupCaraBayarId = !empty($value['groupcarabayar_id']) ? $value['groupcarabayar_id'] : null;
			$attributes[$key]['no'] = $key +1;
			$attributes[$key]['tgl_masuk'] = !empty($value['tgl_pendaftaran']) ? $value['tgl_pendaftaran'] : '-';
			$attributes[$key]['tgl_keluar'] = !empty($value['tglpasienpulang']) ? $value['tglpasienpulang'] : '-';
			$attributes[$key]['instalasi_nama'] = !empty($value['instalasi_pelayanan']) ? $value['instalasi_pelayanan'] : '-';
			$attributes[$key]['ruangan_akhir'] = !empty($value['ruangan_pelayanan']) ? $value['ruangan_pelayanan'] : '-';
			$attributes[$key]['no_pendaftaran'] = !empty($value['no_pendaftaran']) ? $value['no_pendaftaran'] : '-';
			$attributes[$key]['nama_pasien'] = !empty($value['nama_pasien']) ? $value['nama_pasien'] : '-';
			$attributes[$key]['no_rekam_medik'] = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '-';
			$attributes[$key]['dokterpenanggungjawab_nama'] = !empty($value['dokterpenanggungjawab_nama']) ? $value['dokterpenanggungjawab_nama'] : '-';
			$attributes[$key]['daftartindakan_nama'] = $isObat ? '-' : $tindakanObatNama;
			$attributes[$key]['obatalkes_nama'] = $isObat ? $tindakanObatNama : '-';
			$attributes[$key]['tarif_satuan'] = !empty($value['tarif_satuan']) ? $value['tarif_satuan'] : '0';
			$attributes[$key]['cara_bayar'] = !empty($value['carabayar_pelayanan']) ? $value['carabayar_pelayanan'] : '-';
			$attributes[$key]['penjamin_nama'] = !empty($value['penjamin_pelayanan']) ? $value['penjamin_pelayanan'] : '-';
			$attributes[$key]['subtotal'] = $subTotal;
			$attributes[$key]['diskon'] = 0;
			$attributes[$key]['dijamin'] = $groupCaraBayarId != DocoConstants::GROUP_UMUM ? $subTotal : 0;
			$attributes[$key]['ditagihkan'] = $groupCaraBayarId == DocoConstants::GROUP_UMUM ? $subTotal : 0;
		}

        $cacheFiles->set($this->unique_str, $attributes);
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'.$this->unique_str,
			'message' => json_encode([
				 'status' => 'finish', 
				 'messageProcess' => 'Berhasil menyiapkan data.',
				 'progress' => 70
			 ]),
        ]);
        return json_encode([
            'service' => 'Sirs-DataExportExcel',
            'payload' => $attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function getDataAttibutes($params)
    {
		
        $params = !empty($params['params']) ? $params['params'] : [];
        $pendaftaranId = !empty($params['pendaftaran_id']) ? $params['pendaftaran_id'] : null;
        
        try {
			$data = [];
			if(!empty($pendaftaranId)){
				$data = Yii::$app->db->createCommand("
								SELECT 
									if.tgl_pendaftaran,
									if.instalasi_pelayanan,
									if.ruangan_pelayanan,
									if.no_pendaftaran,
									if.nama_pasien,
									if.no_rekam_medik,
									if.dokterpenanggungjawab_nama,
									if.tindakan_obat_nama,
									if.is_obat,
									if.tarif_satuan,
									if.carabayar_pelayanan,
									if.sub_total,
									pd.tgl_stopakomodasi,
									CASE
									 WHEN pm.penjamin_nama IS NOT NULL
										THEN 
											concat(if.penjamin_pelayanan, ', ', pm.penjamin_nama)
										ELSE
											if.penjamin_pelayanan
									END penjamin_pelayanan,
									COALESCE(pd.tgl_stopakomodasi, pulang_ri.tglpasienpulang, pulang_rj.tglpasienpulang) AS tglpasienpulang
								FROM infotagihanpasien_v if
								LEFT JOIN ( SELECT a.pendaftaran_id, a.tgl_stopakomodasi, a.pasienadmisi_id, a.pasienpulang_id
									FROM pendaftaran_t a) pd ON pd.pendaftaran_id = if.pendaftaran_id
								LEFT JOIN ( SELECT a.pasienpulang_id, a.pasienadmisi_id
									FROM pasienadmisi_t a) pa ON pa.pasienadmisi_id = pd.pasienadmisi_id
								LEFT JOIN ( SELECT a.pasienpulang_id,a.tglpasienpulang
									FROM pasienpulang_t a) pulang_ri ON pa.pasienpulang_id = pulang_ri.pasienpulang_id
								LEFT JOIN ( SELECT a.pasienpulang_id,a.tglpasienpulang
								   FROM pasienpulang_t a) pulang_rj ON pd.pasienpulang_id = pulang_rj.pasienpulang_id
								LEFT JOIN (SELECT a.penjamin_id, a.pendaftaran_id, b.penjamin_nama
										FROM pendaftaran_multipayer_t a
										LEFT JOIN penjamin_m b on b.penjamin_id = a.penjamin_id) pm ON pm.pendaftaran_id = pd.pendaftaran_id
								WHERE if.pendaftaran_id = {$pendaftaranId}")
							->queryAll();
			}
			return $data;
		} catch (\GuzzleHttp\Exception\RequestException $e) {
			if($e->hasResponse()) {
				$response = $e->getResponse();
				return $response->getBody();
			}
		}
    }

    private function setUrl()
	{
		$header = [
			'Authorization' => $this->token,
			'user-agent' => 'cli',
			'X-Owner' => $this->xOwner,
		];
		$client =  new Client([
			'base_uri' => isset($this->params['base_uri']) ? $this->params['base_uri'] : '',
			'headers' => $header
		]);

		return $client;
	}
}
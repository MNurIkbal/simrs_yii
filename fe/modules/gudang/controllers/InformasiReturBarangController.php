<?php
    namespace Doco\gudang\controllers;
    /**
    * @author anggoro -- tri.anggoro@docotel.com
    */

    use Yii;
    use yii\web\Response;
    use yii\web\UploadedFile;

    use app\components\DocoController;
    use app\components\DocoHelpers;
    use app\components\DocoDatatableHelper;
    use app\components\DocoConstants;

    class InformasiReturBarangController extends DocoController
    {
        public $_title = "Informasi Retur Barang Supplier";
        protected $_module = '/gudang/informasi-retur-barang/';
        protected $_restGudang;

        public function init()
        {
            parent::init();
            $this->_restGudang = Yii::$app->docoRest->gudang;
        }

        public function actionIndex()
        {
            $title = $this->_title;
            $module = $this->_module;

            return $this->render('index', get_defined_vars());
        }

        public function actionGetData()
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            $draw = $request->get('draw',1);
            $data = [];
            try {
                $response = $this->_restGudang->get('informasi-retur-barang/', [
                    'query' => $filter
                ]);
                $body = json_decode($response->getBody(), true);
                foreach ($body["response"]["data"] as $index => $row)
                {
                    $primaryKey = DocoHelpers::encrypt($row['returpenerimaanbarang_id']);
                    $row['primary'] = $primaryKey;
                    $row["tgl_retur"] = date("d-M-Y", strtotime($row["tgl_retur"]));
                    $row["no_retur"] = $row["no_returpenerimaanbarang"];
                    $row["no_penerimaan"] = $row["no_penerimaan"];
                    $row["no_faktur"] = $row["no_faktur"];
                    $row["supplier_nama"] = $row["supplier_nama"];
                    $row["barang_nama"] = $row["barang_nama"];
                    $row["qty_input"] = $row["qty_input"] .'  '. $row['satuanunit_nama'];
                    $row["alasan_retur"] = $row["alasan_retur"];

                    $data[$index] = $row;
                }

                $result['data'] = $data;
                $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
                $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

                return $result;
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } catch (\Exception $e){
                $result['error'] = $e->getMessage();
                return $result;
            }
        }

        public function actionCetakTransaksi($id)
        {
            $request = Yii::$app->request;
            $path = Yii::getAlias("@download") . "/gudang-informasi-retur-barang.pdf";
            $id = DocoHelpers::decrypt($id);
            $query = [
                "id" => $id
            ];
            try {
                $response = $this->_restGudang->get('informasi-retur-barang/cetak-transaksi', [
                    'query' => $query,
                    'save_to' => $path
                ]);
                return DocoHelpers::previewPdf($path);
            } catch (RequestException $e) {
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            } catch (\Exception $e) {
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
            }
        }

        public function actionCetakPdf()
        {
            $request = Yii::$app->request;
            $path = Yii::getAlias("@download") . "/gudang-informasi-retur-barang.pdf";
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            try {
                $response = $this->_restGudang->get('informasi-retur-barang/cetak-pdf', [
                    'query' => $filter,
                    'save_to' => $path
                ]);

                return DocoHelpers::previewPdf($path);
            } catch (RequestException $e) {
                return $e->getMessage();
                throw new \yii\web\HttpException(500);
            } catch (\Exception $e) {
                return $e->getMessage();
                throw new \yii\web\HttpException(500);
            }
        }

        public function actionExportExcel()
        {
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());

            try {
                $path = Yii::getAlias("@download") . "/informasi-retur-barang.xlsx";
                $response = $this->_restGudang->get('informasi-retur-barang/export-excel', [
                    'query' => $filter,
                    'save_to' => $path
                ]);
                return DocoHelpers::downloadFile($path,true);
           } catch (\Exception $e) {
                return $e->getMessage();
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
           } catch (RequestException $e){
                return $e->getMessage();
                throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
           }
        }
    }
?>
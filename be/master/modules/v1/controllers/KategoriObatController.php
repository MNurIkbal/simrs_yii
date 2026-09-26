<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\RestrictionObat;
use app\modules\v1\models\RestrictionAksesObat;
use app\modules\v1\models\RestrictionListObat;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\ObatAlkesView;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\CaraBayar;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\RestrictionAksesobatView;
use app\modules\v1\models\KategoriObatForm;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

class KategoriObatController extends \Doco\components\DocoActiveController
{

  public $modelClass = 'app\modules\v1\models\RestrictionObat';
  public function verbs()
  {
    $verbs = parent::verbs();
    $verbs["index"] = ["POST", "GET"];
    $verbs["update"] = ["POST", "PUT"];
    $verbs["create"] = ["POST"];
    $verbs["delete"] = ["POST", "DELETE"];
    $verbs["change-status"] = ["POST", "GET"];
    $verbs["check-active-restrictions"] = ["GET"];
    return $verbs;
  }

  public function actions()
  {
    $actions = parent::actions();
    unset($actions['index']);
    unset($actions['delete']);
    unset($actions['view']);
    unset($actions['create']);
    unset($actions['update']);
    return $actions;
  }

  public function actionIndex()
  {
    try {
      $model = new RestrictionAksesobatView;
      $query = $model->find();
      $request = Yii::$app->request;
      
      // Apply filters
      if(isset($_GET['advanced-filter'])) {
        $advancedFilter = $request->get('advanced-filter', []);
        
        if(isset($advancedFilter['instalasi_id']) && !empty($advancedFilter['instalasi_id'])) {
            $instalasiList = is_array($advancedFilter['instalasi_id']) ? $advancedFilter['instalasi_id'] : [$advancedFilter['instalasi_id']];
            $inList = implode("','", array_map('trim', $instalasiList));
            $query->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(instalasi_id AS TEXT), ',')) AS id WHERE trim(id) IN ('{$inList}'))"));
            unset($_GET['advanced-filter']['instalasi_id']);
        }

        if(isset($advancedFilter['penjamin_id']) && !empty($advancedFilter['penjamin_id'])) {
            $penjaminList = is_array($advancedFilter['penjamin_id']) ? $advancedFilter['penjamin_id'] : [$advancedFilter['penjamin_id']];
            $inList = implode("','", array_map('trim', $penjaminList));
            $query->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(penjamin_id AS TEXT), ',')) AS id WHERE trim(id) IN ('{$inList}'))"));
            unset($_GET['advanced-filter']['penjamin_id']);
        }
      }

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      
      $order = $request->get('order', '');
      if (!empty($order)) {
        $orderParts = explode(',', $order);
        foreach ($orderParts as $orderPart) {
          $orderPart = trim($orderPart);
          if (!empty($orderPart)) {
            $query->addOrderBy($orderPart);
          }
        }
      } else {
        $query->orderBy(['restriction_obat_nama' => SORT_ASC]);
      }
      
      $allRawData = $query->asArray()->all();
      
      $allGroupedData = [];
      foreach ($allRawData as $record) {
        $id = $record['restriction_obat_id'];
        
        if (!isset($allGroupedData[$id])) {
          $allGroupedData[$id] = $record;
        } else {
          $allGroupedData[$id]['instalasi_id'] .= ',' . $record['instalasi_id'];
          $allGroupedData[$id]['instalasi_nama'] .= ',' . $record['instalasi_nama'];
          $allGroupedData[$id]['penjamin_id'] .= ',' . $record['penjamin_id'];
          $allGroupedData[$id]['penjamin_nama'] .= ',' . $record['penjamin_nama'];
        }
      }
      
      $totalCount = count($allGroupedData);
      
      $page = $request->get('page', 1);
      $perPage = $request->get('per-page', 10);
      $offset = ($page - 1) * $perPage;
      
      $paginatedGroupedData = array_slice($allGroupedData, $offset, $perPage, true);
      
      $finalData = [];
      foreach ($paginatedGroupedData as $data) {
        $instalasiIds = array_unique(array_map('trim', explode(',', $data['instalasi_id'])));
        $instalasiNamas = array_unique(array_map('trim', explode(',', $data['instalasi_nama'])));
        $penjaminIds = array_unique(array_map('trim', explode(',', $data['penjamin_id'])));
        $penjaminNamas = array_unique(array_map('trim', explode(',', $data['penjamin_nama'])));
        
        // Replace IGD with "Rawat Darurat" and Rawat Jalan with "Rawat Jalan/Penunjang" in instalasi_nama
        $modifiedInstalasiNamas = [];
        foreach ($instalasiNamas as $nama) {
          if (trim($nama) === 'IGD') {
            $modifiedInstalasiNamas[] = 'Rawat Darurat';
          } elseif (trim($nama) === 'Rawat Jalan') {
            $modifiedInstalasiNamas[] = 'Rawat Jalan/Penunjang';
          } else {
            $modifiedInstalasiNamas[] = $nama;
          }
        }
        
        sort($modifiedInstalasiNamas);
        sort($penjaminNamas);
        
        $finalData[] = [
          'restriction_obat_id' => $data['restriction_obat_id'],
          'restriction_obat_nama' => $data['restriction_obat_nama'],
          'restriction_obat_namalainnya' => $data['restriction_obat_namalainnya'],
          'instalasi_id' => $instalasiIds,
          'instalasi_nama' => $modifiedInstalasiNamas,
          'penjamin_id' => $penjaminIds,
          'penjamin_nama' => $penjaminNamas,
          'carabayar_id' => $data['carabayar_id'],
          'carabayar_nama' => $data['carabayar_nama'],
          'jml' => $data['jml'],
          'is_active' => $data['is_active']
        ];
      }
      
      return [
        'data' => $finalData,
        'recordsTotal' => $totalCount,
        'recordsFiltered' => $totalCount
      ];
      
    } catch (\Exception $e) {
      return ['status' => 500, 'message' => $e->getMessage()];
    }
  }


  public function actionFiltersIndex()
  {
    $instalasiList = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI];
    $instalasi = Instalasi::find()
      ->select(['instalasi_id as id', 'instalasi_nama as text'])
      ->where(['is_active' => true])->andWhere(['IN', 'instalasi_id', $instalasiList])
      ->orderBy(['instalasi_nama' => SORT_ASC])->asArray()->all();

    // Replace IGD with "Rawat Darurat" and Rawat Jalan with "Rawat Jalan/Penunjang" in instalasi text
    foreach ($instalasi as &$item) {
      if ($item['text'] === 'IGD') {
        $item['text'] = 'Rawat Darurat';
      } elseif ($item['text'] === 'Rawat Jalan') {
        $item['text'] = 'Rawat Jalan/Penunjang';
      }
    }

    $penjamin = Penjamin::find()
      ->innerJoin('carabayar_m c', 'c.carabayar_id = penjamin_m.carabayar_id')
      ->select(['penjamin_m.penjamin_id as id', 'penjamin_m.penjamin_nama as text'])
      ->where(['penjamin_m.is_active' => true])
      ->andWhere(['c.is_active' => true])
      ->orderBy(['penjamin_m.penjamin_nama' => SORT_ASC])
      ->asArray()
      ->all();

    return [
      'instalasi' => $instalasi,
      'penjamin' => $penjamin
    ];
  }

  public function actionFilters()
  {
    $instalasiList = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI];
    $instalasi = Instalasi::find()
      ->select(['instalasi_id as id', 'instalasi_nama as text'])
      ->where(['is_active' => true])->andWhere(['IN', 'instalasi_id', $instalasiList])
      ->orderBy(['instalasi_nama' => SORT_ASC])->asArray()->all();

    // Replace IGD with "Rawat Darurat" and Rawat Jalan with "Rawat Jalan/Penunjang" in instalasi text
    foreach ($instalasi as &$item) {
      if ($item['text'] === 'IGD') {
        $item['text'] = 'Rawat Darurat';
      } elseif ($item['text'] === 'Rawat Jalan') {
        $item['text'] = 'Rawat Jalan/Penunjang';
      }
    }

    $caraBayarList = CaraBayar::find()
      ->select(['carabayar_id', 'carabayar_nama'])
      ->where(['is_active' => true])
      ->orderBy(['carabayar_nama' => SORT_ASC])
      ->asArray()
      ->all();

    $penjaminRaw = Penjamin::find()
      ->innerJoin('carabayar_m c', 'c.carabayar_id = penjamin_m.carabayar_id')
      ->select([
        'penjamin_m.penjamin_id',
        'penjamin_m.penjamin_nama',
        'penjamin_m.carabayar_id'
      ])
      ->where(['penjamin_m.is_active' => true])
      ->andWhere(['c.is_active' => true])
      ->orderBy(['penjamin_m.penjamin_nama' => SORT_ASC])
      ->asArray()
      ->all();

    $caraBayarMap = [];
    foreach ($caraBayarList as $c) {
      $caraBayarId = ArrayHelper::getValue($c, 'carabayar_id');
      $caraBayarMap[$caraBayarId] = [
        'id' => $caraBayarId,
        'text' => ArrayHelper::getValue($c, 'carabayar_nama'),
        'children' => [],
      ];
    }

    foreach ($penjaminRaw as $p) {
      $caraBayarId = ArrayHelper::getValue($p, 'carabayar_id');
      $caraBayarMap[$caraBayarId]['children'][] = [
        'id' => ArrayHelper::getValue($p, 'penjamin_id'),
        'text' => ArrayHelper::getValue($p, 'penjamin_nama'),
      ];
    }

    $penjaminList = array_values($caraBayarMap);

    return [
      'instalasi' => $instalasi,
      'penjamin' => $penjaminList
    ];
  }

  public function actionSave()
  {
    $request = Yii::$app->request;

    $restriction_obat_id = $request->post('restriction_obat_id', null);
    $kategori = $request->post('kategori', '');
    $instalasi_id = $request->post('instalasi_id', []);
    $penjamin_id = $request->post('penjamin_id', []);
    $detail_obat = $request->post('detail_obat', '');
    
    $isUpdate = !empty($restriction_obat_id);
    
    $model = new KategoriObatForm();
    $model->kategori = $kategori;
    $model->instalasi_id = $instalasi_id;
    $model->penjamin_id = $penjamin_id;
    $model->detail_obat = $detail_obat;
    
    if ($isUpdate) {
      $model->restriction_obat_id = $restriction_obat_id;
      $model->scenario = KategoriObatForm::SCENARIO_UPDATE;
    }
    
    if (!$model->validate()) {
      $firstError = '';
      foreach ($model->errors as $fieldErrors) {
        if (!empty($fieldErrors)) {
          $firstError = $fieldErrors[0];
          break;
        }
      }
      
      return DocoHelpers::response([
        'status' => 422,
        'title' => 'Proses Gagal!',
        'text' => $firstError ?: 'Terjadi kesalahan validasi',
        'data' => $model->errors
      ]);
    }
    
    if (!empty($detail_obat)) {
      if (is_string($detail_obat)) {
        $detail_obat = json_decode($detail_obat, true);
      }
      if (is_array($detail_obat) && isset($detail_obat[0]['obatalkes_id'])) {
        $detail_obat = array_column($detail_obat, 'obatalkes_id');
      }
    }

    $instalasi_id = array_filter($model->instalasi_id, function ($value) {
      return !empty($value);
    });
    $penjamin_id = array_filter($model->penjamin_id, function ($value) {
      return !empty($value);
    });


    if (is_array($instalasi_id)) {
      $instalasi_ids = array_values($instalasi_id);
    } else {
      $instalasi_ids = [$instalasi_id];
    }

    if (is_array($penjamin_id)) {
      $penjamin_ids = array_values($penjamin_id);
    } else {
      $penjamin_ids = [$penjamin_id];
    }

    foreach ($instalasi_ids as $inst_id) {
      foreach ($penjamin_ids as $pen_id) {
        $existingMapping = RestrictionAksesobatView::find()
          ->where(['is_active' => true])
          ->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(instalasi_id AS TEXT), ',')) AS id WHERE trim(id) = '{$inst_id}')"))
          ->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(penjamin_id AS TEXT), ',')) AS id WHERE trim(id) = '{$pen_id}')"));
        
        if ($isUpdate) {
          $existingMapping->andWhere(['!=', 'restriction_obat_id', $restriction_obat_id]);
        }
        
        $existingMapping = $existingMapping->one();

        if ($existingMapping) {
          return DocoHelpers::response([
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Kombinasi Instalasi dan Penjamin yang dipilih sudah ada. Silahkan ubah inputan anda terlebih dahulu.',
            'data' => ['instalasi_id' => ['Kombinasi instalasi dan penjamin sudah ada']]
          ]);
        }
      }
    }

    $transaction = Yii::$app->db->beginTransaction();

    $namaChanged = false;
    
    try {
      if ($isUpdate) {
        $model = RestrictionObat::findOne($restriction_obat_id);
        if (!$model) {
          $transaction->rollBack();
          return DocoHelpers::response([
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Data kategori obat tidak ditemukan',
            'data' => ['restriction_obat_id' => ['Data tidak ditemukan']]
          ]);
        }
        
        $namaChanged = ($model->restriction_obat_nama !== $kategori);
        
        if ($namaChanged) {
          $model->restriction_obat_nama = $kategori;
          $model->restriction_obat_namalainnya = $kategori;
          
          if (!$model->validate() || !$model->save()) {
            $transaction->rollBack();
            $errors = DocoHelpers::parseError($model->errors, 'KategoriObatForm');
            return DocoHelpers::response([
              'status' => 422,
              'title' => 'Proses Gagal!',
              'text' => 'Terjadi kesalahan saat menyimpan data',
              'data' => $errors
            ]);
          }
        }
        
        RestrictionAksesObat::deleteAll(['restriction_obat_id' => $restriction_obat_id]);
        RestrictionListObat::deleteAll(['restriction_obat_id' => $restriction_obat_id]);
      } else {
        $model = new RestrictionObat;
        $model->restriction_obat_nama = $kategori;
        $model->restriction_obat_namalainnya = $kategori;

        if (!$model->validate() || !$model->save()) {
          $transaction->rollBack();
          $errors = DocoHelpers::parseError($model->errors, 'KategoriObatForm');
          return DocoHelpers::response([
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Terjadi kesalahan saat menyimpan data',
            'data' => $errors
          ]);
        }
      }

      $restriction_obat_id = $model->restriction_obat_id;

      $mappingCount = 0;
      $obatCount = 0;
      foreach ($instalasi_ids as $inst_id) {
        foreach ($penjamin_ids as $pen_id) {
          $aksesObat = new RestrictionAksesObat;
          $aksesObat->restriction_obat_id = $restriction_obat_id;
          $aksesObat->instalasi_id = $inst_id;
          $aksesObat->penjamin_id = $pen_id;

          if (!$aksesObat->validate() || !$aksesObat->save()) {
            $transaction->rollBack();
            $errors = DocoHelpers::parseError($aksesObat->errors, 'RestrictionAksesObat');
            return DocoHelpers::response([
              'status' => 422,
              'title' => 'Proses Gagal!',
              'text' => 'Terjadi kesalahan saat menyimpan mapping akses',
              'data' => $errors
            ]);
          }
          $mappingCount++;
        }
      }
      
      if (!is_array($detail_obat)) {
        $transaction->rollBack();
        return DocoHelpers::response([
          'status' => 422,
          'title' => 'Proses Gagal!',
          'text' => 'Format detail obat tidak valid',
          'data' => ['detail_obat' => ['Format detail obat tidak valid']]
        ]);
      }

      foreach ($detail_obat as $obatalkes_id) {
        $listObat = new RestrictionListObat;
        $listObat->restriction_obat_id = $restriction_obat_id;
        $listObat->obatalkes_id = $obatalkes_id;

        if (!$listObat->validate() || !$listObat->save()) {
          $transaction->rollBack();
          $errors = DocoHelpers::parseError($listObat->errors, 'RestrictionListObat');
          return DocoHelpers::response([
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Terjadi kesalahan saat menyimpan mapping obat',
            'data' => $errors
          ]);
        }
        $obatCount++;
      }
      

      $transaction->commit();

      $message = $isUpdate ? 'Data kategori obat berhasil diperbarui' : 'Data kategori obat berhasil disimpan';
      
      return DocoHelpers::response([
        'status' => 200,
        'title' => 'Proses Berhasil!',
        'text' => $message,
        'data' => [
          'restriction_obat_id' => $restriction_obat_id,
          'is_update' => $isUpdate,
          'nama_changed' => $isUpdate ? $namaChanged : null,
          'mapping_count' => $mappingCount,
          'obat_count' => $obatCount
        ]
      ]);
    } catch (\yii\db\Exception $e) {
      $transaction->rollBack();
      return DocoHelpers::response([
        'status' => 500,
        'title' => 'Proses Gagal!',
        'text' => 'Terjadi kesalahan database: ' . $e->getMessage()
      ]);
    } catch (\Exception $e) {
      $transaction->rollBack();
      return DocoHelpers::response([
        'status' => 500,
        'title' => 'Proses Gagal!',
        'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ]);
    }
  }

  public function actionUpdate()
  {
    $request = Yii::$app->request;
    $restriction_obat_id = $request->post('restriction_obat_id');

    if (empty($restriction_obat_id)) {
      return DocoHelpers::response([
        'status' => 422,
        'title' => 'Proses Gagal!',
        'text' => 'ID kategori obat tidak boleh kosong',
        'data' => ['restriction_obat_id' => ['ID kategori obat tidak boleh kosong']]
      ]);
    }

    $restriction_obat_id = (int) $restriction_obat_id;

    $kategori = trim($request->post('kategori', ''));
    $instalasi_id = $request->post('instalasi_id', []);
    $penjamin_id = $request->post('penjamin_id', []);
    $detail_obat = $request->post('detail_obat', '');
    
    if (!empty($detail_obat)) {
      if (is_string($detail_obat)) {
        $detail_obat = json_decode($detail_obat, true);
      }
      if (is_array($detail_obat) && isset($detail_obat[0]['obatalkes_id'])) {
        $detail_obat = array_column($detail_obat, 'obatalkes_id');
      }
    }

    $model = new KategoriObatForm();
    $model->kategori = $kategori;
    $model->instalasi_id = $instalasi_id;
    $model->penjamin_id = $penjamin_id;
    $model->detail_obat = $detail_obat;
    $model->restriction_obat_id = $restriction_obat_id;
    $model->scenario = KategoriObatForm::SCENARIO_UPDATE;
    
    if (!$model->validate()) {
      $firstError = '';
      foreach ($model->errors as $fieldErrors) {
        if (!empty($fieldErrors)) {
          $firstError = $fieldErrors[0];
          break;
        }
      }
      
      return DocoHelpers::response([
        'status' => 422,
        'title' => 'Proses Gagal!',
        'text' => $firstError ?: 'Terjadi kesalahan validasi',
        'data' => $model->errors
      ]);
    }

    $instalasi_id = array_filter($model->instalasi_id, function ($value) {
      return !empty($value);
    });
    $penjamin_id = array_filter($model->penjamin_id, function ($value) {
      return !empty($value);
    });

    if (is_array($instalasi_id)) {
      $instalasi_ids = array_values($instalasi_id);
    } else {
      $instalasi_ids = [$instalasi_id];
    }

    if (is_array($penjamin_id)) {
      $penjamin_ids = array_values($penjamin_id);
    } else {
      $penjamin_ids = [$penjamin_id];
    }

    foreach ($instalasi_ids as $inst_id) {
      foreach ($penjamin_ids as $pen_id) {
        $inst_id = (int) $inst_id;
        $pen_id = (int) $pen_id;
        
        $existingMapping = RestrictionAksesobatView::find()
          ->where(['is_active' => true])
          ->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(instalasi_id AS TEXT), ',')) AS id WHERE trim(id) = '{$inst_id}')"))
          ->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(penjamin_id AS TEXT), ',')) AS id WHERE trim(id) = '{$pen_id}')"))
          ->andWhere(['!=', 'restriction_obat_id', $restriction_obat_id])
          ->one();

        if ($existingMapping) {
          return DocoHelpers::response([
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Kombinasi Instalasi dan Penjamin yang dipilih sudah ada. Silahkan ubah inputan anda terlebih dahulu.',
            'data' => ['instalasi_id' => ['Kombinasi instalasi dan penjamin sudah ada']]
          ]);
        }
      }
    }

    $transaction = Yii::$app->db->beginTransaction();

    try {
      $model = RestrictionObat::findOne($restriction_obat_id);
      if (!$model) {
        $transaction->rollBack();
        return DocoHelpers::response([
          'status' => 404,
          'title' => 'Proses Gagal!',
          'text' => 'Data tidak ditemukan'
        ]);
      }

      $model->restriction_obat_nama = $kategori;
      $model->restriction_obat_namalainnya = $kategori;

      if (!$model->validate() || !$model->save()) {
        $transaction->rollBack();
        $errors = DocoHelpers::parseError($model->errors, 'KategoriObatForm');
        return DocoHelpers::response([
          'status' => 422,
          'title' => 'Proses Gagal!',
          'text' => 'Terjadi kesalahan saat mengupdate data',
          'data' => $errors
        ]);
      }

      RestrictionAksesObat::deleteAll(['restriction_obat_id' => $restriction_obat_id]);
      RestrictionListObat::deleteAll(['restriction_obat_id' => $restriction_obat_id]);

      foreach ($instalasi_ids as $inst_id) {
        foreach ($penjamin_ids as $pen_id) {
          $aksesObat = new RestrictionAksesObat;
          $aksesObat->restriction_obat_id = $restriction_obat_id;
          $aksesObat->instalasi_id = $inst_id;
          $aksesObat->penjamin_id = $pen_id;

          if (!$aksesObat->validate() || !$aksesObat->save()) {
            $transaction->rollBack();
            $errors = DocoHelpers::parseError($aksesObat->errors, 'RestrictionAksesObat');
            return DocoHelpers::response([
              'status' => 422,
              'title' => 'Proses Gagal!',
              'text' => 'Terjadi kesalahan saat menyimpan mapping akses',
              'data' => $errors
            ]);
          }
        }
      }

      RestrictionListObat::updateAll(
        ['is_deleted' => true],
        ['restriction_obat_id' => $restriction_obat_id]
      );

      if (!is_array($detail_obat)) {
        $transaction->rollBack();
        return DocoHelpers::response([
          'status' => 422,
          'title' => 'Proses Gagal!',
          'text' => 'Format detail obat tidak valid',
          'data' => ['detail_obat' => ['Format detail obat tidak valid']]
        ]);
      }

      foreach ($detail_obat as $obatalkes_id) {
        $obatalkes_id = (int) $obatalkes_id;
        
        $listObat = new RestrictionListObat;
        $listObat->restriction_obat_id = $restriction_obat_id;
        $listObat->obatalkes_id = $obatalkes_id;

        if (!$listObat->validate() || !$listObat->save()) {
          $transaction->rollBack();
          $errors = DocoHelpers::parseError($listObat->errors, 'RestrictionListObat');
          return DocoHelpers::response([
            'status' => 422,
            'title' => 'Proses Gagal!',
            'text' => 'Terjadi kesalahan saat menyimpan mapping obat',
            'data' => $errors
          ]);
        }
      }

      $transaction->commit();

      return DocoHelpers::response([
        'status' => 200,
        'title' => 'Proses Berhasil!',
        'text' => 'Data berhasil diupdate'
      ]);
    } catch (\yii\db\Exception $e) {
      $transaction->rollBack();
      return DocoHelpers::response([
        'status' => 500,
        'title' => 'Proses Gagal!',
        'text' => 'Terjadi kesalahan database: ' . $e->getMessage()
      ]);
    } catch (\Exception $e) {
      $transaction->rollBack();
      return DocoHelpers::response([
        'status' => 500,
        'title' => 'Proses Gagal!',
        'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ]);
    }
  }

  public function actionDelete($id = null)
  {
    $request = Yii::$app->request;

    if ($id !== null) {
      try {
        $id = (int) $id;
        $model = RestrictionObat::findOne($id);

        if ($model) {
          return $this->deleteKategoriObat($model, $id);
        } else {
          return [
            'success' => false,
            'message' => 'Data tidak ditemukan'
          ];
        }
      } catch (\Exception $e) {
        return [
          'success' => false,
          'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ];
      }
    }
  }

  public function actionGetData()
  {
    $request = Yii::$app->request;
    $id = $request->get('id', null);
    try {
      $model = RestrictionObat::findOne($id);
      if ($model) {
        $aksesObat = RestrictionAksesObat::find()
          ->select(['instalasi_id', 'penjamin_id'])
          ->where(['restriction_obat_id' => $id, 'is_deleted' => false])
          ->asArray()
          ->all();

        $listObat = RestrictionListObat::find()
          ->select(['obatalkes_id'])
          ->where(['restriction_obat_id' => $id, 'is_deleted' => false])
          ->asArray()
          ->all();

        $instalasi_ids = array_column($aksesObat, 'instalasi_id');
        $penjamin_ids = array_column($aksesObat, 'penjamin_id');
        $obatalkes_ids = array_column($listObat, 'obatalkes_id');

        $results = [
          'data' => $model,
          'mapping' => [
            'instalasi_ids' => array_unique($instalasi_ids),
            'penjamin_ids' => array_unique($penjamin_ids),
            'obatalkes_ids' => $obatalkes_ids
          ]
        ];
      } else {
        $results = ['message' => 'Data Tidak Ditemukan'];
      }
      return $results;
    } catch (\yii\db\Exception $e) {
      \Yii::$app->response->statusCode = 500;
      return [
        'message' => $e->getMessage()
      ];
    } catch (\Exception $e) {
      \Yii::$app->response->statusCode = 500;
      return [
        'message' => $e->getMessage()
      ];
    }
  }
  
  public function actionGetKategoriByObatId($obat_id)
  {
      // 1. Cari semua restriction_obat_id yang mengandung obatalkes_id ini
      $list = RestrictionListObat::find()
          ->select(['restriction_obat_id'])
          ->where(['obatalkes_id' => $obat_id, 'is_deleted' => false, 'is_active' => true])
          ->asArray()
          ->all();
  
      $restriction_ids = array_column($list, 'restriction_obat_id');
      if (empty($restriction_ids)) {
          return ['data' => []];
      }
  
      // 2. Ambil nama kategori dari RestrictionObat
      $categories = RestrictionObat::find()
          ->select(['restriction_obat_nama'])
          ->where(['restriction_obat_id' => $restriction_ids, 'is_deleted' => false, 'is_active' => true])
          ->asArray()
          ->all();
  
      return ['data' => $categories];
  }

  public function actionChangeStatus()
  {
    $request = Yii::$app->request;
    $isActive = $request->get('is_active', true);
    $id = $request->get('restriction_obat_id');

    if (empty($id)) {
      return DocoHelpers::response([
        'status' => 422,
        'title' => 'Proses Gagal!',
        'text' => 'ID kategori obat wajib diisi',
        'data' => ['restriction_obat_id' => ['ID kategori obat wajib diisi']]
      ]);
    }

    $user = \Yii::$app->user->identity;
    if (!$user) {
      return DocoHelpers::response([
        'status' => 401,
        'title' => 'Unauthorized!',
        'text' => 'User tidak terautentikasi'
      ]);
    }

    try {
      $model = RestrictionObat::findOne($id);
      if (!$model) {
        return DocoHelpers::response([
          'status' => 404,
          'title' => 'Proses Gagal!',
          'text' => 'Data tidak ditemukan'
        ]);
      }

      $isActive = $isActive == 0 ? false : true;

      if ($isActive) {
        $currentRestriction = RestrictionAksesobatView::find()
          ->where(['restriction_obat_id' => $id])
          ->all();

        if ($currentRestriction) {
          $currentIns = [];
          $currentPenj = [];
          foreach($currentRestriction as $data){
            $instalasiIds = array_map('trim', explode(',', $data['instalasi_id']));
            $currentIns = array_merge($currentIns, $instalasiIds);
            
            $penjaminIds = array_map('trim', explode(',', $data['penjamin_id']));
            $currentPenj = array_merge($currentPenj, $penjaminIds);
          }
          $currentIns = array_unique($currentIns);
          $currentPenj = array_unique($currentPenj);
          $currentIns = implode(',', $currentIns);
          $currentPenj = implode(',', $currentPenj);
          $instalasiIds = array_map('trim', explode(',', $currentIns));
          $penjaminIds = array_map('trim', explode(',', $currentPenj));

          foreach ($instalasiIds as $inst_id) {
            foreach ($penjaminIds as $pen_id) {
              $conflictingMapping = RestrictionAksesobatView::find()
                ->where(['is_active' => true])
                ->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(instalasi_id AS TEXT), ',')) AS id WHERE trim(id) = '{$inst_id}')"))
                ->andWhere(new \yii\db\Expression("EXISTS (SELECT 1 FROM unnest(string_to_array(CAST(penjamin_id AS TEXT), ',')) AS id WHERE trim(id) = '{$pen_id}')"))
                ->andWhere(['!=', 'restriction_obat_id', $id])
                ->one();

              if ($conflictingMapping) {
                return DocoHelpers::response([
                  'status' => 422,
                  'title' => 'Proses Gagal!',
                  'text' => 'Kombinasi Instalasi dan Penjamin yang dipilih sudah ada. Silahkan ubah inputan anda terlebih dahulu.',
                  'data' => ['status' => ['Kombinasi instalasi dan penjamin sudah ada']]
                ]);
              }
            }
          }
        }
      }

      $model->is_active = $isActive;

      if ($model->validate() && $model->save()) {
        RestrictionAksesObat::updateAll(
          ['is_active' => $isActive],
          ['restriction_obat_id' => $id, 'is_deleted' => false]
        );

        RestrictionListObat::updateAll(
          ['is_active' => $isActive],
          ['restriction_obat_id' => $id, 'is_deleted' => false]
        );

        $statusText = $isActive ? 'diaktifkan' : 'dinonaktifkan';
        return DocoHelpers::response([
          'status' => 200,
          'title' => 'Proses Berhasil!',
          'text' => "Status berhasil {$statusText}"
        ]);
      } else {
        $errors = DocoHelpers::parseError($model->errors, 'RestrictionObat');
        $firstError = '';
        foreach ($model->errors as $fieldErrors) {
          if (!empty($fieldErrors)) {
            $firstError = $fieldErrors[0];
            break;
          }
        }
        return DocoHelpers::response([
          'status' => 422,
          'title' => 'Proses Gagal!',
          'text' => $firstError ?: 'Terjadi kesalahan saat mengubah status',
          'data' => $errors
        ]);
      }
    } catch (\yii\db\Exception $e) {
      return DocoHelpers::response([
        'status' => 500,
        'title' => 'Proses Gagal!',
        'text' => 'Terjadi kesalahan database: ' . $e->getMessage()
      ]);
    } catch (\Exception $e) {
      return DocoHelpers::response([
        'status' => 500,
        'title' => 'Proses Gagal!',
        'text' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
      ]);
    }
  }

  public function actionCheckActiveRestrictions()
  {
    $request = Yii::$app->request;
    $restrictionObatId = $request->get('restriction_obat_id');

    if (empty($restrictionObatId)) {
      return DocoHelpers::response([
        'success' => false,
        'message' => 'ID restriction obat wajib diisi',
        'active_count' => 0
      ]);
    }

    try {
      $activeCount = RestrictionAksesObat::find()
        ->where([
          'restriction_obat_id' => $restrictionObatId,
          'is_active' => true,
          'is_deleted' => false
        ])
        ->select('instalasi_id')
        ->distinct()
        ->count();

      return DocoHelpers::response([
        'success' => true,
        'active_count' => (int)$activeCount,
        'message' => 'Data berhasil diambil'
      ]);
    } catch (\Exception $e) {
      return DocoHelpers::response([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
        'active_count' => 0
      ]);
    }
  }

  public function actionGetObatDetails()
  {
    $request = Yii::$app->request;
    $obatIds = $request->post('obat_ids', []);

    if (empty($obatIds)) {
      return [
        'success' => false,
        'message' => 'No obat IDs provided',
        'data' => []
      ];
    }

    try {
      $obatDataMain = ObatAlkes::find()
        ->select([
          'obatalkes_id',
          'obatalkes_nama',
          'obatalkes_kode',
          'is_active'
        ])
        ->where(['IN', 'obatalkes_id', $obatIds])
        ->asArray()
        ->all();

      $obatData = ObatAlkesView::find()
        ->select([
          'obatalkes_id',
          'obatalkes_nama',
          'obatalkes_kode',
          'is_active'
        ])
        ->where(['IN', 'obatalkes_id', $obatIds])
        ->asArray()
        ->all();

      $finalData = !empty($obatData) ? $obatData : $obatDataMain;

      return [
        'success' => true,
        'data' => $finalData
      ];
    } catch (\Exception $e) {
      return [
        'success' => false,
        'message' => $e->getMessage(),
        'data' => []
      ];
    }
  }



  public function actionListObatAlkesInfinity()
  {
    $params = Yii::$app->request;
    $term = $params->get('term');
    $page = $params->get('page', 0);
    $limit = $params->get('limit', 5);
    $offset = $params->get('offset', 0);

    $model = new ObatAlkes();
    $query = $model::find();
    $query->leftJoin('jenisobatalkes_m', 'jenisobatalkes_m.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id');

    $group = $params->get('group', null);
    if (!empty($group)) {
      $query->andWhere(['jenisobatalkes_m.group_jenisobat' => $group]);
    }
    if ($term) {
      $query->andWhere([
        'or',
        ['ILIKE', 'LOWER(obatalkes_m.obatalkes_nama)', $term],
        ['ILIKE', 'LOWER(obatalkes_m.obatalkes_namalain)', $term],
        ['ILIKE', 'LOWER(obatalkes_m.obatalkes_kode)', $term]
      ]);
    }

    return $query->offset($offset)->limit($limit)->asArray()->all();
  }



  /**
   * Hapus seluruh kategori obat dan mapping terkait
   */
  private function deleteKategoriObat($model, $restriction_obat_id)
  {
    if (!$model->delete()) {
      return [
        'success' => false,
        'message' => 'Gagal menghapus data'
      ];
    }

    RestrictionAksesObat::deleteAll(['restriction_obat_id' => $restriction_obat_id]);
    RestrictionListObat::deleteAll(['restriction_obat_id' => $restriction_obat_id]);

    return [
      'success' => true,
      'message' => 'Data berhasil dihapus'
    ];
  }


}

<?php

use yii\db\Migration;

/**
 * Class m201020_072550_oddo_view_20201020
 */
class m201020_072550_oddo_view_20201020 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute("
        CREATE VIEW \"public\".\"int_tindakan_v\" AS  SELECT concat('TND', daftartindakan_m.daftartindakan_id) AS sync_id_api,
    daftartindakan_m.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    daftartindakan_m.daftartindakan_nama AS name,
    concat('TND', daftartindakan_m.kelompoktindakan_id) AS categ_id,
    NULL::text AS uom_po_id,
    351 AS uom_id,
    daftartindakan_m.daftartindakan_kode AS default_code,
    'service'::text AS type,
    false AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    NULL::text AS brand,
    NULL::text AS manufacturer_code,
    NULL::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    'TINDAKAN'::text AS jenis
   FROM daftartindakan_m
  WHERE (daftartindakan_m.is_deleted = false)
UNION ALL
 SELECT concat('PKT', tipepaket_m.tipepaket_id) AS sync_id_api,
    tipepaket_m.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    tipepaket_m.tipepaket_nama AS name,
    '-'::text AS categ_id,
    NULL::text AS uom_po_id,
    351 AS uom_id,
    tipepaket_m.tipepaket_kode AS default_code,
    'service'::text AS type,
    false AS wipro_block,
    NULL::text AS strength,
    NULL::text AS catalog_code,
    NULL::text AS brand,
    NULL::text AS manufacturer_code,
    NULL::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    'PAKET'::text AS jenis
   FROM tipepaket_m
  WHERE (tipepaket_m.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_tindakan_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_servicecategory_v\" AS  SELECT
        CASE
            WHEN (servicecategory_m.is_obat = false) THEN concat('TND', servicecategory_m.servicecategory_id)
            ELSE concat('OBT', servicecategory_m.servicecategory_id)
        END AS sync_id_api,
    '-'::text AS parent_id,
    servicecategory_m.servicecategory_nama AS name,
        CASE
            WHEN (servicecategory_m.is_obat = false) THEN true
            ELSE false
        END AS sync_is_service,
        CASE
            WHEN (servicecategory_m.is_obat = false) THEN 'service'::text
            ELSE 'normal'::text
        END AS type,
    servicecategory_m.is_active AS active,
    6 AS sync_type
   FROM servicecategory_m;");

    $this->execute('ALTER TABLE "public"."int_servicecategory_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_saleorderupdate_v\" AS  SELECT pembayaran_r.id,
    pendaftaran_t.pendaftaran_id AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS confirmation_date,
    pendaftaran_t.pasien_id AS partner_id,
    pendaftaran_t.tgl_pendaftaran AS date_order,
        CASE
            WHEN (pendaftaran_t.instalasi_id = 1) THEN 1
            WHEN (pendaftaran_t.instalasi_id = 3) THEN 2
            WHEN (pendaftaran_t.instalasi_id = 2) THEN 3
            WHEN (pendaftaran_t.instalasi_id = 6) THEN 6
            WHEN (pendaftaran_t.instalasi_id = 21) THEN 5
            ELSE 4
        END AS patient_type,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN COALESCE(pendaftaran_t.penjamin_id, 0)
            ELSE COALESCE(pasienadmisi_r.penjamin_id, 0)
        END AS payer_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.s_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
    'done'::text AS state,
    ((pembayaran_r.total_tunai + pembayaran_r.total_nontunai) - pembayaran_r.total_kembalian) AS personal_amount,
    pembayaran_r.total_dijamin AS payer_amount,
    total_ditagihkan.total_tagihan AS total_amount,
    pembayaran_r.is_update
   FROM ((((((((pembayaran_r
     LEFT JOIN pendaftaran_t ON ((pembayaran_r.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasienadmisi_r ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id)))
     LEFT JOIN penjamin_m p1 ON ((pendaftaran_t.penjamin_id = p1.penjamin_id)))
     LEFT JOIN penjamin_m p2 ON ((pasienadmisi_r.penjamin_id = p2.penjamin_id)))
     LEFT JOIN carabayar_m cb1 ON ((p1.carabayar_id = cb1.carabayar_id)))
     LEFT JOIN carabayar_m cb2 ON ((p2.carabayar_id = cb2.carabayar_id)))
     JOIN pembayaranpelayanan_t ON ((pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
     LEFT JOIN ( SELECT pembayaran_t.pembayaran_id,
            pembayaran_t.total_tagihan
           FROM pembayaran_t
          GROUP BY pembayaran_t.pembayaran_id, pembayaran_t.total_tagihan) total_ditagihkan ON ((pembayaran_r.pembayaran_id = total_ditagihkan.pembayaran_id)))
  WHERE (pembayaran_r.is_update = false);");

    $this->execute('ALTER TABLE "public"."int_saleorderupdate_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_penjamin_v\" AS  SELECT concat('PEN', penjamin_m.penjamin_id) AS sync_id_api,
    penjamin_m.s_kode AS vendor_code,
    penjamin_m.penjamin_nama AS name,
    penjamin_m.penjamin_nama AS display_name,
    carabayar_m.carabayar_nama AS customer_type_api,
    '-'::text AS contact_person,
    '-'::text AS phone,
    '-'::text AS mobile,
    '-'::text AS fax,
    '-'::text AS email,
    '-'::text AS website,
    penjamin_m.alamat_penjamin AS street,
    penjamin_m.alamat_penjamin AS street2,
    penjamin_m.alamat_penjamin AS street3,
    '-'::text AS city,
    '-'::text AS zip,
    NULL::text AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS taxid,
        CASE
            WHEN (penjamin_m.is_active = true) THEN false
            ELSE true
        END AS wipro_block,
        CASE
            WHEN (carabayar_m.groupcarabayar_id = 417) THEN false
            ELSE true
        END AS insurance,
    true AS customer,
    penjamin_m.is_active AS active,
    6 AS sync_type
   FROM (penjamin_m
     JOIN carabayar_m ON (((penjamin_m.carabayar_id = carabayar_m.carabayar_id) AND (carabayar_m.is_deleted = false))))
  WHERE (penjamin_m.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_penjamin_v" OWNER TO "postgres";');

    $this->execute("CREATE VIEW \"public\".\"int_stokmoveheader_v\" AS  SELECT concat('RSP', penjualanresep_t.penjualanresep_id) AS sync_id_api,
    penjualanresep_t.noresep AS name,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pasien_m.nama_pasien
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE NULL::character varying
        END AS partner_id,
    penjualanresep_t.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS aspicking_type,
    penjualanresep_t.tglresep AS date_move,
    NULL::text AS min_date,
    6 AS sync_type
   FROM ((penjualanresep_t
     LEFT JOIN pasien_m ON ((penjualanresep_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE (penjualanresep_t.is_deleted = false)
UNION ALL
 SELECT concat('BHP', pendaftaran_t.pendaftaran_id) AS sync_id_api,
    pendaftaran_t.no_pendaftaran AS name,
    pasien_m.nama_pasien AS partner_id,
    obatalkespasien_t.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS aspicking_type,
    pendaftaran_t.tgl_pendaftaran AS date_move,
    NULL::text AS min_date,
    6 AS sync_type
   FROM ((obatalkespasien_t
     JOIN pendaftaran_t ON ((obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false))
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, pasien_m.nama_pasien, obatalkespasien_t.ruangan_id, 'stockout'::text, 'stockout'::text, pendaftaran_t.tgl_pendaftaran, NULL::text, 6::integer;");

    $this->execute('ALTER TABLE "public"."int_stokmoveheader_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_kelompoktindakan_v\" AS  SELECT concat('TND', kelompoktindakan_m.kelompoktindakan_id) AS sync_id_api,
    '-'::text AS parent_id,
    kelompoktindakan_m.kelompoktindakan_nama AS name,
    true AS sync_is_service,
    'service'::text AS type,
    kelompoktindakan_m.is_active AS active,
    6 AS sync_type
   FROM kelompoktindakan_m
  WHERE (kelompoktindakan_m.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_kelompoktindakan_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_kelompokobat_v\" AS  SELECT concat('OBT', jenisobatalkes_m.jenisobatalkes_id) AS sync_id_api,
    '-'::text AS parent_id,
    jenisobatalkes_m.jenisobatalkes_nama AS name,
    false AS sync_is_service,
    'normal'::text AS type,
    jenisobatalkes_m.is_active AS active,
    6 AS sync_type
   FROM jenisobatalkes_m
  WHERE (jenisobatalkes_m.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_kelompokobat_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_ruangan_v\" AS  SELECT ruangan_m.ruangan_id AS sync_id_api,
    ruangan_m.ruangan_id AS parent_id,
    ruangan_m.ruangan_nama AS name,
    true AS is_store,
    false AS is_mainstore,
    false AS is_substore,
    false AS is_cartstore,
    ruangan_m.is_active AS active,
    6 AS sync_type
   FROM ruangan_m
  WHERE (ruangan_m.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_ruangan_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_satuanunit_v\" AS  SELECT satuanunit_r.satuanunit_id AS sync_id_api,
    satuanunit_r.satuanunit_nama AS name,
    satuanunit_r.satuanunit_namalain AS code,
    satuanunit_r.is_active AS active,
    1 AS factor,
    'reference'::text AS uom_type,
    '1::int'::text AS category_id,
    6 AS sync_type,
    satuanunit_r.keterangan_rekap,
    satuanunit_r.id,
    satuanunit_r.is_sent,
    satuanunit_r.is_sending
   FROM satuanunit_r;");

    $this->execute('ALTER TABLE "public"."int_satuanunit_v" OWNER TO "postgres";');

    $this->execute("
    CREATE VIEW \"public\".\"int_obat_v\" AS  SELECT concat('OBT', obatalkes_r.obatalkes_id) AS sync_id_api,
    obatalkes_r.is_active AS active,
    true AS sale_ok,
    true AS purchase_ok,
    obatalkes_r.obatalkes_nama AS name,
    concat('OBT', obatalkes_r.jenisobatalkes_id) AS categ_id,
    obatalkes_r.satuankecil_id AS uom_id,
    obatalkes_r.obatalkes_kode AS default_code,
    'product'::text AS type,
    false AS wipro_block,
    obatalkes_r.strength,
    NULL::text AS catalog_code,
    '-'::text AS brand,
    NULL::text AS manufacturer_code,
    '-'::text AS manufacturer_name,
    NULL::text AS pharmacalogy,
    NULL::text AS shelf,
    1 AS conversion_rate,
    6 AS sync_type,
    obatalkes_r.keterangan_rekap,
    obatalkes_r.id,
    obatalkes_r.is_sent,
    obatalkes_r.is_sending
   FROM obatalkes_r;");

    $this->execute('ALTER TABLE "public"."int_obat_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_batchobat_v\" AS  SELECT stokobatalkes_t.nobatch AS sync_id_api,
    stokobatalkes_t.nobatch AS name,
    concat('OBT', obatalkes_m.obatalkes_id) AS product_id,
    obatalkes_m.satuankecil_id AS product_uom_id,
    stokobatalkes_t.tglkadaluarsa AS expired_date,
    6 AS sync_type
   FROM (stokobatalkes_t
     JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
  WHERE (stokobatalkes_t.is_deleted = false)
  GROUP BY stokobatalkes_t.nobatch, obatalkes_m.obatalkes_id, obatalkes_m.satuankecil_id, stokobatalkes_t.tglkadaluarsa;");

    $this->execute('ALTER TABLE "public"."int_batchobat_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchase_v\" AS  SELECT concat('POS', penerimaansupp_r.penerimaansupp_id) AS sync_id_api,
    6 AS sync_type,
    penerimaansupp_r.no_penerimaan AS name,
    penerimaansupp_r.no_penerimaan AS title,
    false AS is_consignment,
    concat('SUP', penerimaansupp_r.supplier_id) AS partner_id,
    supplier_m.supplier_kode AS vendor_ref,
    penerimaansupp_r.tgl_penerimaan AS date_order,
    true AS no_approval,
    penerimaansupp_r.tgl_verifikasi AS date_planned,
    'draft'::text AS state,
    penerimaansupp_r.tgl_penerimaan AS podate,
    penerimaansupp_r.id,
    penerimaansupp_r.is_sent,
    penerimaansupp_r.is_sending,
    'POS'::text AS tipe_rekap
   FROM (penerimaansupp_r
     JOIN supplier_m ON ((penerimaansupp_r.supplier_id = supplier_m.supplier_id)))
  WHERE ((penerimaansupp_r.is_deleted = false) AND (penerimaansupp_r.is_verifikasi = true))
UNION ALL
 SELECT concat('POM', penerimaanobat_r.penerimaanobat_id) AS sync_id_api,
    6 AS sync_type,
    penerimaanobat_r.no_penerimaan AS name,
    penerimaanobat_r.no_penerimaan AS title,
    false AS is_consignment,
    concat('SUP', penerimaanobat_r.supplier_id) AS partner_id,
    supplier_m.supplier_kode AS vendor_ref,
    penerimaanobat_r.tgl_penerimaan AS date_order,
    true AS no_approval,
    penerimaanobat_r.tgl_penerimaan AS date_planned,
    'draft'::text AS state,
    penerimaanobat_r.tgl_penerimaan AS podate,
    penerimaanobat_r.id,
    penerimaanobat_r.is_sent,
    penerimaanobat_r.is_sending,
    'POM'::text AS tipe_rekap
   FROM (penerimaanobat_r
     JOIN supplier_m ON ((penerimaanobat_r.supplier_id = supplier_m.supplier_id)))
  WHERE (penerimaanobat_r.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_purchase_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_uangmuka_v\" AS  SELECT concat('UM', bayaruangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    bayaruangmuka_r.pendaftaran_id AS admission_id,
    bayaruangmuka_r.no_uangmuka AS trans_no,
    bayaruangmuka_r.tgl_uangmuka AS trans_date,
    'Deposit Collect'::text AS trans_type,
    bayaruangmuka_r.no_uangmuka AS reference_no,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pasien_m.nama_pasien AS patient_name,
        CASE
            WHEN (bayaruangmuka_r.metode_pembayaran = 27) THEN 'Cash'::text
            WHEN (bayaruangmuka_r.metode_pembayaran = 28) THEN 'DebitCard'::text
            ELSE '-'::text
        END AS payment_name,
    bayaruangmuka_r.tgl_uangmuka AS tglproses,
    tandabuktibayar_t.no_rek AS edc_machine,
    bayaruangmuka_r.jumlah_uangmuka AS amount,
    bayaruangmuka_r.keterangan_uangmuka AS note,
    'draft'::text AS state,
    6 AS sync_type,
    bayaruangmuka_r.id,
    bayaruangmuka_r.is_sent,
    bayaruangmuka_r.is_sending,
    'UANG_MUKA'::text AS tipe_rekap
   FROM (((((bayaruangmuka_r
     JOIN loginpemakai_k ON ((bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN tandabuktibayar_t ON ((bayaruangmuka_r.bayaruangmuka_id = tandabuktibayar_t.bayaruangmuka_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE (bayaruangmuka_r.is_deleted = false)
UNION ALL
 SELECT concat('PUM', pengembalianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    pengembalianuangmuka_r.pendaftaran_id AS admission_id,
    tandabuktikeluar_t.no_buktikeluar AS trans_no,
    pengembalianuangmuka_r.tgl_pengembalian AS trans_date,
    pengembalianuangmuka_r.keterangan AS trans_type,
    tandabuktikeluar_t.no_buktikeluar AS reference_no,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pasien_m.nama_pasien AS patient_name,
        CASE
            WHEN (tandabuktikeluar_t.is_tunai IS TRUE) THEN 'Cash'::text
            WHEN (tandabuktikeluar_t.is_tunai IS FALSE) THEN 'DebitCard'::text
            ELSE '-'::text
        END AS payment_name,
    pengembalianuangmuka_r.tgl_pengembalian AS tglproses,
    tandabuktikeluar_t.no_rek AS edc_machine,
    pengembalianuangmuka_r.total_pengembalian AS amount,
    '-'::text AS note,
    'draft'::text AS state,
    6 AS sync_type,
    pengembalianuangmuka_r.id,
    pengembalianuangmuka_r.is_sent,
    pengembalianuangmuka_r.is_sending,
    'PENGEMBALIAN_UANGMUKA'::text AS tipe_rekap
   FROM (((((pengembalianuangmuka_r
     JOIN loginpemakai_k ON ((pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN tandabuktikeluar_t ON ((pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id)))
     JOIN pendaftaran_t ON ((pengembalianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
UNION ALL
 SELECT concat('PKUM', pemakaianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
    pemakaianuangmuka_r.pendaftaran_id AS admission_id,
    pembayaranpelayanan_t.no_pembayaran AS trans_no,
    pemakaianuangmuka_r.tgl_pemakaian AS trans_date,
    pemakaianuangmuka_r.keterangan AS trans_type,
    pembayaranpelayanan_t.no_pembayaran AS reference_no,
    pendaftaran_t.no_pendaftaran AS admission_no,
    pasien_m.nama_pasien AS patient_name,
    'Cash'::text AS payment_name,
    pemakaianuangmuka_r.tgl_proses AS tglproses,
    '-'::character varying AS edc_machine,
    pemakaianuangmuka_r.pemakaian_uangmuka AS amount,
    '-'::text AS note,
    'draft'::text AS state,
    6 AS sync_type,
    pemakaianuangmuka_r.id,
    pemakaianuangmuka_r.is_sent,
    pemakaianuangmuka_r.is_sending,
    'PEMAKAIAN_UANGMUKA'::text AS tipe_rekap
   FROM (((((pemakaianuangmuka_r
     JOIN loginpemakai_k ON ((pemakaianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     JOIN pembayaranpelayanan_t ON ((pemakaianuangmuka_r.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pendaftaran_t ON ((pemakaianuangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)));");

    $this->execute('ALTER TABLE "public"."int_uangmuka_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_stockout_v\" AS  SELECT concat('PBHP', int_pendaftaranbmhp_r.pendaftaran_id) AS sync_id_api,
    int_pendaftaranbmhp_r.no_pendaftaran AS name,
    (int_pendaftaranbmhp_r.pasien_id)::character varying AS partner_id,
    obatalkespasien_t.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS picking_type_id,
    obatalkespasien_t.tglpelayanan AS date_move,
    obatalkespasien_t.tglpelayanan AS min_date,
    6 AS sync_type,
    int_pendaftaranbmhp_r.is_sent,
    int_pendaftaranbmhp_r.is_sending,
    int_pendaftaranbmhp_r.sync_respon,
    int_pendaftaranbmhp_r.id,
    'BMHP'::text AS tipe_rekap
   FROM (int_pendaftaranbmhp_r
     JOIN ( SELECT obatalkespasien_t_1.pendaftaran_id,
            obatalkespasien_t_1.ruangan_id,
            obatalkespasien_t_1.tglpelayanan
           FROM obatalkespasien_t obatalkespasien_t_1
          WHERE ((obatalkespasien_t_1.is_deleted = false) AND (obatalkespasien_t_1.status_bmhp = 680))
          GROUP BY obatalkespasien_t_1.pendaftaran_id, obatalkespasien_t_1.ruangan_id, obatalkespasien_t_1.tglpelayanan) obatalkespasien_t ON ((int_pendaftaranbmhp_r.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
  WHERE (int_pendaftaranbmhp_r.is_deleted = false)
UNION ALL
 SELECT concat('PRSP', int_penjualanresep_r.penjualanresep_id) AS sync_id_api,
        CASE
            WHEN (int_penjualanresep_r.nama_pembeli IS NOT NULL) THEN (concat(int_penjualanresep_r.noresep, '-', int_penjualanresep_r.nama_pembeli))::character varying
            ELSE int_penjualanresep_r.noresep
        END AS name,
        CASE
            WHEN (int_penjualanresep_r.pasien_id IS NOT NULL) THEN (int_penjualanresep_r.pasien_id)::character varying
            WHEN (int_penjualanresep_r.karyawan_id IS NOT NULL) THEN (concat('PEG', int_penjualanresep_r.karyawan_id))::character varying
            ELSE NULL::character varying
        END AS partner_id,
    int_penjualanresep_r.ruangan_id AS location_id,
    'stockout'::text AS dest_location_id,
    'stockout'::text AS picking_type_id,
    int_penjualanresep_r.tglresep AS date_move,
    int_penjualanresep_r.tglresep AS min_date,
    6 AS sync_type,
    int_penjualanresep_r.is_sent,
    int_penjualanresep_r.is_sending,
    int_penjualanresep_r.sync_respon,
    int_penjualanresep_r.id,
    'RESEP'::text AS tipe_rekap
   FROM (int_penjualanresep_r
     JOIN ( SELECT penjualanresep_t.penjualanresep_id
           FROM penjualanresep_t
          WHERE (penjualanresep_t.status_reseptur = 660)) penjualan_resep ON ((int_penjualanresep_r.penjualanresep_id = penjualan_resep.penjualanresep_id)))
  WHERE (int_penjualanresep_r.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_stockout_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"saleorder_v\" AS  SELECT pendaftaran_r.id,
    pendaftaran_r.pendaftaran_id AS sync_id_api,
    pendaftaran_r.no_pendaftaran AS name,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, '-'::character varying) AS billno,
        CASE
            WHEN (pembayaranpelayanan_t.pembayaranpelayanan_id IS NULL) THEN pendaftaran_r.tgl_pendaftaran
            ELSE pembayaranpelayanan_t.tgl_pembayaran
        END AS confirmation_date,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.tgl_pendaftaran AS date_order,
        CASE
            WHEN (pendaftaran_r.instalasi_id = 1) THEN 1
            WHEN (pendaftaran_r.instalasi_id = 3) THEN 2
            WHEN (pendaftaran_r.instalasi_id = 2) THEN 3
            WHEN (pendaftaran_r.instalasi_id = 6) THEN 6
            WHEN (pendaftaran_r.instalasi_id = 21) THEN 5
            ELSE 4
        END AS patient_type,
        CASE
            WHEN (pendaftaran_r.pasienadmisi_id IS NULL) THEN COALESCE(pendaftaran_r.penjamin_id, 0)
            ELSE COALESCE(pasienadmisi_r.penjamin_id, 0)
        END AS payer_id,
        CASE
            WHEN (pendaftaran_r.pasienadmisi_id IS NULL) THEN COALESCE(p1.s_kode, '-'::character varying)
            ELSE COALESCE(p2.s_kode, '-'::character varying)
        END AS payer_code,
        CASE
            WHEN (pendaftaran_r.pasienadmisi_id IS NULL) THEN COALESCE(fgetnamalookup(cb1.groupcarabayar_id), '-'::character varying)
            ELSE COALESCE(fgetnamalookup(cb2.groupcarabayar_id), '-'::character varying)
        END AS payer_type,
    6 AS sync_type,
        CASE
            WHEN ((pendaftaran_r.keterangan)::text = 'UPDATE'::text) THEN 'done'::text
            ELSE 'draft'::text
        END AS state,
    pendaftaran_r.keterangan,
    pendaftaran_r.is_sending,
    pendaftaran_r.is_sent,
        CASE
            WHEN (pendaftaran_r.asuransipasien_id IS NULL) THEN COALESCE(asuransipasien_m.namapemilikasuransi, '-'::character varying)
            ELSE COALESCE(asuransipasien_m.namapemilikasuransi, '-'::character varying)
        END AS nama_asuransi,
        CASE
            WHEN (pendaftaran_r.asuransipasien_id IS NULL) THEN COALESCE(asuransipasien_m.nokartuasuransi, '-'::character varying)
            ELSE COALESCE(asuransipasien_m.nokartuasuransi, '-'::character varying)
        END AS nomor_asuransi
   FROM (((((((pendaftaran_r
     LEFT JOIN pasienadmisi_r ON ((pendaftaran_r.pasienadmisi_id = pasienadmisi_r.pasienadmisi_id)))
     LEFT JOIN penjamin_m p1 ON ((pendaftaran_r.penjamin_id = p1.penjamin_id)))
     LEFT JOIN penjamin_m p2 ON ((pasienadmisi_r.penjamin_id = p2.penjamin_id)))
     LEFT JOIN carabayar_m cb1 ON ((p1.carabayar_id = cb1.carabayar_id)))
     LEFT JOIN carabayar_m cb2 ON ((p2.carabayar_id = cb2.carabayar_id)))
     LEFT JOIN asuransipasien_m ON ((pendaftaran_r.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
     LEFT JOIN pembayaranpelayanan_t ON (((pendaftaran_r.pendaftaran_id = pembayaranpelayanan_t.pembayaranpelayanan_id) AND (pembayaranpelayanan_t.is_deleted = false))));");

    $this->execute('ALTER TABLE "public"."saleorder_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_supplier_v\" AS  SELECT concat('SUP', supplier_r.supplier_id) AS sync_id_api,
    supplier_r.supplier_kode AS vendor_code,
    supplier_r.supplier_nama AS name,
    supplier_r.supplier_nama AS display_name,
    'CORPORATE'::text AS customer_type_api,
    supplier_r.no_tlp AS contact_person,
    supplier_r.no_tlp AS phone,
    supplier_r.no_tlp AS mobile,
    supplier_r.no_fax AS fax,
    supplier_r.email,
    supplier_r.website,
    supplier_r.supplier_alamat AS street,
    supplier_r.supplier_alamat AS street2,
    supplier_r.supplier_alamat AS street3,
    propinsi_m.propinsi_nama AS city,
    NULL::text AS zip,
    supplier_r.credit_limit AS credit_days,
    NULL::text AS opdiscountid,
    NULL::text AS ipdiscountid,
    NULL::text AS astaxid,
    false AS wipro_block,
    true AS insruance,
    true AS customer,
    supplier_r.is_active AS active,
    6 AS sync_type,
    supplier_r.keterangan_rekap,
    supplier_r.id,
    supplier_r.is_sent,
    supplier_r.is_sending
   FROM (supplier_r
     LEFT JOIN propinsi_m ON ((supplier_r.propinsi_id = propinsi_m.propinsi_id)));");

    $this->execute('ALTER TABLE "public"."int_supplier_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchasedetail_v\" AS  SELECT concat('POS', penerimaansuppdetail_r.id) AS sync_id_api,
    6 AS sync_type,
    concat('POS', penerimaansupp_r.penerimaansupp_id) AS order_id,
    penerimaansupp_r.no_penerimaan AS order_no,
    penerimaansupp_r.tgl_penerimaan AS order_date,
        CASE
            WHEN (\"left\"((obatalkes_m.obatalkes_kode)::text, 3) = 'CGN'::text) THEN true
            ELSE false
        END AS is_consignment,
    concat('OBT', penerimaansuppdetail_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    penerimaansuppdetail_r.satuankecil_id AS product_uom,
    satuan_kecil.satuanunit_nama AS uom_name,
    penerimaansuppdetail_r.no_batch AS batch_id,
    penerimaansuppdetail_r.qty_kecil AS product_qty,
    (penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) AS price_unit,
    penerimaansuppdetail_r.diskon AS discount_persen,
    ((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * ((penerimaansuppdetail_r.diskon / (100)::numeric))::double precision) AS discount_value,
    (((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * ((penerimaansuppdetail_r.diskon / (100)::numeric))::double precision) * (penerimaansuppdetail_r.qty_kecil)::double precision) AS discount_total,
    pajak_m.pajak_persen AS tax_persen,
    ((((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) - (((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * (penerimaansuppdetail_r.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) AS tax_value,
    (((((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) - (((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * (penerimaansuppdetail_r.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (penerimaansuppdetail_r.qty_kecil)::double precision) AS has_tax,
    (((((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) - (((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * (penerimaansuppdetail_r.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (penerimaansuppdetail_r.qty_kecil)::double precision) AS price_tax,
    ((((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * (penerimaansuppdetail_r.qty_kecil)::double precision) - ((((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * (penerimaansuppdetail_r.diskon)::double precision) / (100)::double precision) * (penerimaansuppdetail_r.qty_kecil)::double precision)) + (((((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) - (((penerimaansuppdetail_r.harga_netto / (penerimaansuppdetail_r.qty_kecil)::double precision) * (penerimaansuppdetail_r.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (penerimaansuppdetail_r.qty_kecil)::double precision)) AS price_subtotal,
    supplier_m.supplier_kode AS partner_ref,
    penerimaansuppdetail_r.tgl_kadaluarsa AS expired_date,
    penerimaansupp_r.tgl_penerimaan AS date_planned,
    penerimaansuppdetail_r.id,
    penerimaansuppdetail_r.is_sent,
    penerimaansuppdetail_r.is_sending,
    'POS'::text AS tipe_rekap
   FROM (((((penerimaansuppdetail_r
     JOIN penerimaansupp_r ON (((penerimaansuppdetail_r.penerimaansupp_id = penerimaansupp_r.penerimaansupp_id) AND (penerimaansupp_r.is_sent = true))))
     JOIN obatalkes_m ON ((penerimaansuppdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((penerimaansuppdetail_r.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN pajak_m ON ((penerimaansupp_r.pajak_id = pajak_m.pajak_id)))
     JOIN supplier_m ON ((penerimaansupp_r.supplier_id = supplier_m.supplier_id)))
UNION ALL
 SELECT concat('POM', penerimaanobatdetail_r.id) AS sync_id_api,
    6 AS sync_type,
    concat('POM', penerimaanobat_r.penerimaanobat_id) AS order_id,
    penerimaanobat_r.no_penerimaan AS order_no,
    penerimaanobat_r.tgl_penerimaan AS order_date,
    false AS is_consignment,
    concat('OBT', penerimaanobatdetail_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    satuan.satuankecil_id AS product_uom,
    satuan_kecil.satuanunit_nama AS uom_name,
    penerimaanobatdetail_r.no_batch AS batch_id,
    qty_konversi.qty_konversi AS product_qty,
    qty_konversi.harga_konversi AS price_unit,
    penerimaanobatdetail_r.discount AS discount_persen,
    ((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision) AS discount_value,
    (((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision) * qty_konversi.qty_konversi) AS discount_total,
    po.pajak_persen AS tax_persen,
    (((qty_konversi.harga_konversi - ((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) AS tax_value,
    ((((qty_konversi.harga_konversi - ((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * qty_konversi.qty_konversi) AS has_tax,
    ((((qty_konversi.harga_konversi - ((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * qty_konversi.qty_konversi) AS price_tax,
    (((qty_konversi.harga_konversi * qty_konversi.qty_konversi) - (((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision) * qty_konversi.qty_konversi)) + ((((qty_konversi.harga_konversi - ((qty_konversi.harga_konversi * penerimaanobatdetail_r.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * qty_konversi.qty_konversi)) AS price_subtotal,
    supplier_m.supplier_kode AS partner_ref,
    penerimaanobatdetail_r.tgl_kadaluarsa AS expired_date,
    penerimaanobat_r.tgl_penerimaan AS date_planned,
    penerimaanobatdetail_r.id,
    penerimaanobatdetail_r.is_sent,
    penerimaanobatdetail_r.is_sending,
    'POM'::text AS tipe_rekap
   FROM ((((((((penerimaanobatdetail_r
     JOIN penerimaanobat_r ON (((penerimaanobatdetail_r.penerimaanobat_id = penerimaanobat_r.penerimaanobat_id) AND (penerimaanobat_r.is_sent = true))))
     JOIN obatalkes_m ON ((penerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuankonversi_m ON ((penerimaanobatdetail_r.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
     LEFT JOIN satuanunit_m satuan_kecil ON ((satuankonversi_m.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN supplier_m ON ((penerimaanobat_r.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
            pajak_m.pajak_persen
           FROM (validasipoobat_t
             JOIN pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
          WHERE (validasipoobat_t.is_deleted = false)) po ON ((penerimaanobat_r.validasipoobat_id = po.validasipoobat_id)))
     LEFT JOIN ( SELECT satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.satuankecil_id,
            satuan_kecil_1.satuanunit_nama AS satuan_kecil
           FROM (satuankonversi_m satuankonversi_m_1
             JOIN satuanunit_m satuan_kecil_1 ON ((satuankonversi_m_1.satuankecil_id = satuan_kecil_1.satuanunit_id)))
          WHERE (satuankonversi_m_1.is_deleted = false)) satuan ON ((penerimaanobatdetail_r.s_konversiobt_id = satuan.satuankonversi_id)))
     LEFT JOIN ( SELECT penerimaanobatdetail_r_1.penerimaanobatdetail_id,
            ((penerimaanobatdetail_r_1.qty_diterima)::double precision * satuankonversi_m_1.nilai_konversi) AS qty_konversi,
            (penerimaanobatdetail_r_1.harga / satuankonversi_m_1.nilai_konversi) AS harga_konversi
           FROM (penerimaanobatdetail_r penerimaanobatdetail_r_1
             JOIN satuankonversi_m satuankonversi_m_1 ON ((penerimaanobatdetail_r_1.s_konversiobt_id = satuankonversi_m_1.satuankonversi_id)))
          WHERE (satuankonversi_m_1.is_deleted = false)) qty_konversi ON ((penerimaanobatdetail_r.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id)));");

    $this->execute('ALTER TABLE "public"."int_purchasedetail_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_pegawai_v\" AS  SELECT concat('PEG', pegawai_m.pegawai_id) AS sync_id_api,
    '-'::text AS parent_doctor_id,
    pegawai_m.nomorindukpegawai AS doctor_code,
    pegawai_m.nama_pegawai AS name,
    pegawai_m.nama_pegawai AS display_name,
    spesialis_m.spesialis_nama AS spec,
    '-'::text AS department,
    'internal'::text AS type_employment,
    'internal'::text AS type_doctor,
    '-'::text AS designation,
    kelompokpegawai_m.kelompokpegawai_nama AS hrprofile,
    fgetnamalookup((pegawai_m.jeniskelamin)::integer) AS gender,
    pegawai_m.notelp_pegawai AS phone,
    pegawai_m.nomobile_pegawai AS mobile,
    '-'::text AS fax,
    pegawai_m.alamatemail AS email,
    '-'::text AS website,
    pegawai_m.alamat_pegawai AS street,
    pegawai_m.alamat_pegawai AS street2,
    pegawai_m.alamat_pegawai AS street3,
    kabupaten_m.kabupaten_nama AS city,
    '-'::text AS zip,
    spesialis_m.spesialis_nama AS specialise_api_id,
    pegawai_m.is_active AS wipro_block,
    pegawai_m.is_active AS empblocked,
    'TRUE'::text AS doctor,
    pegawai_m.is_active AS active,
    6 AS sync_type
   FROM ((((pegawai_m
     LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
     LEFT JOIN kabupaten_m ON ((pegawai_m.kabupaten_id = kabupaten_m.kabupaten_id)))
     JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
     LEFT JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))
  WHERE (pegawai_m.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_pegawai_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_pasien_v\" AS  SELECT pasien_r.id,
    pasien_r.pasien_id AS sync_id_api,
    pasien_r.no_rekam_medik AS registration_code,
    pasien_r.nama_pasien AS name,
    pasien_r.nama_pasien AS display_name,
    lower((fgetnamalookup((pasien_r.namadepan)::integer))::text) AS title_name,
    COALESCE(pasien_r.tanggal_lahir, '1000-01-01'::date) AS date_of_birth,
    fgetvaluelookup((pasien_r.jeniskelamin)::integer) AS gender,
    COALESCE(pasien_r.no_telepon_pasien, '-'::character varying) AS phone,
    COALESCE(pasien_r.no_mobile_pasien, '-'::character varying) AS mobile,
    '-'::text AS fax,
    COALESCE(pasien_r.alamatemail, '-'::character varying) AS email,
    COALESCE(pasien_r.alamat_sekarang, '-'::text) AS street,
    '-'::text AS street2,
    '-'::text AS street3,
    COALESCE(pasien_r.alamat_pasien, '-'::text) AS city,
    '-'::text AS zip,
    '-'::text AS passport,
    COALESCE(pasien_r.no_identitas_pasien, '-'::character varying) AS ktp,
    false AS wipro_block,
    true AS patient,
    pasien_r.is_active AS active,
    6 AS sync_type,
    pasien_r.keterangan,
    pasien_r.tgl_proses,
    pasien_r.is_sent,
    pasien_r.is_sending
   FROM pasien_r;");

    $this->execute('ALTER TABLE "public"."int_pasien_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_stockscrap_v\" AS  SELECT 'adj_keluar'::text AS tipe_rekap,
    concat('AJK', adjusmenobatkeluar_r.adjusmenobatkeluar_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    (to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text))::date AS transaction_date,
    'AI'::text AS trans_type,
    (adjusmenobat_t.ruangan_adjusmen_id)::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    jenisobatalkes_m.servicecategory_id AS categ_id,
    concat('OBT', adjusmenobatkeluar_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatkeluar_r.satuankecil_id AS product_uom_id,
    adjusmenobatkeluar_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    ((adjusmenobatkeluar_r.qty_konversi)::double precision * obatalkes_m.harganetto) AS cost_total,
    'done'::text AS state,
    adjusmenobatkeluar_r.id,
    adjusmenobatkeluar_r.is_sending,
    adjusmenobatkeluar_r.is_sent,
    adjusmenobatkeluar_r.sync_respon
   FROM ((((adjusmenobatkeluar_r
     JOIN adjusmenobat_t ON ((adjusmenobatkeluar_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))
     JOIN obatalkes_m ON ((adjusmenobatkeluar_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN ( SELECT stokobatalkes_t.adjusmenobatkeluar_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY stokobatalkes_t.adjusmenobatkeluar_id, stokobatalkes_t.nobatch) stok ON ((adjusmenobatkeluar_r.adjusmenobatkeluar_id = stok.adjusmenobatkeluar_id)))
UNION ALL
 SELECT 'adj_masuk'::text AS tipe_rekap,
    concat('AJM', adjusmenobatmasuk_r.adjusmenobatmasuk_id) AS sync_id_api,
    6 AS sync_type,
    adjusmenobat_t.no_adjusmen AS origin,
    NULL::text AS admission_id,
    adjusmenobat_t.tgl_adjusmen AS transaction_datetime,
    (to_char(adjusmenobat_t.tgl_adjusmen, 'YYYY-MM-DD'::text))::date AS transaction_date,
    'AR'::text AS trans_type,
    'scrap'::character varying AS location_id,
    (adjusmenobat_t.ruangan_adjusmen_id)::character varying AS scrap_location_id,
    stok.nobatch AS lot_id,
    jenisobatalkes_m.servicecategory_id AS categ_id,
    concat('OBT', adjusmenobatmasuk_r.obatalkes_id) AS product_id,
    concat(adjusmenobat_t.no_adjusmen, '-', obatalkes_m.obatalkes_nama) AS name,
    adjusmenobatmasuk_r.satuankecil_id AS product_uom_id,
    adjusmenobatmasuk_r.qty_konversi AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    ((adjusmenobatmasuk_r.qty_konversi)::double precision * obatalkes_m.harganetto) AS cost_total,
    'done'::text AS state,
    adjusmenobatmasuk_r.id,
    adjusmenobatmasuk_r.is_sending,
    adjusmenobatmasuk_r.is_sent,
    adjusmenobatmasuk_r.sync_respon
   FROM ((((adjusmenobatmasuk_r
     JOIN adjusmenobat_t ON ((adjusmenobatmasuk_r.adjusmenobat_id = adjusmenobat_t.adjusmenobat_id)))
     JOIN obatalkes_m ON ((adjusmenobatmasuk_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN ( SELECT stokobatalkes_t.adjusmenobatmasuk_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY stokobatalkes_t.adjusmenobatmasuk_id, stokobatalkes_t.nobatch) stok ON ((adjusmenobatmasuk_r.adjusmenobatmasuk_id = stok.adjusmenobatmasuk_id)))
UNION ALL
 SELECT 'pemusnahan_obat'::text AS tipe_rekap,
    concat('PMO', pemusnahanobatdetail_r.pemusnahanobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemusnahanobat_t.nopemusnahan AS origin,
    NULL::text AS admission_id,
    pemusnahanobat_t.tglpemusnahan AS transaction_datetime,
    (to_char(pemusnahanobat_t.tglpemusnahan, 'YYYY-MM-DD'::text))::date AS transaction_date,
    'BR'::text AS trans_type,
    (pemusnahanobat_t.ruangan_id)::character varying AS location_id,
    'scrap'::character varying AS scrap_location_id,
    pemusnahanobatdetail_r.nobatch AS lot_id,
    jenisobatalkes_m.servicecategory_id AS categ_id,
    concat('OBT', pemusnahanobatdetail_r.obatalkes_id) AS product_id,
    concat(pemusnahanobat_t.nopemusnahan, '-', obatalkes_m.obatalkes_nama) AS name,
    pemusnahanobatdetail_r.satuan_id AS product_uom_id,
    pemusnahanobatdetail_r.jumlah AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    (pemusnahanobatdetail_r.jumlah * obatalkes_m.harganetto) AS cost_total,
    'done'::text AS state,
    pemusnahanobatdetail_r.id,
    pemusnahanobatdetail_r.is_sending,
    pemusnahanobatdetail_r.is_sent,
    pemusnahanobatdetail_r.sync_respon
   FROM ((((pemusnahanobatdetail_r
     JOIN pemusnahanobat_t ON ((pemusnahanobatdetail_r.pemusnahanobat_id = pemusnahanobat_t.pemusnahanobat_id)))
     JOIN obatalkes_m ON ((pemusnahanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN ( SELECT stokobatalkes_t.pemusnahanobatdetail_id
           FROM stokobatalkes_t
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY stokobatalkes_t.pemusnahanobatdetail_id) stok ON ((pemusnahanobatdetail_r.pemusnahanobatdetail_id = stok.pemusnahanobatdetail_id)))
UNION ALL
 SELECT 'pemakaian_obat'::text AS tipe_rekap,
    concat('PKO', pemakaianobatdetail_r.pemakaianobatdetail_id) AS sync_id_api,
    6 AS sync_type,
    pemakaianobat_t.nopemakaian_obat AS origin,
    NULL::text AS admission_id,
    pemakaianobat_t.tglpemakaianobat AS transaction_datetime,
    (to_char((pemakaianobat_t.tglpemakaianobat)::timestamp with time zone, 'YYYY-MM-DD'::text))::date AS transaction_date,
    'SC'::text AS trans_type,
    (pemakaianobat_t.ruangan_id)::character varying AS location_id,
    'scrap'::text AS scrap_location_id,
    stok.nobatch AS lot_id,
    jenisobatalkes_m.servicecategory_id AS categ_id,
    concat('OBT', pemakaianobatdetail_r.obatalkes_id) AS product_id,
    concat(pemakaianobat_t.nopemakaian_obat, '-', obatalkes_m.obatalkes_nama) AS name,
    pemakaianobatdetail_r.satuankecil_id AS product_uom_id,
    pemakaianobatdetail_r.qty_satuanpakai AS scrap_qty,
    obatalkes_m.harganetto AS cost,
    ((pemakaianobatdetail_r.qty_satuanpakai)::double precision * obatalkes_m.harganetto) AS cost_total,
    'done'::text AS state,
    pemakaianobatdetail_r.id,
    pemakaianobatdetail_r.is_sending,
    pemakaianobatdetail_r.is_sent,
    pemakaianobatdetail_r.sync_respon
   FROM ((((pemakaianobatdetail_r
     JOIN pemakaianobat_t ON ((pemakaianobatdetail_r.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id)))
     JOIN obatalkes_m ON ((pemakaianobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     JOIN ( SELECT stokobatalkes_t.pemakaianobatdetail_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY stokobatalkes_t.pemakaianobatdetail_id, stokobatalkes_t.nobatch) stok ON ((pemakaianobatdetail_r.pemakaianobatdetail_id = stok.pemakaianobatdetail_id)));");

    $this->execute('ALTER TABLE "public"."int_stockscrap_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"saleorder_line_v\" AS  SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('TND', tindakanpelayanan_r.daftartindakan_id) AS product_id,
    daftartindakan_m.daftartindakan_nama AS name,
    351 AS product_uom,
    tindakanpelayanan_r.qty_tindakan AS product_uom_qty,
    ((tindakanpelayanan_r.tarif_satuan + COALESCE(tindakanpelayanan_r.tarifcyto_tindakan, (0)::double precision)) + COALESCE(tindakanpelayanan_r.tarifpenyulit_tindakan, (0)::double precision)) AS price_unit,
    tindakanpelayanan_r.tarif_tindakan AS price_subtotal,
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dibayarkan
            ELSE (- tindakanpelayanan_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dijamin
            ELSE (- tindakanpelayanan_r.tarif_dijamin)
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id AS order_id,
    (daftartindakan_m.servicecategory_id)::character(1) AS service_categ_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS primary_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
        CASE
            WHEN ((kelompoktindakan_m.kelompoktindakan_namalainnya)::text = 'LOS'::text) THEN 'LOS'::text
            ELSE 'LOB'::text
        END AS revenue_type,
    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
        CASE
            WHEN (tindakanpelayanan_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE '-'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN (tindakanpelayanan_r.instalasi_id = 1) THEN 'OUTPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 3) THEN 'INPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 4) THEN 'LABORATORY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 5) THEN 'RADIOLOGY'::text
            ELSE '-'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.s_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    tindakanpelayanan_r.no_tindakanpelayanan AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'TINDAKAN'::text AS jenis,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'bill'::text
        END AS status_bill,
    NULL::json AS additional_paket
   FROM ((((((((((((((((tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang
           FROM (((((pendaftaran_r pendaftaran_r_1
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (((pendaftaran_r_1.keterangan)::text = 'INSERT'::text) AND (pendaftaran_r_1.is_sent = true))) pendaftaran_r ON ((tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN daftartindakan_m ON ((tindakanpelayanan_r.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN servicegroup_m ON ((daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id)))
     LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN penjamin_m ON ((tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_r_1.pendaftaran_id,
            sum(tindakanpelayanan_r_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tindakanpelayanan_r_1
          WHERE (tindakanpelayanan_r_1.is_deleted = false)
          GROUP BY tindakanpelayanan_r_1.pendaftaran_id) total_tagihan ON ((tindakanpelayanan_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_r.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON ((tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id)))
UNION ALL
 SELECT 6 AS sync_type,
    tindakanpelayanan_r.tgl_proses AS tglproses,
    concat('TND', tindakanpelayanan_r.id) AS sync_id_api,
    concat('PKT', tindakanpelayanan_r.tipepaket_id) AS product_id,
    tipepaket_m.tipepaket_nama AS name,
    351 AS product_uom,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'ACCRUAL REVERSAL'::text) THEN (- tindakanpelayanan_r.qty_tindakan)
            ELSE tindakanpelayanan_r.qty_tindakan
        END AS product_uom_qty,
    0 AS price_unit,
    0 AS price_subtotal,
    0 AS price_total,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dibayarkan
            ELSE (- tindakanpelayanan_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = 'BILLING'::text) THEN tindakanpelayanan_r.tarif_dijamin
            ELSE (- tindakanpelayanan_r.tarif_dijamin)
        END AS payer_amount,
    tindakanpelayanan_r.pendaftaran_id AS order_id,
    'OP/IP PACKAGE'::bpchar AS service_categ_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS primary_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS prescribe_doc_id,
    concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id) AS perform_doc_id,
    ruangan_m.ruangan_id AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    COALESCE(pembayaranpelayanan_t.no_pembayaran, no_pembayaran.no_pembayaran) AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    tindakanpelayanan_r.keterangan AS type_line,
    'LOB'::text AS revenue_type,
    '-'::character varying AS item_specialisation,
        CASE
            WHEN (tindakanpelayanan_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE '-'::text
        END AS patient_group,
    '-'::text AS special_group,
        CASE
            WHEN (tindakanpelayanan_r.instalasi_id = 1) THEN 'OUTPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 3) THEN 'INPATIENT'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 21) THEN 'MCU'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 4) THEN 'LABORATORY'::text
            WHEN (tindakanpelayanan_r.instalasi_id = 5) THEN 'RADIOLOGY'::text
            ELSE '-'::text
        END AS special_group2,
    'OTHERS'::text AS service_group,
    kelaspelayanan_m.kelaspelayanan_nama AS bed_type,
    concat('PEN', tindakanpelayanan_r.penjamin_id) AS payer,
    penjamin_m.s_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    tindakanpelayanan_r.no_tindakanpelayanan AS order_no,
    tindakanpelayanan_r.tgl_tindakan AS order_date,
    true AS is_package,
    tipepaket_m.tipepaket_nama AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS account_analytic_id,
        CASE
            WHEN (tindakanpelayanan_r.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', tindakanpelayanan_r.dokterpenanggungjawab_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    tindakanpelayanan_r.is_sent,
    tindakanpelayanan_r.is_sending,
    tindakanpelayanan_r.id,
    'PAKET'::text AS jenis,
        CASE
            WHEN ((tindakanpelayanan_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'bill'::text
        END AS status_bill,
    ( SELECT array_to_json(array_agg(row_to_json(detail_paket.*))) AS array_to_json
           FROM ( SELECT 6 AS sync_type,
                    tp_paket.tgl_proses AS tglproses,
                    concat('TND', tp_paket.id, '-', concat('TND', paketpelayanan_mp.daftartindakan_id)) AS sync_id_api,
                    concat('TND', paketpelayanan_mp.daftartindakan_id) AS product_id,
                    daftartindakan_m.daftartindakan_nama AS name,
                    351 AS product_uom,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = 'ACCRUAL REVERSAL'::text) THEN (- tp_paket.qty_tindakan)
                            WHEN ((tp_paket.keterangan)::text = 'BILLING CANCEL'::text) THEN (- tp_paket.qty_tindakan)
                            ELSE tp_paket.qty_tindakan
                        END AS product_uom_qty,
                    ((paket_detail.harga_satuan + paket_detail.harga_cyto) + paket_detail.harga_penyulit) AS price_unit,
                    paket_detail.harga_total AS price_total,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = 'BILLING'::text) THEN tp_paket.tarif_dibayarkan
                            ELSE (- tp_paket.tarif_dibayarkan)
                        END AS personal_amount,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = 'BILLING'::text) THEN tp_paket.tarif_dijamin
                            ELSE (- tp_paket.tarif_dijamin)
                        END AS payer_amount,
                    tp_paket.pendaftaran_id AS order_id,
                    daftartindakan_m.servicecategory_id AS service_categ_id,
                    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS primary_doc_id,
                    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS prescribe_doc_id,
                    concat('PEG', tp_paket.dokterpenanggungjawab_id) AS perform_doc_id,
                    ruangan_m_1.ruangan_id AS location_id,
                    ruangan_m_1.ruangan_nama AS department_id,
                    COALESCE(pembayaranpelayanan_t_1.no_pembayaran, no_pembayaran_1.no_pembayaran) AS billno,
                    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
                    tp_paket.keterangan AS type_line,
                        CASE
                            WHEN ((kelompoktindakan_m.kelompoktindakan_namalainnya)::text = 'LOS'::text) THEN 'LOS'::text
                            ELSE 'LOB'::text
                        END AS revenue_type,
                    kategoritindakan_m.kategoritindakan_nama AS item_specialisation,
                        CASE
                            WHEN (tp_paket.instalasi_id = 1) THEN 'OPD'::text
                            WHEN (tp_paket.instalasi_id = 2) THEN 'EMERGENCY'::text
                            WHEN (tp_paket.instalasi_id = 3) THEN 'IPD'::text
                            WHEN (tp_paket.instalasi_id = 21) THEN 'MCU'::text
                            ELSE '-'::text
                        END AS patient_group,
                    '-'::text AS special_group,
                        CASE
                            WHEN (tp_paket.instalasi_id = 1) THEN 'OUTPATIENT'::text
                            WHEN (tp_paket.instalasi_id = 2) THEN 'EMERGENCY'::text
                            WHEN (tp_paket.instalasi_id = 3) THEN 'INPATIENT'::text
                            WHEN (tp_paket.instalasi_id = 21) THEN 'MCU'::text
                            WHEN (tp_paket.instalasi_id = 4) THEN 'LABORATORY'::text
                            WHEN (tp_paket.instalasi_id = 5) THEN 'RADIOLOGY'::text
                            ELSE '-'::text
                        END AS special_group2,
                    servicegroup_m.servicegroup_nama AS service_group,
                    kelaspelayanan_m_1.kelaspelayanan_nama AS bed_type,
                    concat('PEN', tp_paket.penjamin_id) AS payer,
                    penjamin_m_1.s_kode AS payer_code,
                    carabayar_m_1.carabayar_nama AS payer_type,
                    penjamin_m_1.penjamin_nama AS payer_name,
                    tp_paket.no_tindakanpelayanan AS order_no,
                    tp_paket.tgl_tindakan AS order_date,
                    true AS is_package,
                    tipepaket_m_1.tipepaket_nama AS package_name,
                    NULL::text AS cost_unit,
                    NULL::text AS cost_total,
                        CASE
                            WHEN (tp_paket.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r_1.pegawai_id)
                            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
                        END AS account_analytic_id,
                        CASE
                            WHEN (tp_paket.dokterpenanggungjawab_id IS NULL) THEN concat('PEG', pendaftaran_r_1.pegawai_id)
                            ELSE concat('PEG', tp_paket.dokterpenanggungjawab_id)
                        END AS backup_analytic_id,
                    pendaftaran_r_1.kota,
                    pendaftaran_r_1.kecamatan,
                    pendaftaran_r_1.kelurahan,
                    pendaftaran_r_1.pasien_id AS partner_id,
                    pendaftaran_r_1.no_rekam_medik AS registration_code,
                    pendaftaran_r_1.no_pendaftaran AS number_admission,
                    '-'::text AS manufacture,
                    pendaftaran_r_1.tglpasienpulang AS discharge_date,
                    pegawai_1.spesialis_nama AS specialization_primary,
                    tp_paket.is_sent,
                    tp_paket.is_sending,
                    tp_paket.id,
                    'PAKET_DETAIL'::text AS jenis,
                        CASE
                            WHEN ((tp_paket.keterangan)::text = ANY (ARRAY[('ACCRUAL REVERSAL'::character varying)::text, ('ACCRUAL'::character varying)::text])) THEN 'draft'::text
                            ELSE 'bill'::text
                        END AS status_bill
                   FROM (((((((((((((((((((tindakanpelayanan_r tp_paket
                     JOIN ( SELECT pen_det.pendaftaran_id,
                            pen_det.pegawai_id,
                            pen_det.pasien_id,
                            pasien_m.no_rekam_medik,
                            pen_det.no_pendaftaran,
                            kabupaten_m.kabupaten_nama AS kota,
                            kecamatan_m.kecamatan_nama AS kecamatan,
                            kelurahan_m.kelurahan_nama AS kelurahan,
                            pasienpulang_t.tglpasienpulang
                           FROM (((((pendaftaran_r pen_det
                             JOIN pasien_m ON ((pen_det.pasien_id = pasien_m.pasien_id)))
                             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
                             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
                             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
                             LEFT JOIN pasienpulang_t ON ((pen_det.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
                          WHERE (((pen_det.keterangan)::text = 'INSERT'::text) AND (pen_det.is_sent = true))) pendaftaran_r_1 ON ((tp_paket.pendaftaran_id = pendaftaran_r_1.pendaftaran_id)))
                     JOIN tipepaket_m tipepaket_m_1 ON ((tp_paket.tipepaket_id = tipepaket_m_1.tipepaket_id)))
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                     LEFT JOIN servicegroup_m ON ((daftartindakan_m.servicegroup_id = servicegroup_m.servicegroup_id)))
                     LEFT JOIN kategoritindakan_m ON ((daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id)))
                     JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
                     JOIN ruangan_m ruangan_m_1 ON ((tp_paket.ruangan_id = ruangan_m_1.ruangan_id)))
                     JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
                     JOIN penjamin_m penjamin_m_1 ON ((tp_paket.penjamin_id = penjamin_m_1.penjamin_id)))
                     JOIN carabayar_m carabayar_m_1 ON ((penjamin_m_1.carabayar_id = carabayar_m_1.carabayar_id)))
                     LEFT JOIN kelaspelayanan_m kelaspelayanan_m_1 ON ((tp_paket.kelaspelayanan_id = kelaspelayanan_m_1.kelaspelayanan_id)))
                     LEFT JOIN ( SELECT tp_paket_1.pendaftaran_id,
                            sum(tp_paket_1.tarif_tindakan) AS total_tagihan
                           FROM tindakanpelayanan_r tp_paket_1
                          WHERE (tp_paket_1.is_deleted = false)
                          GROUP BY tp_paket_1.pendaftaran_id) total_tagihan_1 ON ((tp_paket.pendaftaran_id = total_tagihan_1.pendaftaran_id)))
                     LEFT JOIN tindakansudahbayar_t tindakansudahbayar_t_1 ON ((tp_paket.tindakansudahbayar_id = tindakansudahbayar_t_1.tindakansudahbayar_id)))
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((tindakansudahbayar_t_1.pembayaranpelayanan_id = pembayaranpelayanan_t_1.pembayaranpelayanan_id)))
                     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
                            sum(pembayaran_t.total_dibayar) AS total_dibayar,
                            sum(pembayaran_t.total_dijamin) AS total_dijamin
                           FROM pembayaran_t
                          GROUP BY pembayaran_t.pendaftaran_id) pembayaran_1 ON ((tp_paket.pendaftaran_id = pembayaran_1.pendaftaran_id)))
                     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'daftartindakan_id'::text))::integer AS daftartindakan_id,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_satuan'::text))::double precision AS harga_satuan,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_cyto'::text))::double precision AS harga_cyto,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_penyulit'::text))::double precision AS harga_penyulit,
                            ((json_array_elements(((tindakanpelayanan_t.additional_data)::json -> 'detail_paket'::text)) ->> 'harga_total'::text))::double precision AS harga_total
                           FROM tindakanpelayanan_t) paket_detail ON (((tp_paket.tindakanpelayanan_id = paket_detail.tindakanpelayanan_id) AND (daftartindakan_m.daftartindakan_id = paket_detail.daftartindakan_id))))
                     LEFT JOIN ( SELECT pembayaranpelayanan_t_1_1.pembayaran_id,
                            pembayaranpelayanan_t_1_1.no_pembayaran
                           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1_1) no_pembayaran_1 ON ((tp_paket.pembayaran_id = no_pembayaran_1.pembayaran_id)))
                     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                            spesialis_m.spesialis_nama
                           FROM (pegawai_m
                             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai_1 ON ((tp_paket.dokterpenanggungjawab_id = pegawai_1.pegawai_id)))
                  WHERE (tp_paket.id = tindakanpelayanan_r.id)) detail_paket) AS additional_paket
   FROM (((((((((((((tindakanpelayanan_r
     JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang
           FROM (((((pendaftaran_r pendaftaran_r_1
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (((pendaftaran_r_1.keterangan)::text = 'INSERT'::text) AND (pendaftaran_r_1.is_sent = true))) pendaftaran_r ON ((tindakanpelayanan_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN tipepaket_m ON ((tindakanpelayanan_r.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN ruangan_m ON ((tindakanpelayanan_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN penjamin_m ON ((tindakanpelayanan_r.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN kelaspelayanan_m ON ((tindakanpelayanan_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_r_1.pendaftaran_id,
            sum(tindakanpelayanan_r_1.tarif_tindakan) AS total_tagihan
           FROM tindakanpelayanan_r tindakanpelayanan_r_1
          WHERE (tindakanpelayanan_r_1.is_deleted = false)
          GROUP BY tindakanpelayanan_r_1.pendaftaran_id) total_tagihan ON ((tindakanpelayanan_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_r.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((tindakanpelayanan_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN ( SELECT pembayaranpelayanan_t_1.pembayaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1) no_pembayaran ON ((tindakanpelayanan_r.pembayaran_id = no_pembayaran.pembayaran_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((tindakanpelayanan_r.dokterpenanggungjawab_id = pegawai.pegawai_id)));");

    $this->execute('ALTER TABLE "public"."saleorder_line_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_pembayaran_v\" AS  SELECT concat('BYR', pembayaran_r.id) AS sync_id_api,
        CASE
            WHEN ((pembayaran_r.keterangan)::text = ANY ((ARRAY['BILL CANCEL'::character varying, 'DISCOUNT CANCEL'::character varying])::text[])) THEN peg_deleted.nama_pegawai
            ELSE pegawai_m.nama_pegawai
        END AS user_name,
    fgetnamalookup(pembayaran_r.tipe_pembayaran) AS trans_type,
    concat(kasir.ruangan_nama, ' - ', pembayaranpelayanan_t.no_pembayaran) AS facility_name,
    pembayaran_r.tgl_proses AS tglproses,
    pembayaran_r.keterangan AS payment_name,
    pembayaran_r.nama_edc AS edc_machine,
        CASE
            WHEN (pembayaran_r.total_tunai <> (0)::double precision) THEN (pembayaran_r.total_tunai - pembayaran_r.total_kembalian)
            WHEN (pembayaran_r.total_nontunai <> (0)::double precision) THEN pembayaran_r.total_nontunai
            ELSE NULL::double precision
        END AS total_collect,
        CASE
            WHEN (pembayaranpelayanan_t.pendaftaran_id IS NULL) THEN penjualanresep_t.noresep
            ELSE pendaftaran_t.no_pendaftaran
        END AS note,
    pembayaran_r.pendaftaran_id AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pembayaran_r.keterangan,
    pembayaran_r.id,
    pembayaran_r.is_sent,
    pembayaran_r.is_sending,
    pembayaran_r.is_update,
    'PEMBAYARAN'::text AS tipe_rekap
   FROM (((((((((pembayaran_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1
          WHERE (((pendaftaran_r_1.keterangan)::text = 'INSERT'::text) AND (pendaftaran_r_1.is_sent = true))) pendaftaran_r ON ((pembayaran_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN pembayaranpelayanan_t ON ((pembayaran_r.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
     JOIN loginpemakai_k ON ((pembayaran_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN loginpemakai_k deleted_by ON ((pembayaran_r.deleted_by = deleted_by.loginpemakai_id)))
     LEFT JOIN pegawai_m peg_deleted ON ((deleted_by.pegawai_id = peg_deleted.pegawai_id)))
     LEFT JOIN ruangan_m kasir ON ((pembayaranpelayanan_t.ruangan_id = kasir.ruangan_id)))
     LEFT JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
UNION ALL
 SELECT concat('UM', bayaruangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN (bayaruangmuka_r.metode_pembayaran = 27) THEN 'CASH'::text
            ELSE 'BankTransfer'::text
        END AS trans_type,
    concat(ruangan_m.ruangan_nama, ' - ', bayaruangmuka_r.no_uangmuka) AS facility_name,
    bayaruangmuka_r.tgl_uangmuka AS tglproses,
    'DEPOSIT'::character varying AS payment_name,
    '-'::text AS edc_machine,
    bayaruangmuka_r.jumlah_uangmuka AS total_collect,
    pendaftaran_t.no_pendaftaran AS note,
    bayaruangmuka_r.pendaftaran_id AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    bayaruangmuka_r.keterangan,
    bayaruangmuka_r.id,
    bayaruangmuka_r.is_sent_scr AS is_sent,
    bayaruangmuka_r.is_sending_scr AS is_sending,
    NULL::boolean AS is_update,
    'UANG_MUKA'::text AS tipe_rekap
   FROM (((((bayaruangmuka_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1) pendaftaran_r ON ((bayaruangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN loginpemakai_k ON ((bayaruangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((bayaruangmuka_r.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pendaftaran_t ON ((bayaruangmuka_r.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
  WHERE (bayaruangmuka_r.is_deleted = false)
UNION ALL
 SELECT concat('PUM', pengembalianuangmuka_r.id) AS sync_id_api,
    pegawai_m.nama_pegawai AS user_name,
        CASE
            WHEN (tandabuktikeluar_t.is_tunai IS TRUE) THEN 'CASH'::text
            ELSE 'BankTransfer'::text
        END AS trans_type,
    concat(ruangan_m.ruangan_nama, ' - ', tandabuktikeluar_t.no_buktikeluar) AS facility_name,
    pengembalianuangmuka_r.tgl_pengembalian AS tglproses,
    'REFUND'::character varying AS payment_name,
    '-'::text AS edc_machine,
    pengembalianuangmuka_r.total_pengembalian AS total_collect,
    pendaftaran_r.no_pendaftaran AS note,
    pengembalianuangmuka_r.pendaftaran_id AS admission_id,
    pendaftaran_r.no_pendaftaran AS admission_no,
    'draft'::text AS state,
    6 AS sync_type,
    pengembalianuangmuka_r.keterangan,
    pengembalianuangmuka_r.id,
    pengembalianuangmuka_r.is_sent_scr AS is_sent,
    pengembalianuangmuka_r.is_sending_scr AS is_sending,
    NULL::boolean AS is_update,
    'PENGEMBALIAN_UANGMUKA'::text AS tipe_rekap
   FROM (((((pengembalianuangmuka_r
     LEFT JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.*::pendaftaran_r AS pendaftaran_r_1,
            pendaftaran_r_1.no_pendaftaran,
            pendaftaran_r_1.pegawai_id
           FROM pendaftaran_r pendaftaran_r_1
          WHERE ((pendaftaran_r_1.keterangan)::text = 'INSERT'::text)) pendaftaran_r ON ((pengembalianuangmuka_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN loginpemakai_k ON ((pengembalianuangmuka_r.created_by = loginpemakai_k.loginpemakai_id)))
     JOIN pegawai_m ON ((loginpemakai_k.pegawai_id = pegawai_m.pegawai_id)))
     JOIN tandabuktikeluar_t ON ((pengembalianuangmuka_r.pengembalianuangmuka_id = tandabuktikeluar_t.pengembalianuangmuka_id)))
     JOIN ruangan_m ON ((pengembalianuangmuka_r.ruangan_id = ruangan_m.ruangan_id)))
  WHERE (pengembalianuangmuka_r.is_deleted = false);");

    $this->execute('ALTER TABLE "public"."int_pembayaran_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_stockreturn_v\" AS  SELECT concat('RTR', returresep_r.returresep_id) AS sync_id_api,
    concat(penjualanresep_t.noresep, '-', returresep_r.no_returresep) AS name,
    (penjualanresep_t.pasien_id)::character varying AS partner_id,
    'return'::text AS location_id,
    returresep_r.ruangan_id AS dest_location_id,
    'return'::text AS picking_type_id,
    returresep_r.tgl_retur AS date_move,
    NULL::text AS min_date,
    6 AS sync_type,
    returresep_r.is_sent,
    returresep_r.is_sending,
    returresep_r.sync_respon,
    returresep_r.id,
    'RETUR_RESEP'::text AS tipe_rekap
   FROM (returresep_r
     JOIN penjualanresep_t ON ((returresep_r.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
UNION ALL
 SELECT concat('BTL', pembatalanresep_r.pembatalanresep_id) AS sync_id_api,
    concat(penjualanresep_t.noresep, '-', pembatalanresep_r.no_pembatalan) AS name,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN (penjualanresep_t.pasien_id)::character varying
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN (concat('PEG', penjualanresep_t.karyawan_id))::character varying
            ELSE penjualanresep_t.nama_pembeli
        END AS partner_id,
    'return'::text AS location_id,
    penjualanresep_t.ruangan_id AS dest_location_id,
    'return'::text AS picking_type_id,
    pembatalanresep_r.tgl_pembatalan AS date_move,
    NULL::text AS min_date,
    6 AS sync_type,
    pembatalanresep_r.is_sent,
    pembatalanresep_r.is_sending,
    pembatalanresep_r.sync_respon,
    pembatalanresep_r.id,
    'BATAL_RESEP'::text AS tipe_rekap
   FROM (pembatalanresep_r
     JOIN penjualanresep_t ON ((pembatalanresep_r.penjualanresep_id = penjualanresep_t.penjualanresep_id)));");

    $this->execute('ALTER TABLE "public"."int_stockreturn_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_stockoutdetail_v\" AS  SELECT concat('PRSP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PRSP', int_obatalkespasien_r.penjualanresep_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN (int_obatalkespasien_r.det_konversi = (0)::double precision) THEN int_obatalkespasien_r.det_konversi
            WHEN (int_obatalkespasien_r.det_konversi IS NULL) THEN int_obatalkespasien_r.qty_konversi
            ELSE int_obatalkespasien_r.det_konversi
        END AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobat.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon
   FROM (((((int_obatalkespasien_r
     JOIN int_penjualanresep_r ON (((int_obatalkespasien_r.penjualanresep_id = int_penjualanresep_r.penjualanresep_id) AND (int_penjualanresep_r.is_sent = true))))
     JOIN obatalkes_m ON ((int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN satuanunit_m ON ((int_obatalkespasien_r.satuankecil_id = satuanunit_m.satuanunit_id)))
     JOIN ( SELECT stokobatalkes_t.obatalkespasien_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY stokobatalkes_t.obatalkespasien_id, stokobatalkes_t.nobatch) stokobat ON ((int_obatalkespasien_r.obatalkespasien_id = stokobat.obatalkespasien_id)))
UNION ALL
 SELECT concat('PBHP', int_obatalkespasien_r.obatalkespasien_id) AS sync_id_api,
    concat('PBHP', int_obatalkespasien_r.pendaftaran_id) AS picking_id,
    concat('OBT', int_obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
    int_obatalkespasien_r.qty_oa AS product_uom_qty,
    int_obatalkespasien_r.satuankecil_id AS product_uom_id,
    int_obatalkespasien_r.satuankecil_id AS product_uom,
    stokobat.nobatch AS lot_id,
    6 AS sync_type,
    int_obatalkespasien_r.id,
    int_obatalkespasien_r.is_sent,
    int_obatalkespasien_r.is_sending,
    int_obatalkespasien_r.sync_respon
   FROM (((((int_obatalkespasien_r
     JOIN int_pendaftaranbmhp_r ON (((int_obatalkespasien_r.pendaftaran_id = int_pendaftaranbmhp_r.pendaftaran_id) AND (int_pendaftaranbmhp_r.is_sent = true))))
     JOIN obatalkes_m ON ((int_obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN satuanunit_m ON ((int_obatalkespasien_r.satuankecil_id = satuanunit_m.satuanunit_id)))
     JOIN ( SELECT stokobatalkes_t.obatalkespasien_id,
            stokobatalkes_t.nobatch
           FROM stokobatalkes_t
          WHERE (stokobatalkes_t.is_deleted = false)
          GROUP BY stokobatalkes_t.obatalkespasien_id, stokobatalkes_t.nobatch) stokobat ON ((int_obatalkespasien_r.obatalkespasien_id = stokobat.obatalkespasien_id)));");

    $this->execute('ALTER TABLE "public"."int_stockoutdetail_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchasegrndetail_v\" AS  SELECT concat('RPOS', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOS', returpenerimaanobatdetail_r.penerimaansuppdetail_id) AS picking_id,
    concat('RPOS', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaansupp_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    returpenerimaanobat_r.no_returpenerimaanobat AS order_no,
    obatalkes_m.obatalkes_nama AS name,
    obatalkes_m.harganetto AS normal_price,
    obatalkes_m.harganetto AS price_unit,
    NULL::text AS discount_value,
    NULL::text AS discount_persen,
    NULL::text AS product_uom,
    false AS is_conversion,
        CASE
            WHEN (\"left\"((obatalkes_m.obatalkes_kode)::text, 3) = 'CGN'::text) THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur AS product_qty,
    returpenerimaanobatdetail_r.qty_retur AS qty_received,
    NULL::text AS taxes_id,
    NULL::text AS price_tax,
    NULL::text AS has_tax,
    NULL::text AS price_total,
    NULL::text AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    returpenerimaanobat_r.tgl_retur AS order_date,
    13 AS currency_id,
    'draft'::text AS state,
    6 AS sync_type,
    returpenerimaanobatdetail_r.id,
    returpenerimaanobatdetail_r.is_sent,
    returpenerimaanobatdetail_r.is_sending
   FROM (((returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON (((returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id) AND (returpenerimaanobat_r.is_sent = true))))
     JOIN penerimaansupp_t ON ((returpenerimaanobat_r.panerimaanobatsupp_id = penerimaansupp_t.penerimaansupp_id)))
     JOIN obatalkes_m ON ((returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)));");

    $this->execute('ALTER TABLE "public"."int_purchasegrndetail_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_stockreturndetail_v\" AS  SELECT concat('RTR', returresepdetail_r.returresepdetail_id) AS sync_id_api,
    concat('RTR', returresepdetail_r.returresep_id) AS picking_id,
    concat('OBT', obatalkespasien_t.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE COALESCE(((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text), ''::text)
            WHEN ''::text THEN returresepdetail_r.qty_retur
            ELSE ((((obatalkespasien_t.additional_data)::json ->> 'nilai_konversi'::text))::double precision * returresepdetail_r.qty_retur)
        END AS product_uom_qty,
    obatalkespasien_t.satuankecil_id AS product_uom_id,
    obatalkespasien_t.satuankecil_id AS product_uom,
    NULL::text AS lot_id,
    6 AS sync_type,
    returresepdetail_r.id,
    returresepdetail_r.is_sent,
    returresepdetail_r.is_sending,
    returresepdetail_r.sync_respon,
    'RETUR_RESEP'::text AS tipe_rekap
   FROM (((returresepdetail_r
     JOIN returresep_r ON (((returresepdetail_r.returresep_id = returresep_r.returresep_id) AND (returresep_r.is_sent = true))))
     JOIN obatalkespasien_t ON ((returresepdetail_r.obatalkespasien_id = obatalkespasien_t.obatalkespasien_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
UNION ALL
 SELECT concat('BTL', pembatalanresepdetail_r.obatalkespasien_id) AS sync_id_api,
    concat('BTL', pembatalanresep_r.pembatalanresep_id) AS picking_id,
    concat('OBT', pembatalanresepdetail_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS category_item_id,
        CASE
            WHEN (pembatalanresepdetail_r.det_konversi = (0)::double precision) THEN pembatalanresepdetail_r.det_konversi
            WHEN (pembatalanresepdetail_r.det_konversi IS NULL) THEN pembatalanresepdetail_r.qty_konversi
            ELSE pembatalanresepdetail_r.det_konversi
        END AS product_uom_qty,
    pembatalanresepdetail_r.satuankecil_id AS product_uom_id,
    pembatalanresepdetail_r.satuankecil_id AS product_uom,
    NULL::text AS lot_id,
    6 AS sync_type,
    pembatalanresepdetail_r.id,
    pembatalanresepdetail_r.is_sent,
    pembatalanresepdetail_r.is_sending,
    pembatalanresepdetail_r.sync_respon,
    'BATAL_RESEP'::text AS tipe_rekap
   FROM (((pembatalanresepdetail_r
     JOIN penjualanresep_t ON ((pembatalanresepdetail_r.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN pembatalanresep_r ON (((penjualanresep_t.penjualanresep_id = pembatalanresep_r.penjualanresep_id) AND (pembatalanresep_r.is_sent = true))))
     JOIN obatalkes_m ON ((pembatalanresepdetail_r.obatalkes_id = obatalkes_m.obatalkes_id)));");

    $this->execute('ALTER TABLE "public"."int_stockreturndetail_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_purchasegrn_v\" AS  SELECT concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanobat_r.no_returpenerimaanobat) AS origin,
    returpenerimaanobat_r.no_returpenerimaanobat AS title,
    returpenerimaanobat_r.no_returpenerimaanobat AS name,
    '-'::text AS vendor_ref,
    13 AS currency_id,
    returpenerimaanobat_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    'draft'::text AS state,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanobat_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    6 AS sync_type,
    returpenerimaanobat_r.id,
    returpenerimaanobat_r.is_sent,
    returpenerimaanobat_r.is_sending
   FROM (returpenerimaanobat_r
     JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobat_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.supplier_id,
            sum(((obatalkes_m.harganetto / (returpenerimaanobatdetail_t.qty_retur)::double precision) * ((penerimaansuppdetail_t.diskon / (100)::numeric))::double precision)) AS amount_untaxed,
            sum(((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) - (((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision)) AS amount_tax,
            sum((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) * ((penerimaansuppdetail_t.diskon / (100)::numeric))::double precision) * (returpenerimaanobatdetail_t.qty_retur)::double precision)) AS total_discount,
            sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
            sum(((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) * (returpenerimaanobatdetail_t.qty_retur)::double precision) - ((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_t.qty_retur)::double precision)) + (((((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) - (((penerimaansuppdetail_t.harga_netto / (returpenerimaanobatdetail_t.qty_retur)::double precision) * (penerimaansuppdetail_t.diskon)::double precision) / (100)::double precision)) * (pajak_m.pajak_persen)::double precision) / (100)::double precision) * (returpenerimaanobatdetail_t.qty_retur)::double precision))) AS amount_total,
            pajak_m.pajak_persen AS ppn
           FROM ((((returpenerimaanobatdetail_t
             JOIN penerimaansuppdetail_t ON ((returpenerimaanobatdetail_t.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id)))
             JOIN penerimaansupp_t ON ((penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id)))
             JOIN obatalkes_m ON ((returpenerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN pajak_m ON ((penerimaansupp_t.pajak_id = pajak_m.pajak_id)))
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, pajak_m.pajak_persen) retur_detail ON ((returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id)))
UNION ALL
 SELECT concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanobat_r.no_returpenerimaanobat) AS origin,
    returpenerimaanobat_r.no_returpenerimaanobat AS title,
    returpenerimaanobat_r.no_returpenerimaanobat AS name,
    '-'::text AS vendor_ref,
    13 AS currency_id,
    returpenerimaanobat_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    'draft'::text AS state,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanobat_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    6 AS sync_type,
    returpenerimaanobat_r.id,
    returpenerimaanobat_r.is_sent,
    returpenerimaanobat_r.is_sending
   FROM (returpenerimaanobat_r
     JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobat_id,
            penerimaanobat_t.no_penerimaan,
            penerimaanobat_t.supplier_id,
            sum(((qty_konversi.harga_konversi * penerimaanobatdetail_t.discount) / (100)::double precision)) AS amount_untaxed,
            sum((((qty_konversi.harga_konversi - ((qty_konversi.harga_konversi * penerimaanobatdetail_t.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision)) AS amount_tax,
            sum((((qty_konversi.harga_konversi * penerimaanobatdetail_t.discount) / (100)::double precision) * (qty_konversi.qty_konversi)::double precision)) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum((((qty_konversi.harga_konversi * (qty_konversi.qty_konversi)::double precision) - (((qty_konversi.harga_konversi * penerimaanobatdetail_t.discount) / (100)::double precision) * (qty_konversi.qty_konversi)::double precision)) + ((((qty_konversi.harga_konversi - ((qty_konversi.harga_konversi * penerimaanobatdetail_t.discount) / (100)::double precision)) * (po.pajak_persen)::double precision) / (100)::double precision) * (qty_konversi.qty_konversi)::double precision))) AS amount_total,
            po.pajak_persen AS ppn
           FROM (((((returpenerimaanobatdetail_t
             JOIN penerimaanobatdetail_t ON ((returpenerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_t.penerimaanobatdetail_id)))
             JOIN penerimaanobat_t ON ((penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id)))
             JOIN obatalkes_m ON ((returpenerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
                    pajak_m.pajak_persen
                   FROM (validasipoobat_t
                     JOIN pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
                  WHERE (validasipoobat_t.is_deleted = false)) po ON ((penerimaanobat_t.validasipoobat_id = po.validasipoobat_id)))
             LEFT JOIN ( SELECT penerimaanobatdetail_t_1.penerimaanobatdetail_id,
                    returpenerimaanobatdetail_t_1.qty_retur AS qty_konversi,
                    (penerimaanobatdetail_t_1.harga / satuankonversi_m.nilai_konversi) AS harga_konversi
                   FROM ((penerimaanobatdetail_t penerimaanobatdetail_t_1
                     JOIN returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1 ON ((penerimaanobatdetail_t_1.penerimaanobatdetail_id = returpenerimaanobatdetail_t_1.penerimaanobatdetail_id)))
                     JOIN satuankonversi_m ON ((penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
                  WHERE (satuankonversi_m.is_deleted = false)) qty_konversi ON ((penerimaanobatdetail_t.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id)))
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaanobat_t.no_penerimaan, penerimaanobat_t.supplier_id, po.pajak_persen) retur_detail ON ((returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id)));");

    $this->execute('ALTER TABLE "public"."int_purchasegrn_v" OWNER TO "postgres";');

    $this->execute("
        CREATE VIEW \"public\".\"int_obatalkespasien_v\" AS  SELECT 6 AS sync_type,
    obatalkespasien_r.tgl_proses AS tglproses,
    concat('OBT', obatalkespasien_r.id) AS sync_id_api,
    concat('OBT', obatalkespasien_r.obatalkes_id) AS product_id,
    obatalkes_m.obatalkes_nama AS name,
    351 AS product_uom,
    obatalkespasien_r.qty_oa AS product_uom_qty,
    obatalkespasien_r.hargasatuan_oa AS price_unit,
    obatalkespasien_r.hargajual_oa AS price_subtotal,
    total_tagihan.total_tagihan AS price_total,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = 'BILLING'::text) THEN obatalkespasien_r.tarif_dibayarkan
            ELSE (- obatalkespasien_r.tarif_dibayarkan)
        END AS personal_amount,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = 'BILLING'::text) THEN obatalkespasien_r.tarif_dijamin
            ELSE (- obatalkespasien_r.tarif_dijamin)
        END AS payer_amount,
    obatalkespasien_r.pendaftaran_id AS order_id,
    jenisobatalkes_m.servicecategory_id AS service_categ_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS primary_doc_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS prescribe_doc_id,
    concat('PEG', obatalkespasien_r.pegawai_id) AS perform_doc_id,
    obatalkespasien_r.ruangan_id AS location_id,
    ruangan_m.ruangan_nama AS department_id,
    pembayaranpelayanan_t.no_pembayaran AS billno,
    pembayaranpelayanan_t.tgl_pembayaran AS bill_date,
    obatalkespasien_r.keterangan AS type_line,
    'LOS'::text AS revenue_type,
    jenisobatalkes_m.jenisobatalkes_nama AS item_specialisation,
        CASE
            WHEN (pendaftaran_r.instalasi_id = 1) THEN 'OPD'::text
            WHEN (pendaftaran_r.instalasi_id = 2) THEN 'EMERGENCY'::text
            WHEN (pendaftaran_r.instalasi_id = 3) THEN 'IPD'::text
            WHEN (pendaftaran_r.instalasi_id = 21) THEN 'MCU'::text
            ELSE '-'::text
        END AS patient_group,
    NULL::text AS special_group,
        CASE
            WHEN (pendaftaran_r.pasienadmisi_id IS NULL) THEN 'PHARMACY OUTPATIENT'::text
            ELSE 'PHARMACY INPATIENT'::text
        END AS special_group2,
    servicegroup_m.servicegroup_nama AS service_group,
    COALESCE(kelaspelayanan_m.kelaspelayanan_nama, 'GENERAL'::character varying) AS bed_type,
    concat('PEN', obatalkespasien_r.penjamin_id) AS payer,
    penjamin_m.penjamin_kode AS payer_code,
    carabayar_m.carabayar_nama AS payer_type,
    penjamin_m.penjamin_nama AS payer_name,
    obatalkespasien_r.no_obatalkespasien AS order_no,
    obatalkespasien_r.tglpelayanan AS order_date,
    false AS is_package,
    NULL::text AS package_name,
    NULL::text AS cost_unit,
    NULL::text AS cost_total,
        CASE
            WHEN (obatalkespasien_r.pegawai_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', obatalkespasien_r.pegawai_id)
        END AS account_analytic_id,
        CASE
            WHEN (obatalkespasien_r.pegawai_id IS NULL) THEN concat('PEG', pendaftaran_r.pegawai_id)
            ELSE concat('PEG', obatalkespasien_r.pegawai_id)
        END AS backup_analytic_id,
    pendaftaran_r.kota,
    pendaftaran_r.kecamatan,
    pendaftaran_r.kelurahan,
    pendaftaran_r.pasien_id AS partner_id,
    pendaftaran_r.no_rekam_medik AS registration_code,
    pendaftaran_r.no_pendaftaran AS number_admission,
    '-'::text AS manufacture,
    (pendaftaran_r.tglpasienpulang)::character varying AS discharge_date,
    pegawai.spesialis_nama AS specialization_primary,
    obatalkespasien_r.is_sent,
    obatalkespasien_r.is_sending,
    obatalkespasien_r.id,
        CASE
            WHEN ((obatalkespasien_r.keterangan)::text = ANY (ARRAY[('ACCRUAL'::character varying)::text, ('ACCRUAL REVERSAL'::character varying)::text])) THEN 'draft'::text
            ELSE 'bill'::text
        END AS status_bill
   FROM ((((((((((((((obatalkespasien_r
     JOIN ( SELECT pendaftaran_r_1.pendaftaran_id,
            pendaftaran_r_1.pegawai_id,
            pendaftaran_r_1.pasienadmisi_id,
            pendaftaran_r_1.instalasi_id,
            pendaftaran_r_1.pasien_id,
            pasien_m.no_rekam_medik,
            pendaftaran_r_1.no_pendaftaran,
            kabupaten_m.kabupaten_nama AS kota,
            kecamatan_m.kecamatan_nama AS kecamatan,
            kelurahan_m.kelurahan_nama AS kelurahan,
            pasienpulang_t.tglpasienpulang
           FROM (((((pendaftaran_r pendaftaran_r_1
             JOIN pasien_m ON ((pendaftaran_r_1.pasien_id = pasien_m.pasien_id)))
             LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
             LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
             LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
             LEFT JOIN pasienpulang_t ON ((pendaftaran_r_1.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
          WHERE (((pendaftaran_r_1.keterangan)::text = 'INSERT'::text) AND (pendaftaran_r_1.is_sent = true))) pendaftaran_r ON ((obatalkespasien_r.pendaftaran_id = pendaftaran_r.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_r.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
     LEFT JOIN servicegroup_m ON ((jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id)))
     JOIN ruangan_m ON ((obatalkespasien_r.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN penjamin_m ON ((obatalkespasien_r.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN ( SELECT obatalkespasien_r_1.pendaftaran_id,
            sum(obatalkespasien_r_1.hargajual_oa) AS total_tagihan
           FROM obatalkespasien_r obatalkespasien_r_1
          WHERE (obatalkespasien_r_1.is_deleted = false)
          GROUP BY obatalkespasien_r_1.pendaftaran_id) total_tagihan ON ((obatalkespasien_r.pendaftaran_id = total_tagihan.pendaftaran_id)))
     LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_r.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
     LEFT JOIN pembayaranpelayanan_t ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            sum(pembayaran_t.total_dibayar) AS total_dibayar,
            sum(pembayaran_t.total_dijamin) AS total_dijamin
           FROM pembayaran_t
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON ((obatalkespasien_r.pendaftaran_id = pembayaran.pendaftaran_id)))
     LEFT JOIN kelaspelayanan_m ON ((obatalkespasien_r.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            spesialis_m.spesialis_nama
           FROM (pegawai_m
             JOIN spesialis_m ON ((pegawai_m.spesialis_id = spesialis_m.spesialis_id)))) pegawai ON ((obatalkespasien_r.pegawai_id = pegawai.pegawai_id)));");

    $this->execute('ALTER TABLE "public"."int_obatalkespasien_v" OWNER TO "postgres";');

   

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_072550_oddo_view_20201020 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_072550_oddo_view_20201020 cannot be reverted.\n";

        return false;
    }
    */
}

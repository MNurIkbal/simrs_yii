-- public.newodoo_patientdebt_v source

CREATE OR REPLACE VIEW public.newodoo_patientdebt_v
AS SELECT 'kunjungan_rs'::text AS tipe,
    pemberianpiutang_r.id::text AS sync_id_api,
    pegawai_kasir.nama_pegawai AS user_name,
    pemberianpiutang_r.no_pemberianpiutang AS trans_no,
    pemberianpiutang_r.tgl_pemberianpiutang AS trans_date,
    'Debt'::text AS trans_type,
    pembayaran_t.no_pembayaran AS reference_no,
    pendaftaran_t.pasien_id::character varying AS partner_id,
    pendaftaran_t.pendaftaran_id::character varying AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    int_billing_r.id AS billing_id,
    pembayaran_t.no_pembayaran AS billing_no,
    pasien_m.nama_pasien AS patient_name,
    pembayaranmetode_t.tipe_pembayaran AS payment_name,
    pemberianpiutang_r.tgl_proses AS tglproses,
    pembayaranmetode_t.nama_edc AS edc_machine,
    pemberianpiutang_r.total_piutang AS amount,
    pemberianpiutang_r.catatan AS note,
        CASE
            WHEN pemberianpiutang_r.keterangan_rekap::text = 'ACCRUAL'::text THEN 'draft'::text
            ELSE 'cancel'::text
        END AS state,
    pendaftaran_t.patient_type,
    pemberianpiutang_r.keterangan_rekap
   FROM pemberianpiutang_r
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON pemberianpiutang_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
     LEFT JOIN ( SELECT a.pemberianpiutang_id,
            a.no_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pemberianpiutang_r.pemberianpiutang_id = pembayaran_t.pemberianpiutang_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id
           FROM int_billing_r a) int_billing_r ON pembayaran_t.pembayaran_id = int_billing_r.pembayaran_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.no_pendaftaran,
                CASE
                    WHEN a.is_aps = true AND a.instalasi_id <> 21 THEN '1'::text
                    WHEN a.instalasi_id = 1 THEN '1'::text
                    WHEN a.instalasi_id = 3 THEN '2'::text
                    WHEN a.instalasi_id = 2 AND a.pasienadmisi_id IS NOT NULL THEN '2'::text
                    WHEN a.instalasi_id = 2 AND a.pasienadmisi_id IS NULL THEN '3'::text
                    WHEN a.instalasi_id = 6 THEN '6'::text
                    WHEN a.instalasi_id = 21 THEN '5'::text
                    ELSE '4'::text
                END AS patient_type
           FROM pendaftaran_t a) pendaftaran_t ON pemberianpiutang_r.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.nama_edc,
            tipe_pembayaran.lookup_value AS tipe_pembayaran
           FROM pembayaranmetode_t a
             LEFT JOIN jenisnontunai_m ON a.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN lookup_m tipe_pembayaran ON jenisnontunai_m.tipe_pembayaran = tipe_pembayaran.lookup_id) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
UNION ALL
 SELECT 'penjualan_resep'::text AS tipe,
    pemberianpiutang_r.id::text AS sync_id_api,
    pegawai_kasir.nama_pegawai AS user_name,
    pemberianpiutang_r.no_pemberianpiutang AS trans_no,
    pemberianpiutang_r.tgl_pemberianpiutang AS trans_date,
    'Debt'::text AS trans_type,
    pembayaran_t.no_pembayaran AS reference_no,
    penjualanresep_t.partner_id,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS admission_id,
    penjualanresep_t.noresep AS admission_no,
    int_billing_r.id AS billing_id,
    pembayaran_t.no_pembayaran AS billing_no,
    penjualanresep_t.patient_nama AS patient_name,
    pembayaranmetode_t.tipe_pembayaran AS payment_name,
    pemberianpiutang_r.tgl_proses AS tglproses,
    pembayaranmetode_t.nama_edc AS edc_machine,
    pemberianpiutang_r.total_piutang AS amount,
    pemberianpiutang_r.catatan AS note,
        CASE
            WHEN pemberianpiutang_r.keterangan_rekap::text = 'ACCRUAL'::text THEN 'draft'::text
            ELSE 'cancel'::text
        END AS state,
    '1'::text AS patient_type,
    pemberianpiutang_r.keterangan_rekap
   FROM pemberianpiutang_r
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON pemberianpiutang_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
     LEFT JOIN ( SELECT a.pemberianpiutang_id,
            a.no_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pemberianpiutang_r.pemberianpiutang_id = pembayaran_t.pemberianpiutang_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id
           FROM int_billing_r a) int_billing_r ON pembayaran_t.pembayaran_id = int_billing_r.pembayaran_id
     JOIN ( SELECT a.penjualanresep_id,
            a.noresep,
                CASE
                    WHEN a.jenispenjualan::text = '343'::text THEN 0::character varying
                    WHEN a.jenispenjualan::text = '345'::text THEN concat('PEG', a.karyawan_id)::character varying
                    ELSE 0::character varying
                END AS partner_id,
                CASE
                    WHEN a.jenispenjualan::text = '343'::text THEN 'PASIEN APS'::character varying
                    WHEN a.jenispenjualan::text = '345'::text THEN pegawai_m.nama_pegawai
                    ELSE NULL::character varying
                END AS patient_nama
           FROM penjualanresep_t a
             LEFT JOIN pegawai_m ON a.karyawan_id = pegawai_m.pegawai_id) penjualanresep_t ON pemberianpiutang_r.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.nama_edc,
            tipe_pembayaran.lookup_value AS tipe_pembayaran
           FROM pembayaranmetode_t a
             LEFT JOIN jenisnontunai_m ON a.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN lookup_m tipe_pembayaran ON jenisnontunai_m.tipe_pembayaran = tipe_pembayaran.lookup_id) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
UNION ALL
 SELECT 'kunjungan_rs'::text AS tipe,
    concat('BPU', pembayaranpiutang_r.id) AS sync_id_api,
    pegawai_kasir.nama_pegawai AS user_name,
    pembayaranpiutang_r.no_pembayaranpiutang AS trans_no,
    pembayaranpiutang_r.tgl_pembayaranpiutang AS trans_date,
    'Payment'::text AS trans_type,
    pembayaran_t.no_pembayaran AS reference_no,
    pendaftaran_t.pasien_id::character varying AS partner_id,
    pendaftaran_t.pendaftaran_id::character varying AS admission_id,
    pendaftaran_t.no_pendaftaran AS admission_no,
    int_billing_r.id AS billing_id,
    pembayaran_t.no_pembayaran AS billing_no,
    pasien_m.nama_pasien AS patient_name,
    pembayaranmetode_t.tipe_pembayaran AS payment_name,
    pembayaranpiutang_r.tgl_proses AS tglproses,
    pembayaranmetode_t.nama_edc AS edc_machine,
    pembayaranpiutang_r.total_bayarpiutang AS amount,
    pembayaranpiutang_r.catatan AS note,
    'draft'::text AS state,
    pendaftaran_t.patient_type,
    pembayaranpiutang_r.keterangan_rekap
   FROM pembayaranpiutang_r
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON pembayaranpiutang_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pembayaranpiutang_r.pendaftaran_id = pembayaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id
           FROM int_billing_r a) int_billing_r ON pembayaran_t.pembayaran_id = int_billing_r.pembayaran_id
     JOIN pemberianpiutang_t ON pembayaranpiutang_r.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.no_pendaftaran,
                CASE
                    WHEN a.is_aps = true AND a.instalasi_id <> 21 THEN '1'::text
                    WHEN a.instalasi_id = 1 THEN '1'::text
                    WHEN a.instalasi_id = 3 THEN '2'::text
                    WHEN a.instalasi_id = 2 AND a.pasienadmisi_id IS NOT NULL THEN '2'::text
                    WHEN a.instalasi_id = 2 AND a.pasienadmisi_id IS NULL THEN '3'::text
                    WHEN a.instalasi_id = 6 THEN '6'::text
                    WHEN a.instalasi_id = 21 THEN '5'::text
                    ELSE '4'::text
                END AS patient_type
           FROM pendaftaran_t a) pendaftaran_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.nama_edc,
            tipe_pembayaran.lookup_value AS tipe_pembayaran
           FROM pembayaranmetode_t a
             LEFT JOIN jenisnontunai_m ON a.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN lookup_m tipe_pembayaran ON jenisnontunai_m.tipe_pembayaran = tipe_pembayaran.lookup_id) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id
UNION ALL
 SELECT 'penjualan_resep'::text AS tipe,
    concat('BPU', pembayaranpiutang_r.id) AS sync_id_api,
    pegawai_kasir.nama_pegawai AS user_name,
    pembayaranpiutang_r.no_pembayaranpiutang AS trans_no,
    pembayaranpiutang_r.tgl_pembayaranpiutang AS trans_date,
    'Payment'::text AS trans_type,
    pembayaran_t.no_pembayaran AS reference_no,
    penjualanresep_t.partner_id,
    concat('RSPB', penjualanresep_t.penjualanresep_id) AS admission_id,
    penjualanresep_t.noresep AS admission_no,
    int_billing_r.id AS billing_id,
    pembayaran_t.no_pembayaran AS billing_no,
    penjualanresep_t.patient_nama AS patient_name,
    pembayaranmetode_t.tipe_pembayaran AS payment_name,
    pembayaranpiutang_r.tgl_proses AS tglproses,
    pembayaranmetode_t.nama_edc AS edc_machine,
    pembayaranpiutang_r.total_bayarpiutang AS amount,
    pembayaranpiutang_r.catatan AS note,
    'draft'::text AS state,
    '1'::text AS patient_type,
    pembayaranpiutang_r.keterangan_rekap
   FROM pembayaranpiutang_r
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON pembayaranpiutang_r.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_kasir ON loginpemakai_k.pegawai_id = pegawai_kasir.pegawai_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.no_pembayaran,
            a.pembayaran_id
           FROM pembayaran_t a
          WHERE a.is_deleted = false) pembayaran_t ON pembayaranpiutang_r.pendaftaran_id = pembayaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.id
           FROM int_billing_r a) int_billing_r ON pembayaran_t.pembayaran_id = int_billing_r.pembayaran_id
     JOIN pemberianpiutang_t ON pembayaranpiutang_r.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id
     JOIN ( SELECT a.penjualanresep_id,
            a.noresep,
                CASE
                    WHEN a.jenispenjualan::text = '343'::text THEN 0::character varying
                    WHEN a.jenispenjualan::text = '345'::text THEN concat('PEG', a.karyawan_id)::character varying
                    ELSE 0::character varying
                END AS partner_id,
                CASE
                    WHEN a.jenispenjualan::text = '343'::text THEN 'PASIEN APS'::character varying
                    WHEN a.jenispenjualan::text = '345'::text THEN pegawai_m.nama_pegawai
                    ELSE NULL::character varying
                END AS patient_nama
           FROM penjualanresep_t a
             LEFT JOIN pegawai_m ON a.karyawan_id = pegawai_m.pegawai_id) penjualanresep_t ON pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT a.pembayaran_id,
            a.nama_edc,
            tipe_pembayaran.lookup_value AS tipe_pembayaran
           FROM pembayaranmetode_t a
             LEFT JOIN jenisnontunai_m ON a.jenisnontunai_id = jenisnontunai_m.jenisnontunai_id
             LEFT JOIN lookup_m tipe_pembayaran ON jenisnontunai_m.tipe_pembayaran = tipe_pembayaran.lookup_id) pembayaranmetode_t ON pembayaran_t.pembayaran_id = pembayaranmetode_t.pembayaran_id;
<?php

use yii\db\Migration;

/**
 * Class m200117_075022_penatajasa_1779_1795
 */
class m200117_075022_penatajasa_1779_1795 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkespasien_t" 
                        ADD COLUMN "is_penatajasa" bool;');

        $this->execute('DROP VIEW if exists public.infotindakanpenatajasa_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infotindakanpenatajasa_v AS 
 SELECT 'tindakan'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    NULL::integer AS obatalkespasien_id,
    NULL::integer AS obatalkes_id,
    NULL::character varying AS obatalkes_nama,
    NULL::double precision AS qty_oa,
    NULL::double precision AS hargajual_oa,
    NULL::integer AS satuanobat_id,
    NULL::character varying AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id
     LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE tindakanpelayanan_t.is_deleted IS FALSE
UNION ALL
 SELECT 'paket'::text AS jenis,
    tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    tindakanpelayanan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tipepaket_m.tipepaket_id AS daftartindakan_id,
    tipepaket_m.tipepaket_nama AS daftartindakan_nama,
    tindakanpelayanan_t.qty_tindakan,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan,
    tindakanpelayanan_t.tarif_tindakan,
    tindakanpelayanan_t.is_penatajasa,
        CASE COALESCE(tindakansudahbayar.telahbayar, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    tindakanpelayanan_t.is_deleted,
    tindakanpelayanan_t.keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    tindakanpelayanan_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran
   FROM tindakanpelayanan_t
     JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id
     LEFT JOIN obatalkespasien_t ON tindakanpelayanan_t.tindakanpelayanan_id = obatalkespasien_t.tindakanpelayanan_id AND obatalkespasien_t.is_deleted = false
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE tindakanpelayanan_t.is_deleted IS FALSE
UNION ALL
 SELECT 'obat'::text AS jenis,
    NULL::integer AS tindakanpelayanan_id,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    obatalkespasien_t.ruangan_id,
    ruangan_m.ruangan_nama,
    obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    tindakanpelayanan_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    obatalkespasien_t.qty_oa AS qty_tindakan,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarifcyto_tindakan,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan,
    obatalkespasien_t.is_penatajasa,
        CASE COALESCE(obatsudahbayar.telahbayar, 0::bigint)
            WHEN 0 THEN false
            ELSE true
        END AS is_bayar,
    obatalkespasien_t.is_deleted,
    NULL::text AS keterangantindakan,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.hargajual_oa,
    obatalkespasien_t.satuankecil_id AS satuanobat_id,
    satuanunit_m.satuanunit_nama AS satuanobat_nama,
    NULL::integer AS kelaspelayanan_id,
    NULL::character varying AS kelaspelayanan_nama,
    pendaftaran_t.no_pendaftaran
   FROM obatalkespasien_t
     JOIN pendaftaran_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT obatsudahbayar_t.obatalkespasien_id,
            count(*) AS telahbayar
           FROM obatsudahbayar_t
          GROUP BY obatsudahbayar_t.obatsudahbayar_id) obatsudahbayar ON obatalkespasien_t.obatalkespasien_id = obatsudahbayar.obatalkespasien_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id AND tindakanpelayanan_t.is_deleted = false
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
  WHERE obatalkespasien_t.is_deleted IS FALSE;");

        $this->execute('ALTER TABLE public.infotindakanpenatajasa_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.pendaftaranpenjamin_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pendaftaranpenjamin_v AS 
 SELECT pendaftaranpenjamin_t.pendaftaranpenjamin_id,
    pendaftaranpenjamin_t.pendaftaran_id,
    pendaftaranpenjamin_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaranpenjamin_t.penjamin_id,
    pendaftaranpenjamin_t.penjamin_nama,
    pendaftaranpenjamin_t.nokartuasuransi,
    pendaftaranpenjamin_t.pasien_id,
    pendaftaranpenjamin_t.asuransipasien_id,
    pendaftaranpenjamin_t.nama_pasien,
    pendaftaranpenjamin_t.namapemilikasuransi,
    pendaftaranpenjamin_t.nominal_dijamin,
    pendaftaranpenjamin_t.alasan_batal,
    pendaftaranpenjamin_t.is_deleted,
    pendaftaranpenjamin_t.created_date
   FROM pendaftaranpenjamin_t
     JOIN carabayar_m ON pendaftaranpenjamin_t.carabayar_id = carabayar_m.carabayar_id
  WHERE pendaftaranpenjamin_t.is_deleted IS FALSE;
");

        $this->execute('ALTER TABLE public.pendaftaranpenjamin_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infotarifrs_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infotarifrs_v AS 
 SELECT 'tindakan'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    r_tindakan.ruangan_nama,
    r_tindakan.instalasi_id,
    ins_tindakan.instalasi_nama,
    NULL::integer AS ruanganpaket_id,
    NULL::character varying AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    daftartindakan_m.kategoritindakan_id,
    kategoritindakan_m.kategoritindakan_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    daftartindakan_m.is_akomodasi,
    penjamin_m.carabayar_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
     JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
     JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
  WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
UNION ALL
 SELECT 'paket'::text AS jenis,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    r_paket.ruangan_nama,
    r_paket.instalasi_id,
    ins_paket.instalasi_nama,
    paketruangan_mp.ruangan_id AS ruanganpaket_id,
    r_paket.ruangan_namalainnya AS ruanganpaket_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama,
    NULL::integer AS kategoritindakan_id,
    NULL::character varying AS kategoritindakan_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    NULL::boolean AS is_akomodasi,
    penjamin_m.carabayar_id
   FROM tariftindakan_m
     JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
     JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
     JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
  WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true;
");

        $this->execute('ALTER TABLE public.infotarifrs_v
  OWNER TO postgres;');

        $this->execute('CREATE TABLE public.logactivity_r
                    (
                      logactivity_id serial8,
                      tgl date,
                      tipe character varying(100), 
                      aksi character varying(100), 
                      keterangan text,
                      alasan text,
                      additional_data text,
                      created_date timestamp(6) without time zone NOT NULL DEFAULT now(),
                      created_by integer,
                      modified_count integer,
                      last_modified_date timestamp(6) without time zone,
                      last_modified_by integer,
                      is_deleted boolean NOT NULL DEFAULT false,
                      is_active boolean NOT NULL DEFAULT true,
                      deleted_date timestamp(6) without time zone,
                      deleted_by integer,
                      CONSTRAINT logactivity_r_pkey PRIMARY KEY (logactivity_id)
                    )
                    WITH (
                      OIDS=FALSE
                    );');

        $this->execute('ALTER TABLE public.logactivity_r
  OWNER TO postgres;');

        $this->execute("COMMENT ON COLUMN public.logactivity_r.tipe IS 'lookup_type=''logactivity_tipe''';");

        $this->execute("COMMENT ON COLUMN public.logactivity_r.aksi IS 'lookup_type=''logactivity_aksi';");

        $this->execute('DROP VIEW if exists public.logactivitypenatajasa_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.logactivitypenatajasa_v AS 
 SELECT tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.created_date,
    COALESCE(daftartindakan_m.daftartindakan_nama, tipepaket_m.tipepaket_nama) AS keterangan,
    'Tambah Tindakan'::character varying AS tipe,
    tindakanpelayanan_t.keterangantindakan AS alasan,
    loginpemakai_k.loginpemakai_id,
    loginpemakai_k.nama_pemakai,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai
   FROM tindakanpelayanan_t
     JOIN loginpemakai_k ON tindakanpelayanan_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
  WHERE tindakanpelayanan_t.is_penatajasa IS TRUE
UNION ALL
 SELECT tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.deleted_date AS created_date,
    COALESCE(daftartindakan_m.daftartindakan_nama, tipepaket_m.tipepaket_nama) AS keterangan,
    'Hapus Tindakan'::character varying AS tipe,
    tindakanpelayanan_t.keterangantindakan AS alasan,
    loginpemakai_k.loginpemakai_id,
    loginpemakai_k.nama_pemakai,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai
   FROM tindakanpelayanan_t
     JOIN loginpemakai_k ON tindakanpelayanan_t.deleted_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
  WHERE tindakanpelayanan_t.is_penatajasa IS TRUE
UNION ALL
 SELECT pendaftaranpenjamin_t.pendaftaran_id,
    pendaftaranpenjamin_t.created_date,
    pendaftaranpenjamin_t.penjamin_nama AS keterangan,
    'Tambah Penjamin'::character varying AS tipe,
    pendaftaranpenjamin_t.alasan_batal AS alasan,
    loginpemakai_k.loginpemakai_id,
    loginpemakai_k.nama_pemakai,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai
   FROM pendaftaranpenjamin_t
     JOIN loginpemakai_k ON pendaftaranpenjamin_t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
UNION ALL
 SELECT pendaftaranpenjamin_t.pendaftaran_id,
    pendaftaranpenjamin_t.deleted_date AS created_date,
    pendaftaranpenjamin_t.penjamin_nama AS keterangan,
    'Hapus Penjamin'::character varying AS tipe,
    pendaftaranpenjamin_t.alasan_batal AS alasan,
    loginpemakai_k.loginpemakai_id,
    loginpemakai_k.nama_pemakai,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai
   FROM pendaftaranpenjamin_t
     JOIN loginpemakai_k ON pendaftaranpenjamin_t.deleted_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id;");

        $this->execute('ALTER TABLE public.logactivitypenatajasa_v
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200117_075022_penatajasa_1779_1795 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200117_075022_penatajasa_1779_1795 cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m191004_093359_penata_jasa
 */
class m191004_093359_penata_jasa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE tindakanpelayanan_t ADD is_penatajasa BOOLEAN;');

       $this->execute('ALTER TABLE tindakanpelayanan_t ADD additional_riwayat BOOLEAN;');

       $this->execute('ALTER TABLE pendaftaran_t ADD catatan_penatajasa TEXT;');

       $this->execute('CREATE SEQUENCE pendaftaranpenjamin_t_pendaftaranpenjamin_id_seq
                      INCREMENT 1
                      MINVALUE 1
                      MAXVALUE 9223372036854775807
                      START 1
                      CACHE 1;');

       $this->execute('ALTER TABLE pendaftaranpenjamin_t_pendaftaranpenjamin_id_seq
                    OWNER TO postgres;');

       $this->execute("
        CREATE TABLE pendaftaranpenjamin_t
(
    pendaftaranpenjamin_id INTEGER NOT NULL DEFAULT nextval('pendaftaranpenjamin_t_pendaftaranpenjamin_id_seq'::regclass),
    pendaftaran_id integer NOT NULL,
    tgl_pendaftaranpenjamin timestamp without time zone,
    carabayar_id int4,
    penjamin_id int4,
    penjamin_nama VARCHAR,
    pasien_id int4,
    asuransipasien_id int4,
    nama_pasien VARCHAR,
    namapemilikasuransi VARCHAR,
    nokartuasuransi VARCHAR,
    nominal_dijamin float8,
    alasan_batal VARCHAR,
    additional_data text,
    created_date timestamp without time zone NOT NULL DEFAULT ('now'::text)::date,
    created_by integer,
    modified_count integer,
    last_modified_date timestamp without time zone,
    last_modified_by integer,
    is_deleted boolean NOT NULL DEFAULT false,
    is_active boolean NOT NULL DEFAULT true,
    deleted_date timestamp without time zone,
    deleted_by integer,
    CONSTRAINT pendaftaranpenjamin_t_pkey PRIMARY KEY (pendaftaranpenjamin_id)
)
WITH (
  OIDS=FALSE
);");

       $this->execute('ALTER TABLE pendaftaranpenjamin_t
  OWNER TO postgres;');

       $this->execute('DROP INDEX IF EXISTS pendaftaranpenjamin_t_pendaftaran_id_idx;');

       $this->execute('CREATE INDEX pendaftaranpenjamin_t_pendaftaran_id_idx
                      ON pendaftaranpenjamin_t
                      USING btree
                      (pendaftaran_id);');

       $this->execute('DROP VIEW if exists public.infopasienpenatajasa_v;');

       $this->execute("
        CREATE OR REPLACE VIEW public.infopasienpenatajasa_v AS 
 SELECT pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warga_negara,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.ruangan_id,
    ruangan_m.ruangan_nama,
    (instalasi_m.instalasi_nama::text || ' - '::text) || ruangan_m.ruangan_nama::text AS instalasi_ruangan,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    (carabayar_m.carabayar_nama::text || ' - '::text) || penjamin_m.penjamin_nama::text AS carabayar_penjamin,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.status_periksa::integer AS status_periksa_id,
    fgetnamalookup(pendaftaran_t.status_periksa::integer) AS status_periksa,
    pendaftaran_t.status_bayar AS status_bayar_id,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    asuransipasien.nokartuasuransi,
    pendaftaran_t.catatan_penatajasa
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.penjamin_id,
            asuransipasien_m.carabayar_id,
            asuransipasien_m.nokartuasuransi
           FROM asuransipasien_m
             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                    asuransipasien_m_1.carabayar_id,
                    asuransipasien_m_1.penjamin_id
                   FROM asuransipasien_m asuransipasien_m_1
                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id) asuransipasien ON pendaftaran_t.pasien_id = asuransipasien.pasien_id AND pendaftaran_t.carabayar_id = asuransipasien.carabayar_id AND pendaftaran_t.penjamin_id = asuransipasien.penjamin_id
  WHERE pendaftaran_t.instalasi_id <> 3 AND pendaftaran_t.pasienpulang_id IS NULL
UNION ALL
 SELECT pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    fgetnamalookup(pasien_m.jenisidentitas::integer) AS jenisidentitas,
    pasien_m.no_identitas_pasien,
    fgetnamalookup(pasien_m.namadepan::integer) AS namadepan,
    pasien_m.nama_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasien_m.tanggal_lahir,
    fgetnamalookup(pasien_m.statusperkawinan::integer) AS statusperkawinan,
    fgetnamalookup(pasien_m.golongandarah::integer) AS golongandarah,
    fgetnamalookup(pasien_m.agama::integer) AS agama,
    pasien_m.no_telepon_pasien,
    pasien_m.no_mobile_pasien,
    fgetnamalookup(pasien_m.warga_negara::integer) AS warga_negara,
    pasien_m.alamat_pasien,
    pendaftaran_t.umur,
    pendaftaran_t.pendaftaran_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienadmisi_t.ruangan_id,
    ruangan_m.ruangan_nama,
    (instalasi_m.instalasi_nama::text || ' - '::text) || ruangan_m.ruangan_nama::text AS instalasi_ruangan,
    pasienadmisi_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    (carabayar_m.carabayar_nama::text || ' - '::text) || penjamin_m.penjamin_nama::text AS carabayar_penjamin,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasienadmisi_t.status_ranap AS status_periksa_id,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
    pendaftaran_t.status_bayar AS status_bayar_id,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    bpjs_t.klsrawat AS hak_kelas,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_dpjp,
    asuransipasien.nokartuasuransi,
    pendaftaran_t.catatan_penatajasa
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.penjamin_id,
            asuransipasien_m.carabayar_id,
            asuransipasien_m.nokartuasuransi
           FROM asuransipasien_m
             LEFT JOIN ( SELECT min(asuransipasien_m_1.asuransipasien_id) AS asuransipasien_id,
                    asuransipasien_m_1.carabayar_id,
                    asuransipasien_m_1.penjamin_id
                   FROM asuransipasien_m asuransipasien_m_1
                  GROUP BY asuransipasien_m_1.carabayar_id, asuransipasien_m_1.penjamin_id) asuransipasien_min ON asuransipasien_m.asuransipasien_id = asuransipasien_min.asuransipasien_id) asuransipasien ON pendaftaran_t.pasien_id = asuransipasien.pasien_id AND pendaftaran_t.carabayar_id = asuransipasien.carabayar_id AND pendaftaran_t.penjamin_id = asuransipasien.penjamin_id
  WHERE pasienadmisi_t.pasienpulang_id IS NULL;");

       $this->execute('ALTER TABLE public.infopasienpenatajasa_v
  OWNER TO postgres;');

       $this->execute('DROP VIEW if exists public.infotindakanpenatajasa_v;');

       $this->execute("
        CREATE OR REPLACE VIEW public.infotindakanpenatajasa_v AS 
 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
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
    tindakanpelayanan_t.keterangantindakan
   FROM tindakanpelayanan_t
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id
  WHERE tindakanpelayanan_t.is_deleted IS FALSE
UNION ALL
 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
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
    tindakanpelayanan_t.keterangantindakan
   FROM tindakanpelayanan_t
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT tindakansudahbayar_t.tindakanpelayanan_id,
            count(*) AS telahbayar
           FROM tindakansudahbayar_t
          GROUP BY tindakansudahbayar_t.tindakanpelayanan_id) tindakansudahbayar ON tindakanpelayanan_t.tindakanpelayanan_id = tindakansudahbayar.tindakanpelayanan_id
  WHERE tindakanpelayanan_t.is_deleted IS FALSE;");

       $this->execute('ALTER TABLE public.infotindakanpenatajasa_v
  OWNER TO postgres;');

       $this->execute('DROP VIEW if exists public.pendaftaranpenjamin_v;');

       $this->execute("
        CREATE OR REPLACE VIEW public.pendaftaranpenjamin_v AS 
 SELECT pendaftaranpenjamin_t.pendaftaranpenjamin_id,
    pendaftaranpenjamin_t.pendaftaran_id,
    pendaftaranpenjamin_t.carabayar_id,
    pendaftaranpenjamin_t.penjamin_id,
    pendaftaranpenjamin_t.penjamin_nama,
    pendaftaranpenjamin_t.nokartuasuransi,
    pendaftaranpenjamin_t.pasien_id,
    pendaftaranpenjamin_t.asuransipasien_id,
    pendaftaranpenjamin_t.nama_pasien,
    pendaftaranpenjamin_t.namapemilikasuransi,
    pendaftaranpenjamin_t.nominal_dijamin,
    pendaftaranpenjamin_t.alasan_batal,
    pendaftaranpenjamin_t.is_deleted
   FROM pendaftaranpenjamin_t
  WHERE pendaftaranpenjamin_t.is_deleted IS FALSE;
");

       $this->execute('ALTER TABLE public.pendaftaranpenjamin_v
  OWNER TO postgres;');

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
        echo "m191004_093359_penata_jasa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191004_093359_penata_jasa cannot be reverted.\n";

        return false;
    }
    */
}

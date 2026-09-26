<?php

use yii\db\Migration;

/**
 * Class m200512_010955_migrate_20200511
 */
class m200512_010955_migrate_20200511 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infoadjusmenobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoadjusmenobatdetail_v\" AS  SELECT 'masuk'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatmasuk_t.adjusmenobatmasuk_id AS detail_id,
    adjusmenobatmasuk_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatmasuk_t.qty AS qty_input,
    adjusmenobatmasuk_t.qty_konversi,
    adjusmenobatmasuk_t.tgl_kadaluarsa,
    adjusmenobatmasuk_t.harga_netto,
    adjusmenobatmasuk_t.no_batch,
    adjusmenobatmasuk_t.keterangan,
    NULL::text AS alasan
   FROM ((adjusmenobat_t
     JOIN adjusmenobatmasuk_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatmasuk_t.adjusmenobat_id)))
     JOIN obatalkes_m ON ((adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id)))
  WHERE ((adjusmenobat_t.is_deleted = false) AND (adjusmenobatmasuk_t.is_deleted = false))
UNION ALL
 SELECT 'keluar'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatkeluar_t.adjusmenobatkeluar_id AS detail_id,
    adjusmenobatkeluar_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatkeluar_t.qty AS qty_input,
    stokobatalkes_t.qtystok_out AS qty_konversi,
    stokobatalkes_t.tglkadaluarsa AS tgl_kadaluarsa,
    NULL::double precision AS harga_netto,
    adjusmenobatkeluar_t.no_batch,
    adjusmenobatkeluar_t.keterangan,
    adjusmenobatkeluar_t.alasan
   FROM (((adjusmenobat_t
     JOIN adjusmenobatkeluar_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id)))
     JOIN stokobatalkes_t ON (((adjusmenobatkeluar_t.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id) AND (adjusmenobat_t.ruangan_adjusmen_id = stokobatalkes_t.ruangan_id) AND (adjusmenobatkeluar_t.obatalkes_id = stokobatalkes_t.obatalkes_id))))
     JOIN obatalkes_m ON ((adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id)))
  WHERE ((adjusmenobat_t.is_deleted = false) AND (adjusmenobatkeluar_t.is_deleted = false));");
        
        $this->execute('DROP VIEW if exists "public"."infopasienmcudetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienmcudetail_v\" AS  SELECT 'RAD'::text AS penunjang,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    radiologi.tindakanpelayanan_id,
        CASE
            WHEN (radiologi.tipepaket_id IS NULL) THEN radiologi.daftartindakan_id
            ELSE radiologi.tipepaket_id
        END AS tindakan_paket_id,
    radiologi.tipepaket_nama,
    radiologi.detail_2id,
    radiologi.detail_2,
    radiologi.detail_3id,
        CASE
            WHEN (radiologi.tipepaket_id IS NULL) THEN radiologi.daftartindakan_nama
            ELSE radiologi.detail_3
        END AS detail_3,
    radiologi.p_rad AS pemeriksaan,
    radiologi.j_rad AS jenis,
    kesimpulanmcu_t.kesimpulan
   FROM ((((((pendaftaran_t
     JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
     JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 5))))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN ( SELECT rad.tindakanpelayanan_id,
            rad.pendaftaran_id,
            rad.tipepaket_id,
            rad.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            detail.detail_2id,
            detail.detail_2,
            detail.detail_3id,
            detail.detail_3,
            detail.p_rad,
            detail.j_rad
           FROM (((tindakanpelayanan_t rad
             LEFT JOIN tipepaket_m ON ((rad.tipepaket_id = tipepaket_m.tipepaket_id)))
             LEFT JOIN daftartindakan_m ON ((rad.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_rad,
                    paket_detail.j_rad
                   FROM (((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m_1.daftartindakan_id,
                            daftartindakan_m_1.daftartindakan_nama,
                            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                           FROM ((((tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                             JOIN daftartindakan_m daftartindakan_m_1 ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id)))
                             LEFT JOIN pemeriksaanrad_m ON ((daftartindakan_m_1.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                             LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
                   FROM (((((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
                     JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
                     LEFT JOIN pemeriksaanrad_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
                     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)) detail ON ((rad.tipepaket_id = detail.detail_1id)))) radiologi ON ((pendaftaran_t.pendaftaran_id = radiologi.pendaftaran_id)))
     LEFT JOIN kesimpulanmcu_t ON ((radiologi.tindakanpelayanan_id = kesimpulanmcu_t.tindakanpelayanan_id)))
  WHERE (pendaftaran_t.instalasi_id = 21)
UNION ALL
 SELECT 'LAB'::text AS penunjang,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status,
    laboratorium.tindakanpelayanan_id,
        CASE
            WHEN (laboratorium.tipepaket_id IS NULL) THEN laboratorium.daftartindakan_id
            ELSE laboratorium.tipepaket_id
        END AS tindakan_paket_id,
    laboratorium.tipepaket_nama,
    laboratorium.detail_2id,
    laboratorium.detail_2,
    laboratorium.detail_3id,
        CASE
            WHEN (laboratorium.tipepaket_id IS NULL) THEN laboratorium.daftartindakan_nama
            ELSE laboratorium.detail_3
        END AS detail_3,
    laboratorium.p_lab AS pemeriksaan,
    laboratorium.j_lab AS jenis,
    kesimpulanmcu_t.kesimpulan
   FROM ((((((pendaftaran_t
     JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
     JOIN ruangan_m ON (((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.instalasi_id = 4))))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ( SELECT lab.tindakanpelayanan_id,
            lab.pendaftaran_id,
            lab.tipepaket_id,
            lab.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_nama,
            detail.detail_2id,
            detail.detail_2,
            detail.detail_3id,
            detail.detail_3,
            detail.p_lab,
            detail.j_lab
           FROM (((tindakanpelayanan_t lab
             JOIN tipepaket_m ON ((lab.tipepaket_id = tipepaket_m.tipepaket_id)))
             LEFT JOIN daftartindakan_m ON ((lab.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    paketpelayanan_mp.paketdetail_id AS detail_2id,
                    paket_detail.tipepaket_nama AS detail_2,
                    paket_detail.daftartindakan_id AS detail_3id,
                    paket_detail.daftartindakan_nama AS detail_3,
                    paket_detail.p_lab,
                    paket_detail.j_lab
                   FROM (((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            daftartindakan_m_1.daftartindakan_id,
                            daftartindakan_m_1.daftartindakan_nama,
                            pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                           FROM ((((tipepaket_m a
                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
                             JOIN daftartindakan_m daftartindakan_m_1 ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m_1.daftartindakan_id)))
                             LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m_1.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                             LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)
                UNION ALL
                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
                    NULL::integer AS detail_2id,
                    NULL::character varying AS detail_2,
                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
                    tindakan_detail.daftartindakan_nama AS detail_3,
                    pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
                   FROM (((((tipepaket_m tipepaket_m_1
                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
                     JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
                     LEFT JOIN pemeriksaanlab_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
                     LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                  WHERE (tipepaket_m_1.is_deleted = false)) detail ON ((lab.tipepaket_id = detail.detail_1id)))) laboratorium ON ((pendaftaran_t.pendaftaran_id = laboratorium.pendaftaran_id)))
     LEFT JOIN kesimpulanmcu_t ON ((laboratorium.tindakanpelayanan_id = kesimpulanmcu_t.tindakanpelayanan_id)))
  WHERE (pendaftaran_t.instalasi_id = 21);");

        $this->execute('CREATE TABLE "public"."tindakanoperasi_mp" (
  "timoperasi_id" int4 NOT NULL,
  "daftartindakan_id" int4 NOT NULL,
  "additional_data" text COLLATE "pg_catalog"."default",
  "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
  "created_by" int4,
  "modified_count" int4,
  "last_modified_date" timestamp(6),
  "last_modified_by" int4,
  "is_deleted" bool NOT NULL DEFAULT false,
  "is_active" bool NOT NULL DEFAULT true,
  "deleted_date" timestamp(6),
  "deleted_by" int4,
  CONSTRAINT "pk_tindakanoperasi_mp" PRIMARY KEY ("timoperasi_id", "daftartindakan_id")
)
;');
        $this->execute("
            CREATE VIEW \"public\".\"tindakanoperasi_v\" AS  SELECT tindakanoperasi_mp.timoperasi_id,
    lookup_m.lookup_name AS timoperasi_nama,
    tindakanoperasi_mp.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    lookup_m.lookup_value AS persentase
   FROM ((tindakanoperasi_mp
     JOIN daftartindakan_m ON ((tindakanoperasi_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN lookup_m ON ((tindakanoperasi_mp.timoperasi_id = lookup_m.lookup_id)));");

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200512_010955_migrate_20200511 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200512_010955_migrate_20200511 cannot be reverted.\n";

        return false;
    }
    */
}

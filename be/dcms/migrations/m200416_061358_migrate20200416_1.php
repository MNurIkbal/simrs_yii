<?php

use yii\db\Migration;

/**
 * Class m200416_061358_migrate20200416_1
 */
class m200416_061358_migrate20200416_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tariftindakan_m" ADD COLUMN "kamarruangan_id" int4;');

        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" ADD COLUMN "kamarruangan_id" int4;');

        $this->execute('DROP VIEW if exists "public"."masterkamarruangan_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"masterkamarruangan_v\" AS  SELECT kamarruangan_m.ruangan_id,
    kamarruangan_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamarruangan_m.kamarruangan_jenis,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS jenis_kamar,
    kamarruangan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    NULL::text AS status_isi,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.is_dashboard,
    kamarruangan_m.is_active,
    kamarruangan_m.kamarruangan_deskripsi
   FROM (((kamarruangan_m
     JOIN ruangan_m ON (((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id) AND (ruangan_m.is_deleted = false))))
     JOIN jeniskasuspenyakit_m ON (((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id) AND (jeniskasuspenyakit_m.is_deleted = false))))
     JOIN kelaspelayanan_m ON (((kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id) AND (kelaspelayanan_m.is_deleted = false))))
  WHERE (kamarruangan_m.is_deleted = false)
  ORDER BY jeniskasuspenyakit_m.jeniskasuspenyakit_id, ruangan_m.ruangan_id;");

        $this->execute('ALTER TABLE "public"."masterkamarruangan_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."mastertariftindakan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"mastertariftindakan_v\" AS  SELECT 'TINDAKAN'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
    daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama,
    tariftindakan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar
   FROM (((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
     LEFT JOIN kamarruangan_m ON ((tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
  WHERE (tariftindakan_m.is_deleted = false)
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.tipepaket_id AS tindakan_paket_id,
    tipepaket_m.tipepaket_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    tipepaket_m.tipepaket_kode AS daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    NULL::integer AS daftartindakan_id,
    NULL::character varying AS daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    NULL::character varying AS kelompok_nama,
    NULL::text AS ruangan_nama,
    NULL::text AS instalasi_nama,
    NULL::integer AS kamarruangan_id,
    NULL::character varying AS kamar
   FROM ((((((tariftindakan_m
     JOIN tipepaket_m ON ((tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_deleted = false) AND (tariftindakan_m.tarifparent_id IS NULL))
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan_paket,
    tariftindakan_m.tariftindakan_id,
    tariftindakan_m.daftartindakan_id AS tindakan_paket_id,
    daftartindakan_m.daftartindakan_nama AS nama_tindakan_paket,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.is_active,
    tariftindakan_m.komponentarif_id,
    daftartindakan_m.daftartindakan_kode,
    tariftindakan_m.created_date,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    kelompoktindakan_m.kelompoktindakan_nama AS kelompok_nama,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_nama,
    NULL::integer AS kamarruangan_id,
    NULL::character varying AS kamar
   FROM (((((((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     LEFT JOIN kelompoktindakan_m ON ((kelompoktindakan_m.kelompoktindakan_id = daftartindakan_m.kelompoktindakan_id)))
     JOIN tipepaket_m ON ((tipepaket_m.tipepaket_id = tariftindakan_m.tipepaket_id)))
     JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false) AND (daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id))))
     LEFT JOIN ruangan_m ON ((ruangan_m.ruangan_id = paketpelayanan_mp.ruangan_id)))
     LEFT JOIN instalasi_m ON ((instalasi_m.instalasi_id = ruangan_m.instalasi_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_deleted = false) AND (tariftindakan_m.tarifparent_id IS NOT NULL));");

        $this->execute('ALTER TABLE "public"."mastertariftindakan_v" OWNER TO "postgres";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200416_061358_migrate20200416_1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200416_061358_migrate20200416_1 cannot be reverted.\n";

        return false;
    }
    */
}

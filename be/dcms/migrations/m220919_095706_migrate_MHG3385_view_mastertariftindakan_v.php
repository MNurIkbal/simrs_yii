<?php

use yii\db\Migration;

/**
 * Class m220919_095706_migrate_MHG3385_view_mastertariftindakan_v
 */
class m220919_095706_migrate_MHG3385_view_mastertariftindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."mastertariftindakan_v";
        ');

        $this->execute('
            CREATE VIEW "public"."mastertariftindakan_v" AS  
            SELECT \'TINDAKAN\'::text AS jenis_tindakan_paket,
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
                kamarruangan_m.kamarruangan_nokamar AS kamar,
                komponentarif_m.komponentarif_kode,
                tariftindakan_m.persen_penyulit,
                tariftindakan_m.dokter_id,
                pegawai_m.nama_pegawai AS dokter,
                tariftindakan_m.ruangan_id,
                tariftindakan_m.persentase_komponen,
                tariftindakan_m.is_persentase
               FROM tariftindakan_m
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_kode,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama,
                        a.carabayar_id
                       FROM penjamin_m a) penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 JOIN ( SELECT a.perdatarif_id,
                        a.perdanama_sk
                       FROM perdatarif_m a) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 JOIN ( SELECT a.komponentarif_id,
                        a.komponentarif_kode,
                        a.komponentarif_nama
                       FROM komponentarif_m a) komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 LEFT JOIN ( SELECT a.kamarruangan_id,
                        a.kamarruangan_nokamar
                       FROM kamarruangan_m a) kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON tariftindakan_m.dokter_id = pegawai_m.pegawai_id
              WHERE tariftindakan_m.is_deleted = false
            UNION ALL
             SELECT \'PAKET\'::text AS jenis_tindakan_paket,
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
                NULL::character varying AS kamar,
                komponentarif_m.komponentarif_kode,
                tariftindakan_m.persen_penyulit,
                tariftindakan_m.dokter_id,
                pegawai_m.nama_pegawai AS dokter,
                tariftindakan_m.ruangan_id,
                tariftindakan_m.persentase_komponen,
                tariftindakan_m.is_persentase
               FROM tariftindakan_m
                 LEFT JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_kode,
                        a.daftartindakan_nama,
                        a.kelompoktindakan_id
                       FROM daftartindakan_m a) daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN ( SELECT a.kelompoktindakan_id,
                        a.kelompoktindakan_nama
                       FROM kelompoktindakan_m a) kelompoktindakan_m ON kelompoktindakan_m.kelompoktindakan_id = daftartindakan_m.kelompoktindakan_id
                 JOIN ( SELECT a.tipepaket_id,
                        a.tipepaket_nama
                       FROM tipepaket_m a
                      WHERE a.is_deleted = false) tipepaket_m ON tipepaket_m.tipepaket_id = tariftindakan_m.tipepaket_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_m ON ruangan_m.ruangan_id = tariftindakan_m.ruangan_id
                 LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                       FROM instalasi_m a) instalasi_m ON instalasi_m.instalasi_id = ruangan_m.instalasi_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama,
                        a.carabayar_id
                       FROM penjamin_m a) penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT a.perdatarif_id,
                        a.perdanama_sk
                       FROM perdatarif_m a) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                 LEFT JOIN ( SELECT a.komponentarif_id,
                        a.komponentarif_kode,
                        a.komponentarif_nama
                       FROM komponentarif_m a) komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) pegawai_m ON tariftindakan_m.dokter_id = pegawai_m.pegawai_id
              WHERE tariftindakan_m.is_deleted = false;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220919_095706_migrate_MHG3385_view_mastertariftindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220919_095706_migrate_MHG3385_view_mastertariftindakan_v cannot be reverted.\n";

        return false;
    }
    */
}

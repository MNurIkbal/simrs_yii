<?php

use yii\db\Migration;

/**
 * Class m220412_144331_migrate_skema_fisio_tariftotalrstransaksifisio_v
 */
class m220412_144331_migrate_skema_fisio_tariftotalrstransaksifisio_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.tariftotalrstransaksifisio_v;');
        $this->execute("
            CREATE VIEW \"public\".\"tariftotalrstransaksifisio_v\" AS
            SELECT x.jenis,
            x.tariftindakan_id,
            x.ruangan_id,
            x.ruangan_nama,
            x.instalasi_id,
            x.instalasi_nama,
            x.ruanganpaket_id,
            x.ruanganpaket_nama,
            x.perdatarif_id,
            x.perdanama_sk,
            x.kelaspelayanan_id,
            x.kelaspelayanan_nama,
            x.penjamin_id,
            x.penjamin_nama,
            x.kelompoktindakan_id,
            x.kelompoktindakan_nama,
            x.kategoritindakan_id,
            x.kategoritindakan_nama,
            x.daftartindakan_id,
            x.daftartindakan_kode,
            x.daftartindakan_nama,
            x.daftartindakan_detail_id,
            x.daftartindakan_detail_kode,
            x.daftartindakan_detail_nama,
            x.tipepaket_id,
            x.tipepaket_nama,
            x.komponentarif_id,
            x.komponentarif_nama,
            x.harga_tariftindakan,
            x.persencyto_tindakan,
            x.persendiskon_tindakan,
            x.is_default,
            x.is_akomodasi,
            x.carabayar_id,
            x.is_konsultasi,
            x.kamarruangan_nokamar,
            x.kamarruangan_id,
            x.ambulan_id,
            x.no_polisi,
            x.kelompokpemeriksaanfisio_id,
            x.nama_kelompok,
            x.jenispemeriksaanfisio_id,
            x.jenispemeriksaanfisio_nama,
            x.pemeriksaanfisio_id,
            x.pemeriksaanfisio_nama,
            x.persen_penyulit,
            x.kode,
            x.dokter_id,
            x.is_paketfisio,
            x.frekuensi,
            x.jumlah,
            x.is_active,
            x.is_deleted,
            x.is_deleted_detail,
            x.programterapidetail_id
            FROM ( SELECT 'fisio'::text AS jenis,
            COALESCE(tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id,
            tindakanruangan_mp.ruangan_id,
            ruangan_m.ruangan_nama,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id, tarif_konfig.perdatarif_id) AS perdatarif_id,
            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk, tarif_konfig.perdanama_sk) AS perdanama_sk,
            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id, tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama, tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id, tarif_konfig.penjamin_id) AS penjamin_id,
            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama, tarif_konfig.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id, tarif_konfig.komponentarif_id) AS komponentarif_id,
            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama, tarif_konfig.komponentarif_nama) AS komponentarif_nama,
            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tarif_konfig.harga_tariftindakan) AS harga_tariftindakan,
            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tarif_konfig.persencyto_tindakan) AS persencyto_tindakan,
            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan,
            tindakanruangan_mp.is_default,
            daftartindakan_m.is_akomodasi,
            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id, tarif_konfig.carabayar_id) AS carabayar_id,
            daftartindakan_m.is_konsultasi,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::integer AS kamarruangan_id,
            NULL::integer AS ambulan_id,
            NULL::character varying AS no_polisi,
            pemeriksaanfisio_m.kelompokpemeriksaanfisio_id,
            kelompokpemeriksaanfisio_m.nama_kelompok,
            pemeriksaanfisio_m.jenispemeriksaanfisio_id,
            jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
            pemeriksaanfisio_m.pemeriksaanfisio_id,
            pemeriksaanfisio_m.pemeriksaanfisio_nama,
            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit, tarif_konfig.persen_penyulit) AS persen_penyulit,
            daftartindakan_m.daftartindakan_kode AS kode,
            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id, tarif_konfig.dokter_id) AS dokter_id,
            daftartindakan_m.is_paketfisio,
            daftarpaketfisio_m.frekuensi,
            daftarpaketfisio_m.jumlah,
            daftartindakandet_m.daftartindakan_detail_id,
            daftartindakandet_m.daftartindakan_detail_kode,
            daftartindakandet_m.daftartindakan_detail_nama,
            daftartindakan_m.daftartindakan_kode,
            daftartindakan_m.is_active,
            daftartindakan_m.is_deleted,
            daftarpaketfisiodet_m.is_deleted_detail,
            programterapidetail_t.programterapidetail_id
            FROM ((((((((((((((((daftartindakan_m
            JOIN ( SELECT a.parent_id,
            a.frekuensi,
            a.jumlah,
            a.daftarpaketfisio_id
            FROM daftarpaketfisio_m a) daftarpaketfisio_m ON ((daftartindakan_m.daftartindakan_id = daftarpaketfisio_m.parent_id)))
            JOIN ( SELECT a.daftartindakan_id,
            a.programterapidetail_id
            FROM programterapidetail_t a) programterapidetail_t ON ((daftartindakan_m.daftartindakan_id = programterapidetail_t.daftartindakan_id)))
            JOIN ( SELECT a.programterapidetail_id,
            a.daftartindakan_id AS daftartindakandet_id
            FROM programterapidetailpaket_t a) programterapidetailpaket_t ON ((programterapidetail_t.programterapidetail_id = programterapidetailpaket_t.programterapidetail_id)))
            JOIN ( SELECT a.daftartindakan_id AS daftartindakan_detail_id,
            a.daftartindakan_kode AS daftartindakan_detail_kode,
            a.daftartindakan_nama AS daftartindakan_detail_nama,
            a.is_active,
            a.is_deleted
            FROM daftartindakan_m a) daftartindakandet_m ON ((programterapidetailpaket_t.daftartindakandet_id = daftartindakandet_m.daftartindakan_detail_id)))
            LEFT JOIN ( SELECT a.daftarpaketfisio_id,
            a.daftartindakan_id,
            a.is_deleted AS is_deleted_detail
            FROM daftarpaketfisiodet_m a) daftarpaketfisiodet_m ON (((daftarpaketfisio_m.daftarpaketfisio_id = daftarpaketfisiodet_m.daftarpaketfisio_id) AND (daftarpaketfisiodet_m.is_deleted_detail = false))))
            LEFT JOIN ( SELECT 'normal'::text AS tipe,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.tariftindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            tariftindakan_m.penjamin_id,
            penjamin_m.carabayar_id,
            tariftindakan_m.harga_tariftindakan,
            tariftindakan_m.persencyto_tindakan,
            tariftindakan_m.persendiskon_tindakan,
            tariftindakan_m.persen_penyulit,
            tariftindakan_m.perdatarif_id,
            perdatarif_m.perdanama_sk,
            penjamin_m.penjamin_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            tariftindakan_m.dokter_id
            FROM ((((tariftindakan_m
            JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
            JOIN ( SELECT perdatarif_m_1.perdatarif_id,
            perdatarif_m_1.perdanama_sk,
            perdatarif_m_1.perda_no,
            perdatarif_m_1.perda_tgl,
            perdatarif_m_1.perda_tentang,
            perdatarif_m_1.ditetapkan_oleh,
            perdatarif_m_1.tempat_ditetapkan,
            perdatarif_m_1.additional_data,
            perdatarif_m_1.created_date,
            perdatarif_m_1.created_by,
            perdatarif_m_1.modified_count,
            perdatarif_m_1.last_modified_date,
            perdatarif_m_1.last_modified_by,
            perdatarif_m_1.is_deleted,
            perdatarif_m_1.is_active,
            perdatarif_m_1.deleted_date,
            perdatarif_m_1.deleted_by,
            perdatarif_m_1.nama_lainnya
            FROM perdatarif_m perdatarif_m_1
            WHERE ((perdatarif_m_1.is_deleted = false) AND (perdatarif_m_1.is_active = true) AND (perdatarif_m_1.perda_tgl <= now()))
            ORDER BY perdatarif_m_1.perda_tgl DESC
            LIMIT 1) perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
            JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE ((komponentarif_m.is_deleted IS FALSE) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL))) tarif_normal ON ((daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id)))
            LEFT JOIN ( SELECT 'penjamin'::text AS tipe,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.tariftindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            tariftindakan_m.penjamin_id,
            penjamin_m.carabayar_id,
            tariftindakan_m.harga_tariftindakan,
            tariftindakan_m.persencyto_tindakan,
            tariftindakan_m.persendiskon_tindakan,
            tariftindakan_m.persen_penyulit,
            tariftindakan_m.perdatarif_id,
            perdatarif_m.perdanama_sk,
            penjamin_m.penjamin_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            tariftindakan_m.dokter_id
            FROM ((((tariftindakan_m
            JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
            JOIN ( SELECT perdatarif_m_1.perdatarif_id,
            perdatarif_m_1.perdanama_sk,
            perdatarif_m_1.perda_no,
            perdatarif_m_1.perda_tgl,
            perdatarif_m_1.perda_tentang,
            perdatarif_m_1.ditetapkan_oleh,
            perdatarif_m_1.tempat_ditetapkan,
            perdatarif_m_1.additional_data,
            perdatarif_m_1.created_date,
            perdatarif_m_1.created_by,
            perdatarif_m_1.modified_count,
            perdatarif_m_1.last_modified_date,
            perdatarif_m_1.last_modified_by,
            perdatarif_m_1.is_deleted,
            perdatarif_m_1.is_active,
            perdatarif_m_1.deleted_date,
            perdatarif_m_1.deleted_by,
            perdatarif_m_1.nama_lainnya
            FROM perdatarif_m perdatarif_m_1
            WHERE ((perdatarif_m_1.is_deleted = false) AND (perdatarif_m_1.is_active = true) AND (perdatarif_m_1.perda_tgl <= now()))
            ORDER BY perdatarif_m_1.perda_tgl DESC
            LIMIT 1) perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
            JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE ((komponentarif_m.is_deleted IS FALSE) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL))) tarif_penjamin ON (((daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id) AND (tarif_normal.tariftindakan_id IS NULL))))
            LEFT JOIN ( SELECT 'kelas'::text AS tipe,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.tariftindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            tariftindakan_m.penjamin_id,
            penjamin_m.carabayar_id,
            tariftindakan_m.harga_tariftindakan,
            tariftindakan_m.persencyto_tindakan,
            tariftindakan_m.persendiskon_tindakan,
            tariftindakan_m.persen_penyulit,
            tariftindakan_m.perdatarif_id,
            perdatarif_m.perdanama_sk,
            penjamin_m.penjamin_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            tariftindakan_m.dokter_id
            FROM ((((tariftindakan_m
            JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
            JOIN ( SELECT perdatarif_m_1.perdatarif_id,
            perdatarif_m_1.perdanama_sk,
            perdatarif_m_1.perda_no,
            perdatarif_m_1.perda_tgl,
            perdatarif_m_1.perda_tentang,
            perdatarif_m_1.ditetapkan_oleh,
            perdatarif_m_1.tempat_ditetapkan,
            perdatarif_m_1.additional_data,
            perdatarif_m_1.created_date,
            perdatarif_m_1.created_by,
            perdatarif_m_1.modified_count,
            perdatarif_m_1.last_modified_date,
            perdatarif_m_1.last_modified_by,
            perdatarif_m_1.is_deleted,
            perdatarif_m_1.is_active,
            perdatarif_m_1.deleted_date,
            perdatarif_m_1.deleted_by,
            perdatarif_m_1.nama_lainnya
            FROM perdatarif_m perdatarif_m_1
            WHERE ((perdatarif_m_1.is_deleted = false) AND (perdatarif_m_1.is_active = true) AND (perdatarif_m_1.perda_tgl <= now()))
            ORDER BY perdatarif_m_1.perda_tgl DESC
            LIMIT 1) perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
            JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE ((komponentarif_m.is_deleted IS FALSE) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL))) tarif_kelas ON (((daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id) AND (tarif_penjamin.tariftindakan_id IS NULL) AND (tarif_normal.tariftindakan_id IS NULL))))
            LEFT JOIN ( SELECT 'global'::text AS tipe,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.tariftindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            tariftindakan_m.penjamin_id,
            penjamin_m.carabayar_id,
            tariftindakan_m.harga_tariftindakan,
            tariftindakan_m.persencyto_tindakan,
            tariftindakan_m.persendiskon_tindakan,
            tariftindakan_m.persen_penyulit,
            tariftindakan_m.perdatarif_id,
            perdatarif_m.perdanama_sk,
            penjamin_m.penjamin_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            tariftindakan_m.dokter_id
            FROM ((((tariftindakan_m
            JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
            JOIN ( SELECT perdatarif_m_1.perdatarif_id,
            perdatarif_m_1.perdanama_sk,
            perdatarif_m_1.perda_no,
            perdatarif_m_1.perda_tgl,
            perdatarif_m_1.perda_tentang,
            perdatarif_m_1.ditetapkan_oleh,
            perdatarif_m_1.tempat_ditetapkan,
            perdatarif_m_1.additional_data,
            perdatarif_m_1.created_date,
            perdatarif_m_1.created_by,
            perdatarif_m_1.modified_count,
            perdatarif_m_1.last_modified_date,
            perdatarif_m_1.last_modified_by,
            perdatarif_m_1.is_deleted,
            perdatarif_m_1.is_active,
            perdatarif_m_1.deleted_date,
            perdatarif_m_1.deleted_by,
            perdatarif_m_1.nama_lainnya
            FROM perdatarif_m perdatarif_m_1
            WHERE ((perdatarif_m_1.is_deleted = false) AND (perdatarif_m_1.is_active = true) AND (perdatarif_m_1.perda_tgl <= now()))
            ORDER BY perdatarif_m_1.perda_tgl DESC
            LIMIT 1) perdatarif_m ON ((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id)))
            JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            WHERE ((komponentarif_m.is_deleted IS FALSE) AND (tariftindakan_m.is_deleted = false) AND (tariftindakan_m.is_active = true) AND (tariftindakan_m.tarifparent_id IS NULL))) tarif_konfig ON (((daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id) AND (tarif_penjamin.tariftindakan_id IS NULL) AND (tarif_normal.tariftindakan_id IS NULL) AND (tarif_kelas.tariftindakan_id IS NULL))))
            LEFT JOIN pemeriksaanfisio_m ON (((daftartindakan_m.daftartindakan_id = pemeriksaanfisio_m.daftartindakan_id) AND (pemeriksaanfisio_m.is_deleted = false))))
            LEFT JOIN ( SELECT a.tipepaket_id,
            a.tipepaket_nama
            FROM tipepaket_m a) tipepaket_m ON ((pemeriksaanfisio_m.tipepaket_id = tipepaket_m.tipepaket_id)))
            LEFT JOIN jenispemeriksaanfisio_m ON ((pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id)))
            LEFT JOIN kelompokpemeriksaanfisio_m ON ((pemeriksaanfisio_m.kelompokpemeriksaanfisio_id = kelompokpemeriksaanfisio_m.kelompokpemeriksaanfisio_id)))
            JOIN tindakanruangan_mp ON ((daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id)))
            JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            WHERE (((tindakanruangan_mp.is_deleted = false) AND (tarif_normal.tariftindakan_id IS NOT NULL)) OR (tarif_penjamin.tariftindakan_id IS NOT NULL) OR (tarif_kelas.tariftindakan_id IS NOT NULL) OR (tarif_konfig.tariftindakan_id IS NOT NULL))) x
            WHERE (x.komponentarif_id = 6)
            GROUP BY x.jenis, x.tariftindakan_id, x.ruangan_id, x.ruangan_nama, x.instalasi_id, x.instalasi_nama, x.ruanganpaket_id, x.ruanganpaket_nama, x.perdatarif_id, x.perdanama_sk, x.kelaspelayanan_id, x.kelaspelayanan_nama, x.penjamin_id, x.penjamin_nama, x.kelompoktindakan_id, x.kelompoktindakan_nama, x.kategoritindakan_id, x.kategoritindakan_nama, x.daftartindakan_id, x.daftartindakan_nama, x.tipepaket_id, x.tipepaket_nama, x.komponentarif_id, x.komponentarif_nama, x.harga_tariftindakan, x.persencyto_tindakan, x.persendiskon_tindakan, x.is_default, x.is_akomodasi, x.carabayar_id, x.is_konsultasi, x.kamarruangan_nokamar, x.kamarruangan_id, x.ambulan_id, x.no_polisi, x.nama_kelompok, x.persen_penyulit, x.kode, x.dokter_id, x.kelompokpemeriksaanfisio_id, x.jenispemeriksaanfisio_id, x.jenispemeriksaanfisio_nama, x.pemeriksaanfisio_id, x.pemeriksaanfisio_nama, x.is_paketfisio, x.frekuensi, x.jumlah, x.daftartindakan_detail_id, x.daftartindakan_detail_nama, x.daftartindakan_kode, x.daftartindakan_detail_kode, x.is_active, x.is_deleted, x.is_deleted_detail, x.programterapidetail_id
            ;");
        $this->execute('
            ALTER TABLE public.tariftotalrstransaksifisio_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220412_144331_migrate_skema_fisio_tariftotalrstransaksifisio_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220412_144331_migrate_skema_fisio_tariftotalrstransaksifisio_v cannot be reverted.\n";

        return false;
    }
    */
}

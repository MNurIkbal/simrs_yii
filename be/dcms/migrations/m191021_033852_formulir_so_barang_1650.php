<?php

use yii\db\Migration;

/**
 * Class m191021_033852_formulir_so_barang_1650
 */
class m191021_033852_formulir_so_barang_1650 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infostokopnamebarang_v;');

        $this->execute('DROP VIEW if exists public.infostokopnamebarangdetail_v;');

        $this->execute('DROP VIEW if exists public.infoformsobarang_v;');

        $this->execute('DROP VIEW if exists public.infoformsobarangdetail_v;');

        $this->execute('DROP VIEW if exists public.laporanformsobarang_v;');

        $this->execute('ALTER TABLE "public"."formsobarang_t" 
                        ALTER COLUMN "tglformulir" TYPE timestamp(0) USING "tglformulir"::timestamp(0);
                        ');

        $this->execute('ALTER TABLE "public"."stokopnamebarang_t" RENAME COLUMN "petugas_id" TO "pegawai_id";');

        $this->execute('ALTER TABLE "public"."stokopnamebarang_t" 
                        DROP COLUMN "is_stokawal";');

/*infostokopnamebarang_v*/
        $this->execute('
            CREATE OR REPLACE VIEW public.infostokopnamebarang_v AS 
 SELECT stokopnamebarang_t.stokopnamebarang_id,
    stokopnamebarang_t.formsobarang_id,
    stokopnamebarang_t.ruangan_id,
    stokopnamebarang_t.pegmengetahui_id,
    stokopnamebarang_t.pegawai_id AS petugas_id,
    ruangan_m.instalasi_id,
    stokopnamebarang_t.jenisstokopname,
    instalasi_m.instalasi_nama,
    fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_stokopname,
    ruangan_m.ruangan_nama,
    stokopnamebarang_t.tglstokopname,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.totalharga_fisik,
    stokopnamebarang_t.totalharga_sistem,
    stokopnamebarang_t.totalharga_sistem - stokopnamebarang_t.totalharga_fisik AS selisih,
    formsobarang_t.noformulir,
    formsobarang_t.tglformulir,
    sum(stokopnamebarangdetail_t.volume_fisik) AS stok_fisik,
    sum(stokopnamebarangdetail_t.volume_sistem) AS stok_sistem,
    sum(stokopnamebarangdetail_t.volume_fisik) - sum(stokopnamebarangdetail_t.volume_sistem) AS stok_selisih
   FROM stokopnamebarang_t
     JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON stokopnamebarang_t.pegmengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_petugas ON stokopnamebarang_t.pegawai_id = pegawai_petugas.pegawai_id
     LEFT JOIN formsobarang_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
     LEFT JOIN formsobarangdetail_t ON stokopnamebarang_t.formsobarang_id = formsobarang_t.formsobarang_id
     LEFT JOIN stokopnamebarangdetail_t ON stokopnamebarang_t.stokopnamebarang_id = stokopnamebarangdetail_t.stokopnamebarang_id
  WHERE stokopnamebarang_t.is_deleted = false AND stokopnamebarang_t.is_active = true
  GROUP BY stokopnamebarang_t.stokopnamebarang_id, stokopnamebarang_t.formsobarang_id, stokopnamebarang_t.ruangan_id, stokopnamebarang_t.pegmengetahui_id, stokopnamebarang_t.pegawai_id, ruangan_m.instalasi_id, stokopnamebarang_t.jenisstokopname, instalasi_m.instalasi_nama, (fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer)), ruangan_m.ruangan_nama, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.nostokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, formsobarang_t.noformulir, formsobarang_t.tglformulir;
  ');

        $this->execute('ALTER TABLE public.infostokopnamebarang_v
  OWNER TO postgres;');

/*infostokopnamebarangdetail_v*/
        $this->execute("
            CREATE OR REPLACE VIEW public.infostokopnamebarangdetail_v AS 
 SELECT stokopnamebarangdetail_t.stokopnamebarangdetail_id,
    stokopnamebarangdetail_t.stokopnamebarang_id,
    stokopnamebarangdetail_t.barang_id,
    stokopnamebarang_t.ruangan_id,
    stokopnamebarang_t.pegmengetahui_id,
    stokopnamebarang_t.pegawai_id AS petugas_id,
    ruangan_m.ruangan_nama,
    pegawai_mengetahui.nama_pegawai AS pegawai_mengetahui,
    pegawai_petugas.nama_pegawai AS pegawai_petugas,
    stokopnamebarang_t.nostokopname,
    barang_m.barang_nama,
    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
    stokopnamebarangdetail_t.volume_fisik,
    stokopnamebarangdetail_t.volume_sistem,
    stokopnamebarangdetail_t.harganetto,
    stokopnamebarangdetail_t.kondisibarang,
    stokopnamebarangdetail_t.tglkadaluarsa
   FROM stokopnamebarangdetail_t
     JOIN stokopnamebarang_t ON stokopnamebarangdetail_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
     JOIN ruangan_m ON stokopnamebarang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pegawai_m pegawai_mengetahui ON stokopnamebarang_t.pegmengetahui_id = pegawai_mengetahui.pegawai_id
     LEFT JOIN pegawai_m pegawai_petugas ON stokopnamebarang_t.pegawai_id = pegawai_petugas.pegawai_id
     JOIN barang_m ON stokopnamebarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
  WHERE stokopnamebarangdetail_t.is_deleted = false AND stokopnamebarangdetail_t.is_active = true;");

        $this->execute('ALTER TABLE public.infostokopnamebarangdetail_v
  OWNER TO postgres;');

/*infoformsobarang_v*/
        $this->execute("
            CREATE OR REPLACE VIEW public.infoformsobarang_v AS 
 SELECT formsobarang_t.formsobarang_id,
    formsobarang_t.stokopnamebarang_id,
    formsobarang_t.ruangan_id,
    ruangan_m.instalasi_id,
    formsobarang_t.tglformulir,
    formsobarang_t.noformulir,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    formsobarang_t.total_harganetto,
    stokopnamebarang_t.jenisstokopname,
    fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_stokopname,
    stokopnamebarang_t.totalharga_fisik,
    stokopnamebarang_t.totalharga_sistem
   FROM formsobarang_t
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN stokopnamebarang_t ON formsobarang_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id
  WHERE formsobarang_t.is_active = true AND formsobarang_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infoformsobarang_v
  OWNER TO postgres;');

/*infoformsobarangdetail_v*/
        $this->execute("
            CREATE OR REPLACE VIEW public.infoformsobarangdetail_v AS 
 SELECT formsobarangdetail_t.formsobarangdetail_id,
    formsobarangdetail_t.formsobarang_id,
    formsobarangdetail_t.barang_id,
    kelompokbarang_m.kelompokbarang_id,
    kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
    subkelompokbarang_m.subkelompokbarang_id,
    subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
    formsobarang_t.ruangan_id,
    ruangan_m.instalasi_id,
    formsobarang_t.noformulir,
    formsobarang_t.tglformulir,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    barang_m.barang_nama,
    formsobarangdetail_t.stok,
    formsobarangdetail_t.nobatch,
    formsobarangdetail_t.stokbarang_id,
    stokopnamebarangdetail_t.volume_fisik,
    stokopnamebarangdetail_t.volume_sistem,
    stokopnamebarangdetail_t.kondisibarang,
    stokopnamebarangdetail_t.jmlselisihstok,
    formsobarangdetail_t.tgl_kadaluarsa
   FROM formsobarangdetail_t
     JOIN formsobarang_t ON formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id
     JOIN barang_m ON formsobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN stokopnamebarangdetail_t ON formsobarangdetail_t.stokopnamebarangdetail_id = stokopnamebarangdetail_t.stokopnamebarangdetail_id
  WHERE formsobarangdetail_t.is_deleted = false AND formsobarangdetail_t.is_active = true;");

        $this->execute('ALTER TABLE public.infoformsobarangdetail_v
  OWNER TO postgres;');

/*laporanformsobarang_v*/
        $this->execute("
            CREATE OR REPLACE VIEW public.laporanformsobarang_v AS 
 SELECT formsobarang_t.formsobarang_id,
    formsobarang_t.stokopnamebarang_id,
    formsobarang_t.ruangan_id,
    ruangan_m.instalasi_id,
    formsobarang_t.tglformulir,
    formsobarang_t.noformulir,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.tglstokopname,
    stokopnamebarang_t.totalharga_fisik,
    fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer) AS jenis_so,
    stokopnamebarang_t.totalharga_sistem,
    formsobarang_t.total_harganetto
   FROM formsobarang_t
     LEFT JOIN stokopnamebarang_t ON stokopnamebarang_t.stokopnamebarang_id = formsobarang_t.stokopnamebarang_id
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN formsobarangdetail_t ON formsobarang_t.formsobarang_id = formsobarangdetail_t.formsobarang_id
  WHERE formsobarang_t.is_active = true AND formsobarang_t.is_deleted = false
  GROUP BY formsobarang_t.formsobarang_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, stokopnamebarang_t.nostokopname, stokopnamebarang_t.tglstokopname, stokopnamebarang_t.totalharga_fisik, stokopnamebarang_t.totalharga_sistem, (fgetnamalookup(stokopnamebarang_t.jenisstokopname::integer)), formsobarang_t.stokopnamebarang_id, formsobarang_t.ruangan_id, formsobarang_t.tglformulir, formsobarang_t.noformulir, formsobarang_t.total_harganetto;
");

        $this->execute('ALTER TABLE public.laporanformsobarang_v
  OWNER TO postgres;');


/*detailformulirstokopname_v*/
        $this->execute('DROP VIEW if exists public.detailformulirstokopname_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.detailformulirstokopname_v AS 
 SELECT formstokopname_t.formstokopname_id,
    formstokopname_t.formulirstokopname_id,
    formstokopname_t.volume_stok AS stok_sistem,
    formstokopname_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    obatalkes_m.obatalkes_nama,
    formstokopname_t.nobatch,
        CASE
            WHEN stokobatalkes_t.tglkadaluarsa IS NULL THEN formstokopname_t.tglkadaluarsa
            ELSE stokobatalkes_t.tglkadaluarsa
        END AS tglkadaluarsa,
    fgethargajualobat(obatalkes_m.obatalkes_id) AS hargajual,
    obatalkes_m.harganetto,
    formstokopname_t.stokobatalkes_id
   FROM formstokopname_t
     JOIN obatalkes_m ON formstokopname_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN stokobatalkes_t ON formstokopname_t.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
  WHERE formstokopname_t.is_active = true AND formstokopname_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.detailformulirstokopname_v
  OWNER TO postgres;');

/*infostokbarangdetail_v*/
        $this->execute('DROP VIEW if exists public.infostokbarangdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infostokbarangdetail_v AS 
 SELECT proses.barang_id,
    sum(proses.qtystok_in) - sum(proses.qtystok_out) AS stok_sistem,
    proses.barang_nama,
    proses.kelompokbarang_id,
    proses.kelompok_barang,
    proses.subkelompokbarang_id,
    proses.subkelompok_barang,
    proses.nobatch,
    proses.tglkadaluarsa,
    proses.barang_harganetto,
    proses.instalasi_nama,
    proses.ruangan_nama,
    proses.periodestokbarang_id,
    proses.tglperiodestok_awal,
    proses.tglperiodestok_akhir,
    proses.ruangan_id,
    proses.instalasi_id,
    proses.sop_barang_id,
    proses.sop_sopbarangdetail_id,
    proses.id_stok,
    proses.id_stok AS stokbarang_id
   FROM ( SELECT
                CASE
                    WHEN stokbarang_t.stokbarangasal_id IS NULL THEN stokbarang_t.stokbarang_id
                    ELSE stokbarang_t.stokbarangasal_id
                END AS id_stok,
            stokbarang_t.barang_id,
            stokbarang_t.qtystok_in,
            stokbarang_t.qtystok_out,
            stokbarang_t.nobatch,
            stokbarang_t.tglkadaluarsa,
            barang_m.barang_nama,
            subkelompokbarang_m.subkelompokbarang_id,
            subkelompokbarang_m.subkelompok_nama AS subkelompok_barang,
            kelompokbarang_m.kelompokbarang_id,
            kelompokbarang_m.kelompokbarang_nama AS kelompok_barang,
            barang_m.barang_harganetto,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            stokbarang_r.periodestokbarang_id,
            periodestokbarang_m.tglperiodestok_awal,
            periodestokbarang_m.tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            formsobarangdetail_t.barang_id AS sop_barang_id,
            formsobarangdetail_t.stokopnamebarangdetail_id AS sop_sopbarangdetail_id,
            stokbarang_t.stokbarang_id
           FROM stokbarang_t
             JOIN barang_m ON stokbarang_t.barang_id = barang_m.barang_id
             LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
             LEFT JOIN subkelompokbarang_m ON barang_m.subkelompokbarang_id = subkelompokbarang_m.subkelompokbarang_id
             JOIN ruangan_m ON stokbarang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN formsobarangdetail_t ON
                CASE
                    WHEN stokbarang_t.stokbarangasal_id IS NULL THEN stokbarang_t.stokbarang_id
                    ELSE stokbarang_t.stokbarangasal_id
                END = formsobarangdetail_t.stokbarang_id AND stokbarang_t.ruangan_id = formsobarangdetail_t.ruangan_id
             JOIN stokbarang_r ON stokbarang_t.barang_id = stokbarang_r.barang_id AND stokbarang_t.ruangan_id = stokbarang_r.ruangan_id
             LEFT JOIN periodestokbarang_m ON stokbarang_r.periodestokbarang_id = periodestokbarang_m.periodestokbarang_id
          WHERE stokbarang_t.stokbarang_aktif = true AND (formsobarangdetail_t.barang_id IS NOT NULL AND formsobarangdetail_t.stokopnamebarangdetail_id IS NOT NULL OR formsobarangdetail_t.barang_id IS NULL AND formsobarangdetail_t.stokopnamebarangdetail_id IS NULL)) proses
  GROUP BY proses.barang_id, proses.barang_nama, proses.kelompokbarang_id, proses.kelompok_barang, proses.subkelompokbarang_id, proses.subkelompok_barang, proses.nobatch, proses.tglkadaluarsa, proses.barang_harganetto, proses.instalasi_nama, proses.ruangan_nama, proses.periodestokbarang_id, proses.tglperiodestok_awal, proses.tglperiodestok_akhir, proses.ruangan_id, proses.instalasi_id, proses.sop_barang_id, proses.sop_sopbarangdetail_id, proses.id_stok;
");

        $this->execute('ALTER TABLE public.infostokbarangdetail_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191021_033852_formulir_so_barang_1650 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191021_033852_formulir_so_barang_1650 cannot be reverted.\n";

        return false;
    }
    */
}

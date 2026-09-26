<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Akses Pengguna</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    <table border="1" cellspadding="10">
        <tr>
            <th>No</th>
            <th>Peran Pengguna Nama</th>
            <th>Hak Akses</th>
        </tr>
        <?php
            $no = 1;
            foreach ($detail as $key => $value) { ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= $value['peran_pengguna'] ?></td>
                    <td><?= $value['hak_akses'] ?></td>
                </tr>
        <?php
            $no++;
            }
        ?>
    </table>
</body>
</html>
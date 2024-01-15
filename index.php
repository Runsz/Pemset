<?php 
    include 'koneksi.php';

    $query = "SELECT tp.id, u.nama, a.nama_aset, tp.jml_pinjam, tp.tgl_pinjam, tp.tgl_rencana_kembali, tp.tgl_kembali, tp.peruntukkan, s.status, s.id sts_id
                FROM `transaksi_peminjaman` tp JOIN asets a ON tp.aset_id = a.id JOIN users u ON tp.user_id=u.id JOIN statuses s ON tp.status_id=s.id";
    $sql = mysqli_query($db, $query);
    $peminjaman = mysqli_fetch_all($sql, MYSQLI_ASSOC);

    for ($i=0; $i < count($peminjaman); $i++) { 
        $today = date('Y-m-d');
        if ($peminjaman[$i]['tgl_rencana_kembali'] < $today && $peminjaman[$i]['status'] != 'sudah dikembalikan' && $peminjaman[$i]['sts_id'] != 2) {
            $id = $peminjaman[$i]['id'];
            $status_id = 2;

            $query = "UPDATE transaksi_peminjaman SET status_id=$status_id WHERE id=$id";
            $sql = mysqli_query($db, $query);

            $query = "SELECT tp.id, u.nama, a.nama_aset, tp.jml_pinjam, tp.tgl_pinjam, tp.tgl_rencana_kembali, tp.tgl_kembali, tp.peruntukkan, s.status, s.id sts_id
                        FROM `transaksi_peminjaman` tp JOIN asets a ON tp.aset_id = a.id JOIN users u ON tp.user_id=u.id JOIN statuses s ON tp.status_id=s.id";
            $sql = mysqli_query($db, $query);
            $peminjaman = mysqli_fetch_all($sql, MYSQLI_ASSOC);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pencatatan Peminjaman Aset</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <script src="script.js"></script>
    <div class="container">
        <header class="header">
            <h1 class="judul">PemSet</h1>
            <form class="search" method="post" action="index.php">
                <input placeholder="   Search" type="text">
                <button type="" class="btn">Cari</button>
            </form>
        </header>
        
        <div class="add">
            <div class="btn-add">
                <a href="form-pinjam.php?sudah"><button class="btn">+Tambah peminjaman</button></a>
            </div>
            <div class="sortir">
                <p>Urutkan Berdasarkan</p>
                <?php 
                    if (isset($_POST['sort'])) {
                        $key = $_POST['key'];
                
                        function sorting(&$arr, $key){
                            $length = count($arr);
                
                            for ($i=0; $i < $length; $i++) { 
                                $swap = false;
                                for ($j=0; $j < $length - $i - 1; $j++) { 
                                    if ($arr[$j][$key] > $arr[$j+1][$key]) {
                                        $sementara = $arr[$j];
                                        $arr[$j] = $arr[$j+1];
                                        $arr[$j+1] = $sementara;
                                        $swap = true;
                                    }
                                }
                                if ($swap == false) {
                                    break;
                                }
                            }
                        }
                
                        sorting($peminjaman, $key); ?>

                        <form action="index.php" method="post" class="">
                            <select class="sort-item" name="key" id="">
                                <option value="tgl_pinjam" <?php echo $key == 'tgl_pinjam' ? 'selected' : '' ?>>Tanggal</option>
                                <option value="jml_pinjam" <?php echo $key == 'jml_pinjam' ? 'selected' : '' ?>>Jumlah</option>
                                <option value="sts_id" <?php echo $key == 'sts_id' ? 'selected' : '' ?>>Status</option>
                            </select>
                            <button class="btn btn-sort" name="sort" value="sort" type="submit">Urutkan</button>
                        </form>
                    <?php }
                    else { ?>
                        <form action="index.php" method="post">
                            <select class="sort-item" name="key" id="">
                                <option value="tgl_pinjam">Tanggal</option>
                                <option value="jml_pinjam">Jumlah</option>
                                <option value="sts_id">Status</option>
                            </select>
                            <button class="btn btn-sort" name="sort" value="sort" type="submit">Urutkan</button>
                        </form>
                    <?php }
                ?>
            </div>
        </div>

        <main>
            <h2>Riwayat Peminjaman Aset</h2><br>
            <table>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Nama Aset</th>
                    <th>Jumlah</th>
                    <th>Tanggal peminjaman</th>
                    <th>Estimasi Pengembalian</th>
                    <th>Tanggal Pengembalian</th>
                    <th>Kegunaan</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php 
                    for ($i=0; $i < count($peminjaman); $i++) { 
                        $no=$i+1;
                        echo '
                            <tr>
                                <td>'.$no.'</td>
                                <td>'.$peminjaman[$i]['nama'].'</td>
                                <td>'.$peminjaman[$i]['nama_aset'].'</td>
                                <td>'.$peminjaman[$i]['jml_pinjam'].'</td>
                                <td>'.$peminjaman[$i]['tgl_pinjam'].'</td>
                                <td>'.$peminjaman[$i]['tgl_rencana_kembali'].'</td>
                                <td>'.$peminjaman[$i]['tgl_kembali'].'</td>
                                <td>'.$peminjaman[$i]['peruntukkan'].'</td>
                                <td>'.$peminjaman[$i]['status'].'</td>
                                <td><a href="edit-status.php?id='.$peminjaman[$i]['id'].'"><button class="btn btn-edit" type="button">Edit Status</button></a></td>
                            </tr>
                        ';
                    }
                ?>
            </table>
        </main>
    </div>
</body>
</html>
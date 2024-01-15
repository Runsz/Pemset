<?php
    include 'koneksi.php';

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    }
    else{
        $id = $_POST['id'];
    }

    $query = "SELECT tp.id, a.id AS aset_id, u.nama, a.nama_aset, tp.jml_pinjam, tp.tgl_pinjam, tp.tgl_rencana_kembali, tp.tgl_kembali, tp.peruntukkan, s.status, s.id AS status_id
                FROM `transaksi_peminjaman` tp JOIN asets a ON tp.aset_id = a.id JOIN users u ON tp.user_id=u.id JOIN statuses s ON tp.status_id=s.id 
                WHERE tp.id=$id";
    $sql = mysqli_query($db, $query);
    $peminjaman = mysqli_fetch_array($sql);

    if (isset($_POST['edit'])) {
        $status_id = $_POST['status'];
        $aset_id = $peminjaman['aset_id'];
        $jml = $peminjaman['jml_pinjam'];

            if ($peminjaman['status_id'] == 3) {
                if ($status_id != 3) {
                    $query = 'SELECT * FROM asets WHERE id='.$aset_id.' ';
                    $sql = mysqli_query($db, $query);
                    $aset = mysqli_fetch_array($sql);

                    $jml_aset = $aset['jml_aset'] - $jml;

                    $query = "UPDATE asets SET jml_aset=".$jml_aset." WHERE id = ".$aset_id." ";
                    $sql = mysqli_query($db, $query);

                    $query = "UPDATE transaksi_peminjaman SET status_id=$status_id, tgl_kembali=null WHERE id=$id";
                    $sql = mysqli_query($db, $query);

                    if( $sql ) {
                        header('Location: index.php?edit=sukses');
                    }
                }
                else{
                    $query = "UPDATE transaksi_peminjaman SET status_id=$status_id WHERE id=$id";
                    $sql = mysqli_query($db, $query);

                    if( $sql ) {
                        header('Location: index.php?edit=sukses');
                    }
                }
            }
            else {
                if ($status_id == 3) {
                    $query = 'SELECT * FROM asets WHERE id='.$aset_id.' ';
                    $sql = mysqli_query($db, $query);
                    $aset = mysqli_fetch_array($sql);
                    $today = date('Y-m-d');
                    echo $today;

                    $jml_aset = $aset['jml_aset'] + $jml;

                    $query = "UPDATE asets SET jml_aset=".$jml_aset." WHERE id = ".$aset_id." ";
                    $sql = mysqli_query($db, $query);

                    $query = "UPDATE transaksi_peminjaman SET status_id=$status_id, tgl_kembali='$today' WHERE id=$id";
                    $sql = mysqli_query($db, $query);

                    if( $sql ) {
                        header('Location: index.php?edit=sukses');
                    }
                }
                else{
                    $query = "UPDATE transaksi_peminjaman SET status_id=$status_id WHERE id=$id";
                    $sql = mysqli_query($db, $query);

                    if( $sql ) {
                        header('Location: index.php?edit=sukses');
                    }
                }
            }
        
    }

    $query = "SELECT * FROM statuses";
    $sql = mysqli_query($db, $query);
    $statuses = mysqli_fetch_all($sql, MYSQLI_ASSOC);
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
    <div class="container-form-pinjam">
        <h1>Edit Status Peminjaman Aset</h1>
        <form action="edit-status.php" method="post">
            <input name="id" value="<?php echo $id ?>" type="hidden">
            <label for="">Peminjam</label>
            <input type="text" value="<?php echo $peminjaman['nama'] ?>" disabled><br>
            <label for="">Nama Aset</label>
            <input type="text" value="<?php echo $peminjaman['nama_aset'] ?>" disabled><br>
            <label for="">Jumlah</label>
            <input type="number"  value="<?php echo $peminjaman['jml_pinjam'] ?>" disabled><br>
            <label for="">Tanggal Pinjam</label>
            <input type="date"  value="<?php echo $peminjaman['tgl_pinjam'] ?>" disabled><br>
            <label for="">Estimasi Pengembalian</label>
            <input type="date" value="<?php echo $peminjaman['tgl_rencana_kembali'] ?>" disabled><br>
            <label for="">Kegunaan</label>
            <input type="text" value="<?php echo $peminjaman['peruntukkan'] ?>" disabled><br>
            <label for="">Status</label>
            <select name="status" id="">
                <?php for ($i=0; $i < count($statuses); $i++) { ?>
                    <option value="<?php echo $statuses[$i]['id'] ?>"><?php echo $statuses[$i]['status'] ?></option>
                <?php } ?>
            </select>
            <div class="button">
                <a href="index.php"><button class="btn btn-cancel" type="button">Cancel</button></a>
                <button class="btn btn-simpan" name="edit" value="edit" type="submit">Edit</button>
            </div>
        </form>
    </div>
</body>
</html>
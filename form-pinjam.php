<?php
    include 'koneksi.php';

    $status_penambahan = null;

    if (isset($_POST['simpan'])) {
        $status = $_POST['status'];
        
        if ($status == "sudah") {
            $aset_id = (int)$_POST['aset'];
            $user = $_POST['nama'];
            $sts_pinjam = 1;
            $jml = (int)$_POST['jml_pinjam'];
            $tgl_pinjam = date('Y-m-d');
            $tgl_rencana_kembali = $_POST['rencana_kembali'];
            $peruntukkan = $_POST['peruntukkan'];

            $query = "SELECT * FROM transaksi_peminjaman WHERE user_id = $user AND status_id != 3";
            $sql = mysqli_query($db, $query);
            $user_pinjam = mysqli_fetch_all($sql, MYSQLI_ASSOC);

            $query = 'SELECT * FROM asets WHERE id='.$aset_id.' ';
            $sql = mysqli_query($db, $query);
            $aset = mysqli_fetch_array($sql, MYSQLI_ASSOC);

            if ($user_pinjam != NULL) {
                //header('Location: form-pinjam.php?sudah&tambah=gagal');
                //echo 'User ini sedang meminjam aset';
                $status_penambahan = "*User ini sedang meminjam aset";
            }
            else{
                if ($jml > $aset['jml_aset']) {
                    $status_penambahan = "*jumlah pinjam melebihi jumlah aset";
                    //echo 'jumlah pinjam melebihi jumlah aset';
                    //header('Location: form-pinjam.php?sudah&tambah=gagal');
                }
                else {
                    $query = "INSERT INTO `transaksi_peminjaman`
                            (`aset_id`, `user_id`, `status_id`, `jml_pinjam`, `tgl_pinjam`, `tgl_rencana_kembali`, `peruntukkan`) 
                            VALUES ($aset_id,$user,$sts_pinjam,$jml,'$tgl_pinjam','$tgl_rencana_kembali','$peruntukkan')";
                    $sql = mysqli_query($db, $query);

                    $jml_aset = $aset['jml_aset'] - $jml;
                    $query = "UPDATE asets SET jml_aset=".$jml_aset." WHERE id = ".$aset_id." ";
                    $sql = mysqli_query($db, $query);

                    if( $sql ) {
                        header('Location: index.php?tambah=sukses');
                    }
                }
            }
        }
        else if($status == "belum"){
            $nama = $_POST['nama'];
            $instansi = $_POST['instansi'];
            $bagian = $_POST['bagian'];

            $aset_id = (int)$_POST['aset'];
            $sts_pinjam = 1;
            $jml = (int)$_POST['jml_pinjam'];
            $tgl_pinjam = date('Y-m-d');
            $tgl_rencana_kembali = $_POST['rencana_kembali'];
            $peruntukkan = $_POST['peruntukkan'];

            $query = 'SELECT * FROM asets WHERE id='.$aset_id.' ';
            $sql = mysqli_query($db, $query);
            $aset = mysqli_fetch_array($sql, MYSQLI_ASSOC);

            if ($jml > $aset['jml_aset']) {
                $status_penambahan = "*jumlah pinjam melebihi jumlah aset";
                //echo 'jumlah pinjam melebihi jumlah aset';
                //header('Location: form-pinjam.php?belum&tambah=gagal');
            }
            else {
                $query = "INSERT INTO `users`(`nama`, `instansi`, `bagian`) 
                            VALUES ('$nama','$instansi','$bagian')";
                $sql = mysqli_query($db, $query);
                $user_id = mysqli_insert_id($db);

                $query = "INSERT INTO `transaksi_peminjaman`
                        (`aset_id`, `user_id`, `status_id`, `jml_pinjam`, `tgl_pinjam`, `tgl_rencana_kembali`, `peruntukkan`) 
                        VALUES ($aset_id,$user_id,$sts_pinjam,$jml,'$tgl_pinjam','$tgl_rencana_kembali','$peruntukkan')";
                $sql = mysqli_query($db, $query);

                $jml_aset = $aset['jml_aset'] - $jml;
                $query = "UPDATE asets SET jml_aset=".$jml_aset." WHERE id = ".$aset_id." ";
                $sql = mysqli_query($db, $query);

                if( $sql ) {
                    header('Location: index.php?tambah=sukses');
                }
            }
        }
    }

    $query = 'SELECT * FROM asets';
    $sql = mysqli_query($db, $query);
    $asets = mysqli_fetch_all($sql, MYSQLI_ASSOC);

    $query = 'SELECT * FROM users';
    $sql = mysqli_query($db, $query);
    $users = mysqli_fetch_all($sql, MYSQLI_ASSOC);
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
        <h1>Form Peminjaman Aset</h1>
        <div class="pilihan">
            <a href="form-pinjam.php?sudah"><button class="btn" <?php if(isset($_GET['sudah'])) { echo 'style="background-color: #287EFF;"';} ?>>Sudah Pernah Meminjam</button></a>
            <a href="form-pinjam.php?belum"><button class="btn"<?php if(!isset($_GET['sudah'])) { echo 'style="background-color: #287EFF;"';} ?>>Belum Pernah Meminjam</button></a>
        </div>
        
        <?php 
            if (isset($_GET['sudah'])) { ?>
                <form action="form-pinjam.php" method="post">
                    <input type="hidden" name="status" value="sudah">
                    <label for="">Nama Peminjam</label><br>
                    <select name="nama" id="" required>
                        <?php for ($i=0; $i < count($users); $i++) { ?>
                            <option value="<?php echo $users[$i]['id']?>"><?php echo $users[$i]['nama']?></option>
                        <?php } ?>
                    </select><br>
                    <label for="">Nama Aset</label><br>
                    <select name="aset" id="" required>
                        <?php for ($i=0; $i < count($asets); $i++) { ?>
                            <option value="<?php echo $asets[$i]['id']?>"><?php echo $asets[$i]['nama_aset']." - ".$asets[$i]['merk']." (".$asets[$i]['jml_aset'].")" ?></option>
                        <?php } ?>
                    </select><br>
                    <label for="">Jumlah</label><br>
                    <input name="jml_pinjam" type="number" required><br>
                    <label for="">Tanggal Rencana Pengembalian</label><br>
                    <input name="rencana_kembali" type="date" required><br>
                    <label for="">Peruntukkan</label><br>
                    <input name="peruntukkan" type="text" required> 
                    <div class="button">
                        <a href="index.php"><button class="btn btn-cancel" type="button">Cancel</button></a>
                        <button class="btn btn-simpan" value="simpan" name="simpan" type="submit">Simpan</button>
                    </div>
                </form>
        <?php } 
            else { 
                if($status_penambahan != null){
                    echo '<p style="text-align: center; color: #FF0000; margin-bottom: 20px;">'.$status_penambahan.'</p>';
                }
        ?>
                <form action="form-pinjam.php" method="post">
                    <input type="hidden" name="status" value="belum">
                    <label for="">Nama Peminjam</label>
                    <input name="nama" type="text" required><br>
                    <label for="">Instansi</label>
                    <input name="instansi" type="text" required><br>
                    <label for="">Bagian</label>
                    <input name="bagian" type="text" required><br>
                    <label for="">Nama Aset</label>
                    <select name="aset" id="" required>
                        <?php for ($i=0; $i < count($asets); $i++) { ?>
                            <option value="<?php echo $asets[$i]['id']?>"><?php echo $asets[$i]['nama_aset']." - ".$asets[$i]['merk']." (".$asets[$i]['jml_aset'].")" ?></option>
                        <?php } ?>
                    </select><br>
                    <label for="">Jumlah</label>
                    <input name="jml_pinjam" type="number" required><br>
                    <label for="">Tanggal Rencana Pengembalian</label>
                    <input name="rencana_kembali" type="date" required><br>
                    <label for="">Peruntukkan</label>
                    <input name="peruntukkan" type="text" required>
                    <div class="button">
                        <a href="index.php"><button class="btn btn-cancel" type="button">Cancel</button></a>
                        <button class="btn btn-simpan" value="simpan" name="simpan" type="submit">Simpan</button>
                    </div>
                </form>
        <?php }
        ?>
    </div>
</body>
</html>
<?php
include_once 'function.php';

// Logika pendaftaran akan tetap di bawah setelah form HTML
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="Halaman Pendaftaran Admin" />
        <meta name="author" content="" />
        <title>Sign Up - Admin</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Buat Akun Admin</h3></div>
                                    <div class="card-body">
                                        <form action="signup.php" method="post">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputUsername" name="username" type="text" placeholder="Username" required />
                                                <label for="inputUsername">Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Buat password" required />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="mt-4 mb-0">
                                                <div class="d-grid"><button type="submit" class="btn btn-primary btn-block">Buat Akun</button></div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-center py-3">
                                        <div class="small"><a href="login.php">Sudah punya akun? Login</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>

        <?php
        // Logika PHP untuk memproses pendaftaran diletakkan di sini agar tidak mengganggu rendering halaman
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['username'];
            $pass_input = $_POST['password']; 

            if (empty($user) || empty($pass_input)) {
                echo "<script>alert('Username dan password tidak boleh kosong.');</script>";
            } else {
                $stmt_check = mysqli_prepare($koneksi, "SELECT id FROM admin WHERE username = ?");
                if ($stmt_check) {
                    mysqli_stmt_bind_param($stmt_check, "s", $user);
                    mysqli_stmt_execute($stmt_check);
                    $result_check = mysqli_stmt_get_result($stmt_check);

                    if (mysqli_num_rows($result_check) > 0) {
                        echo "<script>alert('Username sudah digunakan.');</script>";
                    } else {
                        $pass_hashed = password_hash($pass_input, PASSWORD_DEFAULT);
                        $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO admin(username, password) VALUES(?, ?)");
                        if ($stmt_insert) {
                            mysqli_stmt_bind_param($stmt_insert, "ss", $user, $pass_hashed);
                            if (mysqli_stmt_execute($stmt_insert)) {
                                echo "<script>alert('Sign up berhasil! Silakan login.'); window.location='login.php';</script>";
                            } else {
                                echo "<script>alert('Sign up gagal. Silakan coba lagi.');</script>";
                                error_log("MySQLi insert execute failed: " . mysqli_stmt_error($stmt_insert));
                            }
                            mysqli_stmt_close($stmt_insert);
                        } else {
                            echo "<script>alert('Terjadi kesalahan pada sistem (prepare insert).');</script>";
                            error_log("MySQLi prepare insert failed: " . mysqli_error($koneksi));
                        }
                    }
                    mysqli_stmt_close($stmt_check);
                } else {
                    echo "<script>alert('Terjadi kesalahan pada sistem (prepare check).');</script>";
                    error_log("MySQLi prepare check failed: " . mysqli_error($koneksi));
                }
            }
        }
        ?>
    </body>
</html>
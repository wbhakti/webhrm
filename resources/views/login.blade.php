<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Login Karyawan HRM" />
    <meta name="author" content="HRM Admin" />
    <title>Login Karyawan - HRM System</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        input[type=password]::-ms-reveal,
        input[type=password]::-ms-clear
        {
            display: none;
        }
    </style>
</head>
<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="card shadow-lg custom-border rounded-lg mt-5">
                                <div class="card-header bg-dark text-white">
                                    <h3 class="text-center font-weight-light my-4">Login Karyawan</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Informasi tambahan untuk karyawan -->
                                    <div class="alert alert-info text-center">
                                        <strong>Informasi:</strong> Silakan login menggunakan email perusahaan dan password Anda.
                                        Hubungi administrator jika Anda mengalami masalah login.
                                    </div>
                                    
                                    <!-- Form login -->
                                    <form method="POST" action="/postlogin">
                                        @csrf
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="username" type="email" placeholder="Email" maxlength="40" required />
                                            <label for="inputEmail">Email</label>
                                        </div>

                                        <div class="form-floating mb-3 position-relative">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" maxlength="6" required />
                                            <label for="inputPassword">Password</label>
                                            <span class="position-absolute" id="togglePassword" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="{{ route('resetpassword') }}">Lupa Password?</a>
                                            <button class="btn btn-primary" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small"><div class="text-muted">Copyright &copy; Web HRM 2024</div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    @if(session('success'))
    <script>
        alert('{{ session('success') }}');
    </script>
    @endif

    @if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#inputPassword');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
        });
    </script>

</body>
</html>

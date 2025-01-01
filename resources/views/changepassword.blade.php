@extends('sb-admin.layouts.app')

@section('content')

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Perubahan Password</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <span><b>Penting!</b> Password maksimal 6 karakter. Gunakan password yang sulit ditebak!</span>
                    </div>
                    <form action="/postubahpassword" method="POST" id="changePasswordForm">
                        @csrf
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" maxlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" maxlength="6" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif

@if(session('error'))
<script>
    alert('{{ session('error') }}');
</script>
@endif

<script>
    // Pengecekan password sebelum mengirimkan formulir
    document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
        var newPassword = document.getElementById('new_password').value;
        var newPasswordConfirmation = document.getElementById('new_password_confirmation').value;

        if (newPassword !== newPasswordConfirmation) {
            event.preventDefault(); // Mencegah pengiriman formulir
            alert('Password tidak cocok. Silakan coba lagi.'); // Tampilkan pesan kesalahan
        }
    });
</script>

@endsection
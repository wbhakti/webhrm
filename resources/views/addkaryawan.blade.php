@extends('sb-admin.layouts.app')

@section('content')
    <h1 class="mt-4">Tambah Karyawan</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Tambah Karyawan</li>
    </ol>

    <div class="card mb-4 col-md-6">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i>
            Input Data Karyawan
        </div>
        <div class="card-body">

            <form method="POST" action="/postaddkaryawan">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label"><b>Nama</b></label>
                    <input type="text" class="form-control" id="nama" name="nama" maxlength="40" required>
                </div>
                <div class="mb-3">
                    <label for="noHp" class="form-label"><b>No HP</b></label>
                    <input type="number" class="form-control" id="noHp" name="no_hp" maxlength="14" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label"><b>Email</b></label>
                    <input type="email" class="form-control" id="email" name="email" maxlength="40" required>
                </div>
                <div class="mb-3">
                    <label for="bagian" class="form-label"><b>Bagian</b></label>
                    <select class="form-select" id="bagian" name="bagian" required>
                        <option value="">Pilih Bagian</option>
                        <option value="1">HEAD OFFICE</option>
                        <option value="2">GUDANG</option>
                        <option value="3">OUTLET</option>
                    </select>
                </div>

                <div class="mb-3" id="outlet-container" style="display: none;"> 
                    <label for="outlet" class="form-label"><b>Outlet</b></label> 
                    <select class="form-select" id="outlet" name="outlet"> 
                        @foreach($data as $outlet)
                            <option value="{{ $outlet->ID_OUTLET }}">{{ $outlet->NAMA }}</option>
                        @endforeach 
                    </select> 
                </div>
                <div class="mb-3">
                    <label for="jabatan" class="form-label"><b>Jabatan</b></label>
                    <select class="form-select" id="jabatan" name="jabatan" required>
                        <option value="">Pilih Jabatan</option>
                    </select>
                </div>
                <div class="mb-3"> 
                    <label for="status" class="form-label"><b>Status Karyawan</b></label> 
                    <select class="form-select" id="status" name="status_karyawan" required onchange="toggleKontrak()">
                        <option value="TETAP">TETAP</option>
                        <option value="KONTRAK">KONTRAK</option> 
                    </select> 
                </div>
                <div class="mb-3" id="kontrak-container" style="display: none;">
                    <label for="kontrak" class="form-label"><b>Masa Kontrak</b></label>
                    <select class="form-select" id="kontrak" name="kontrak">
                        <option value="1">1 Bulan</option>
                        <option value="3">3 Bulan</option>
                        <option value="6">6 Bulan</option>
                        <option value="12">12 Bulan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="ktp" class="form-label"><b>NIK (nomor induk kependudukan)</b></label>
                    <input type="number" class="form-control" id="ktp" name="ktp" maxlength="20" required>
                </div>

                <div class="mb-3">
                    <label for="tempatLahir" class="form-label"><b>Tempat Lahir</b></label>
                    <input type="text" class="form-control" id="tempatLahir" name="tempat_lahir" maxlength="50" required>
                </div>
                <div class="mb-3">
                    <label for="tanggalLahir" class="form-label"><b>Tanggal Lahir</b></label>
                    <input type="date" class="form-control" id="tanggalLahir" name="tanggal_lahir" value="2000-01-07" required>
                </div>
                <div class="mb-3">
                    <label for="alamatKtp" class="form-label"><b>Alamat KTP</b></label>
                    <textarea class="form-control" id="alamatKtp" name="alamat_ktp" rows="3" maxlength="100" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="alamatTinggal" class="form-label"><strong>Alamat Tinggal</strong></label>
                    <textarea class="form-control" id="alamatTinggal" name="alamat_tinggal" rows="3" maxlength="100" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="tanggalBergabung" class="form-label"><strong>Tanggal Bergabung</strong></label>
                    <input type="date" class="form-control" id="tanggalBergabung" name="tanggal_bergabung" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label"><b>Role</b></label>
                    <select class="form-select" id="role" name="role" required>
                        @foreach($dataRole as $item)
                            <option value="{{ $item->ID_ROLE }}">{{ $item->ROLE }}</option>
                        @endforeach 
                    </select>
                </div>   

                <div class="d-grid gap-2 col-6 mx-auto mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                </div>
            </form>

        </div>
    </div>

    @if(session('error'))
    <script>
        alert('{{ session('error') }}');
    </script>
    @endif

    <script>
        function toggleKontrak() {
            const status = document.getElementById('status').value;
            const kontrakSelect = document.getElementById('kontrak');
        
            if (status === 'TETAP') {
                kontrakSelect.disabled = true;
                kontrakSelect.value = "";
            } else if (status === 'KONTRAK') {
                kontrakSelect.disabled = false;
                kontrakSelect.value = "12 Bulan";
            } else {
                kontrakSelect.disabled = true;
            }
        }
    </script>

<script src="{{ asset('vendor/jquery/jquery-3.3.1.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#bagian').change(function() {
            var selectedBagian = $(this).val();
            $('#jabatan').empty();
            
            if (selectedBagian == '3') { // Jika bagian adalah "OUTLET"
                $('#outlet-container').show(); // Tampilkan dropdown Outlet
            } else {
                $('#outlet-container').hide(); // Sembunyikan dropdown Outlet
            }

            // Mendapatkan jabatan berdasarkan bagian yang dipilih
            if(selectedBagian) {
                $.ajax({
                    url: '/get-jabatan/' + selectedBagian,
                    method: 'GET',
                    success: function(data) {
                        $.each(data, function(index, jabatan) {
                            $('#jabatan').append('<option value="' + jabatan.ID_JABATAN + '">' + jabatan.NAMA_JABATAN + '</option>');
                        });
                    }
                });
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#status').change(function() {
            var selectedStatus = $(this).val();

            if (selectedStatus === 'KONTRAK') { 
                $('#kontrak-container').show();
                $('#kontrak').val('12');
            } else {
                $('#kontrak-container').hide();
            }
        });
    });
</script>


@endsection

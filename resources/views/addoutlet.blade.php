@extends('sb-admin.layouts.app')

@section('content')
    <h1 class="mt-4">Tambah Toko/Outlet</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Tambah Toko/Outlet</li>
    </ol>

    <div class="card mb-4 col-md-6"> <!-- Menambahkan kelas col-md-6 dan mx-auto -->
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i>
            Input Data Toko/Outlet
        </div>
        <div class="card-body">
            <form method="POST" action="/postaddoutlet">
                @csrf

                <div class="mb-3">
                    <label for="status" class="form-label"><b>Status Outlet</b></label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="1">PRIBADI</option>
                        <option value="2">FRANCHISE</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipe" class="form-label"><b>Tipe Outlet</b></label>
                    <select class="form-select" id="tipe" name="tipe" required>
                        <option value="1">REGULER</option>
                        <option value="2">EXPRESS</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label"><b>Nama Outlet</b></label>
                    <input type="text" class="form-control" id="nama" name="nama" maxlength="20" required>
                </div>
                <div class="mb-3">
                    <label for="noTelepon" class="form-label"><b>No Telepon</b></label>
                    <input type="number" class="form-control" id="noTelepon" name="no_hp" maxlength="14" required>
                </div>
            
                <div class="mb-3">
                    <label for="provinsi" class="form-label"><b>Provinsi</b></label>
                    <select class="form-select" id="provinsi" name="provinsi" required>
                        <option value="">Pilih Provinsi</option>
                        <!-- Data provinsi akan diisi melalui AJAX -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kabupaten" class="form-label"><b>Kabupaten</b></label>
                    <select class="form-select" id="kabupaten" name="kabupaten" required>
                        <option value="">Pilih Kabupaten</option>
                        <!-- Data kabupaten akan diisi melalui AJAX -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kecamatan" class="form-label"><b>Kecamatan</b></label>
                    <select class="form-select" id="kecamatan" name="kecamatan" required>
                        <option value="">Pilih Kecamatan</option>
                        <!-- Data kecamatan akan diisi melalui AJAX -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="kelurahan" class="form-label"><b>Kelurahan</b></label>
                    <select class="form-select" id="kelurahan" name="kelurahan" required>
                        <option value="">Pilih Kelurahan</option>
                        <!-- Data kelurahan akan diisi melalui AJAX -->
                    </select>
                </div>
            
                <div class="mb-3">
                    <label for="alamat" class="form-label"><b>Alamat</b></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="3" maxlength="100" required></textarea>
                </div>
            
                <div class="mb-3">
                    <label for="latitude" class="form-label"><b>Latitude</b></label>
                    <input type="text" class="form-control" id="latitude" name="latitude" maxlength="100" required>
                </div>
            
                <div class="mb-3">
                    <label for="longitude" class="form-label"><b>Longitude</b></label>
                    <input type="text" class="form-control" id="longitude" name="longitude" maxlength="100" required>
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

    <script src="{{ asset('vendor/jquery/jquery-3.3.1.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Ambil provinsi
            $.ajax({
                url: '/get-provinsi',
                method: 'GET',
                success: function(data) {
                    $.each(data, function(index, provinsi) {
                        $('#provinsi').append('<option value="' + provinsi.prov_id + '|' + provinsi.prov_name + '">' + provinsi.prov_name + '</option>');
                    });
                }
            });
    
            // Ambil kabupaten berdasarkan provinsi yang dipilih
            $('#provinsi').change(function() {
                var provinsiId = $(this).val();
                $('#kabupaten').empty().append('<option value="">Pilih Kabupaten</option>'); // Reset kabupaten
                $('#kecamatan').empty().append('<option value="">Pilih Kecamatan</option>'); // Reset kecamatan
                $('#kelurahan').empty().append('<option value="">Pilih Kelurahan</option>'); // Reset kelurahan
                
                if(provinsiId) {
                    $.ajax({
                        url: '/get-kota/' + provinsiId,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, kota) {
                                $('#kabupaten').append('<option value="' + kota.city_id + '|' + kota.city_name + '">' + kota.city_name + '</option>');
                            });
                        }
                    });
                }
            });
    
            // Ambil kecamatan berdasarkan kabupaten yang dipilih
            $('#kabupaten').change(function() {
                var kotaId = $(this).val();
                $('#kecamatan').empty().append('<option value="">Pilih Kecamatan</option>'); // Reset kecamatan
                $('#kelurahan').empty().append('<option value="">Pilih Kelurahan</option>'); // Reset kelurahan
                
                if(kotaId) {
                    $.ajax({
                        url: '/get-kecamatan/' + kotaId,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, kecamatan) {
                                $('#kecamatan').append('<option value="' + kecamatan.dis_id + '|' + kecamatan.dis_name + '">' + kecamatan.dis_name + '</option>');
                            });
                        }
                    });
                }
            });
    
            // Ambil kelurahan berdasarkan kecamatan yang dipilih
            $('#kecamatan').change(function() {
                var kecamatanId = $(this).val();
                $('#kelurahan').empty().append('<option value="">Pilih Kelurahan</option>'); // Reset kelurahan
                
                if(kecamatanId) {
                    $.ajax({
                        url: '/get-kelurahan/' + kecamatanId,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, kelurahan) {
                                $('#kelurahan').append('<option value="' + kelurahan.subdis_id + '|' + kelurahan.subdis_name + '">' + kelurahan.subdis_name + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>

@endsection

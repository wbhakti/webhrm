@extends('sb-admin.layouts.app')

@section('content')

    <!-- CSS custom -->
    <style>
        .modal-body {
            overflow-y: auto;
        }
    </style>

    <h1 class="mt-4">List Toko/Outlet</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">List Toko/Outlet</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            List Data Toko/Outlet
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="GET" action="/dashboard/list-outlet">
                        <div class="input-group">
                            <label class="input-group-text" for="statusFilter">Filter Outlet</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Outlet</option>
                                <option value="pribadi" {{ request('status') == 'pribadi' ? 'selected' : '' }}>PRIBADI</option>
                                <option value="franchise" {{ request('status') == 'franchise' ? 'selected' : '' }}>FRANCHISE</option>
                                <option value="reguler" {{ request('status') == 'reguler' ? 'selected' : '' }}>REGULER</option>
                                <option value="express" {{ request('status') == 'express' ? 'selected' : '' }}>EXPRESS</option>
                            </select>
                            <button class="btn btn-primary" type="submit">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <table id="datatablesSimple" class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Outlet</th>
                        <th>Lokasi Outlet</th>
                        <th>Status Outlet</th>
                        <th>Tipe Outlet</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($listData) && !$listData->isEmpty())
                        @foreach ($listData as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->NAMA }}</td>
                                <td>{{ $item->ALAMAT }}</td>
                                <td>{{ $item->status_outlet }}</td>
                                <td>{{ $item->jenis_outlet }}</td>
                                <td>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#detailsModal" data-nama="{{ $item->NAMA }}"
                                        data-kelurahan="{{ $item->KELURAHAN }}" data-kecamatan="{{ $item->KECAMATAN }}"
                                        data-kabupaten="{{ $item->KABUPATEN }}" data-provinsi="{{ $item->PROVINSI }}"
                                        data-alamat="{{ $item->ALAMAT }}" data-telepon="{{ $item->NO_HP }}"
                                        data-tipe="{{ $item->jenis_outlet }}" data-status="{{ $item->status_outlet }}"
                                        data-latitude="{{ $item->LATITUDE }}" data-longitude="{{ $item->LONGITUDE }}">
                                        Detail
                                    </button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-nama="{{ $item->NAMA }}"
                                        data-kelurahan="{{ $item->KELURAHAN }}" data-kecamatan="{{ $item->KECAMATAN }}"
                                        data-kabupaten="{{ $item->KABUPATEN }}" data-provinsi="{{ $item->PROVINSI }}"
                                        data-alamat="{{ $item->ALAMAT }}" data-telepon="{{ $item->NO_HP }}"
                                        data-tipe="{{ $item->jenis_outlet }}" data-status="{{ $item->status_outlet }}"
                                        data-latitude="{{ $item->LATITUDE }}" data-longitude="{{ $item->LONGITUDE }}"
                                        data-id="{{ $item->ID_OUTLET }}" data-id_tipe_outlet="{{ $item->id_tipe_outlet }}" 
                                        data-id_status_outlet="{{ $item->id_status_outlet }}">
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                    @endif
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Detail Toko/Outlet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Nama Toko/Outlet -->
                        <div class="form-group mb-2">
                            <label for="modalNama"><strong>Nama Toko/Outlet:</strong></label>
                            <input type="text" class="form-control" id="modalNama" readonly disabled>
                        </div>
                        <!-- Status -->
                        <div class="form-group mb-2">
                            <label for="modalStatus"><strong>Status:</strong></label>
                            <input type="text" class="form-control" id="modalStatus" readonly disabled>
                        </div>
                        <!-- Tipe -->
                        <div class="form-group mb-2">
                            <label for="modalTipe"><strong>Tipe:</strong></label>
                            <input type="text" class="form-control" id="modalTipe" readonly disabled>
                        </div>
                        <!-- No Telepon -->
                        <div class="form-group mb-2">
                            <label for="modalNoTelepon"><strong>No Telepon:</strong></label>
                            <input type="text" class="form-control" id="modalNoTelepon" readonly disabled>
                        </div>
                        <!-- Provinsi -->
                        <div class="form-group mb-2">
                            <label for="modalProvinsi"><strong>Provinsi:</strong></label>
                            <input type="text" class="form-control" id="modalProvinsi" readonly disabled>
                        </div>
                        <!-- Kabupaten -->
                        <div class="form-group mb-2">
                            <label for="modalKabupaten"><strong>Kabupaten:</strong></label>
                            <input type="text" class="form-control" id="modalKabupaten" readonly disabled>
                        </div>
                        <!-- Kecamatan -->
                        <div class="form-group mb-2">
                            <label for="modalKecamatan"><strong>Kecamatan:</strong></label>
                            <input type="text" class="form-control" id="modalKecamatan" readonly disabled>
                        </div>
                        <!-- Kelurahan -->
                        <div class="form-group mb-2">
                            <label for="modalKelurahan"><strong>Kelurahan:</strong></label>
                            <input type="text" class="form-control" id="modalKelurahan" readonly disabled>
                        </div>
                        <!-- Alamat -->
                        <div class="form-group mb-2">
                            <label for="modalAlamat"><strong>Alamat:</strong></label>
                            <textarea class="form-control" id="modalAlamat" readonly disabled></textarea>
                        </div>
                        <!-- Latitude -->
                        <div class="form-group mb-2">
                            <label for="modalLatitude"><strong>Latitude:</strong></label>
                            <input type="text" class="form-control" id="modalLatitude" readonly disabled>
                        </div>

                        <!-- Longitude -->
                        <div class="form-group mb-2">
                            <label for="modalLongitude"><strong>Longitude:</strong></label>
                            <input type="text" class="form-control" id="modalLongitude" readonly disabled>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">

                <div class="modal-content">

                    <form class="modal-content" action="/posteditoutlet" method="POST">
                        @csrf
    
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Toko/Outlet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
    
                        <div class="modal-body">
                            <!-- Nama Toko/Outlet -->
                            <div class="form-group mb-2">
                                <label for="modalNama"><strong>Nama Toko/Outlet:</strong></label>
                                <input type="text" class="form-control" id="modalNama" name="nama">
                            </div>

                            <!-- Status -->
                            <div class="form-group mb-2">
                                <label for="modalStatus"><strong>Status:</strong></label>
                                <input type="hidden" name="rowid" value="" id="modalRowid">
                                <select class="form-select" id="modalStatusEdit" name="status" required>
                                    <option value="1">PRIBADI</option>
                                    <option value="2">FRANCHISE</option>
                                </select>
                            </div>
                            <!-- Tipe -->
                            <div class="form-group mb-2">
                                <label for="modalTipe"><strong>Tipe:</strong></label>
                                <select class="form-select" id="modalTipeEdit" name="tipe" required>
                                    <option value="1">REGULER</option>
                                    <option value="2">EXPRESS</option>
                                </select>
                            </div>
                            <!-- No Telepon -->
                            <div class="form-group mb-2">
                                <label for="modalNoTelepon"><strong>No Telepon:</strong></label>
                                <input type="text" class="form-control" id="modalNoTelepon" name="no_hp">
                            </div>
    
                            <div class="form-group mb-2">
                                <label for="modalProvinsiEdit"><strong>Provinsi:</strong></label>
                                <select class="form-control" id="modalProvinsiEdit" name="provinsi">
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="modalKabupatenEdit"><strong>Kabupaten:</strong></label>
                                <select class="form-control" id="modalKabupatenEdit" name="kabupaten">
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="modalKecamatanEdit"><strong>Kecamatan:</strong></label>
                                <select class="form-control" id="modalKecamatanEdit" name="kecamatan">
                                </select>
                            </div>
                            <div class="form-group mb-2">
                                <label for="modalKelurahanEdit"><strong>Kelurahan:</strong></label>
                                <select class="form-control" id="modalKelurahanEdit" name="kelurahan">
                                </select>
                            </div>
    
                            <!-- Alamat -->
                            <div class="form-group mb-2">
                                <label for="modalAlamat"><strong>Alamat:</strong></label>
                                <textarea class="form-control" id="modalAlamat" name="alamat"></textarea>
                            </div>
                            <!-- Latitude -->
                            <div class="form-group mb-2">
                                <label for="modalLatitude"><strong>Latitude:</strong></label>
                                <input type="text" class="form-control" id="modalLatitude" name="latitude">
                            </div>
    
                            <!-- Longitude -->
                            <div class="form-group mb-2">
                                <label for="modalLongitude"><strong>Longitude:</strong></label>
                                <input type="text" class="form-control" id="modalLongitude" name="longitude">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Save Changes</button>
                        </div>
                    </form>

                </div>

            </div>
        </div>

    </div>


    @if (session('success'))
        <script>
            alert('{{ session('success') }}');
        </script>
    @endif

    @if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
    @endif

    <script src="{{ asset('vendor/jquery/jquery-3.3.1.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#datatablesSimple').DataTable({
                "lengthMenu": [10, 20, 50, 100],
                "pageLength": 10,
                responsive: true,
                searching: true
            });

            // Menangani ketika modal terbuka
            $('#detailsModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button yang memicu modal
                var modal = $(this);

                // Ambil data dari atribut tombol
                var nama = button.data('nama');
                var kelurahan = button.data('kelurahan');
                var kecamatan = button.data('kecamatan');
                var kabupaten = button.data('kabupaten');
                var provinsi = button.data('provinsi');
                var alamat = button.data('alamat');
                var notelepon = button.data('telepon');
                var tipe = button.data('tipe');
                var status = button.data('status');
                var latitude = button.data('latitude');
                var longitude = button.data('longitude');

                // Set data ke dalam modal
                modal.find('#modalNama').val(nama);
                modal.find('#modalKelurahan').val(kelurahan);
                modal.find('#modalKecamatan').val(kecamatan);
                modal.find('#modalKabupaten').val(kabupaten);
                modal.find('#modalProvinsi').val(provinsi);
                modal.find('#modalAlamat').val(alamat);
                modal.find('#modalNoTelepon').val(notelepon);
                modal.find('#modalTipe').val(tipe);
                modal.find('#modalStatus').val(status);
                modal.find('#modalLatitude').val(latitude);
                modal.find('#modalLongitude').val(longitude);
            });

        });
    </script>

    <script>
        $(document).ready(function() {

            // Menangani ketika modal terbuka
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // Button yang memicu modal
                var modal = $(this);

                // Ambil data dari atribut tombol
                var nama = button.data('nama');
                var kelurahan = button.data('kelurahan');
                var kecamatan = button.data('kecamatan');
                var kabupaten = button.data('kabupaten');
                var provinsi = button.data('provinsi');
                var alamat = button.data('alamat');
                var notelepon = button.data('telepon');
                var tipe = button.data('tipe');
                var status = button.data('status');
                var latitude = button.data('latitude');
                var longitude = button.data('longitude');
                var rowid = button.data('id');
                var id_tipe_outlet = button.data('id_tipe_outlet');
                var id_status_outlet = button.data('id_status_outlet');

                // Set data ke dalam modal
                modal.find('#modalNama').val(nama);
                modal.find('#modalKelurahanEdit').val(kelurahan);
                modal.find('#modalKecamatanEdit').val(kecamatan);
                modal.find('#modalKabupatenEdit').val(kabupaten);
                modal.find('#modalProvinsiEdit').val(provinsi);
                modal.find('#modalAlamat').val(alamat);
                modal.find('#modalNoTelepon').val(notelepon);
                modal.find('#modalTipeEdit').val(id_tipe_outlet);
                modal.find('#modalStatusEdit').val(id_status_outlet);
                modal.find('#modalLatitude').val(latitude);
                modal.find('#modalLongitude').val(longitude);
                modal.find('#modalRowid').val(rowid);

                // Ambil daftar provinsi
                $.ajax({
                    url: '/get-provinsi',
                    method: 'GET',
                    success: function(data) {
                        var provinsiId;
                        $.each(data, function(index, prov) {
                            $('#modalProvinsiEdit').append('<option value="' + prov.prov_id + '|' + prov.prov_name + '">' + prov.prov_name + '</option>');
                            if (prov.prov_name === provinsi) {
                                provinsiId = prov.prov_id + '|' + prov.prov_name;
                            }
                        });

                        if (provinsiId) {
                            $('#modalProvinsiEdit').val(provinsiId);
                            loadKabupaten(provinsiId, kabupaten, kecamatan, kelurahan);
                        }
                    }
                });
            });

            // Fungsi untuk memuat kabupaten berdasarkan provinsi
            function loadKabupaten(provinsiId, kabupatenName, kecamatanName, kelurahanName) {
                $.ajax({
                    url: '/get-kota/' + provinsiId,
                    method: 'GET',
                    success: function(data) {
                        var kabupatenId;
                        $.each(data, function(index, kota) {
                            $('#modalKabupatenEdit').append('<option value="' + kota.city_id + '|' + kota.city_name + '">' + kota.city_name + '</option>');
                            
                            if (kota.city_name === kabupatenName) {
                                kabupatenId = kota.city_id + '|' + kota.city_name;
                            }
                        });

                        if (kabupatenId) {
                            $('#modalKabupatenEdit').val(kabupatenId);
                            loadKecamatan(kabupatenId, kabupatenName, kecamatanName, kelurahanName);
                        }
                    }
                });
            }

            // Fungsi untuk memuat kecamatan berdasarkan kabupaten
            function loadKecamatan(kabupatenId, kabupatenName, kecamatanName, kelurahanName) {
                $.ajax({
                    url: '/get-kecamatan/' + kabupatenId,
                    method: 'GET',
                    success: function(data) {
                        var kecamatanId;
                        $.each(data, function(index, kecamatan) {
                            $('#modalKecamatanEdit').append('<option value="' + kecamatan.dis_id + '|' + kecamatan.dis_name + '">' + kecamatan.dis_name + '</option>');
                            
                            if (kecamatan.dis_name === kecamatanName) {
                                kecamatanId = kecamatan.dis_id + '|' + kecamatan.dis_name;
                            }
                        });
                        
                        if (kecamatanId) {
                            $('#modalKecamatanEdit').val(kecamatanId);
                            loadKelurahan(kecamatanId, kelurahanName);
                        }
                    }
                });
            }

            // Fungsi untuk memuat kelurahan berdasarkan kecamatan
            function loadKelurahan(kecamatanId, kelurahanName) {
                $.ajax({
                    url: '/get-kelurahan/' + kecamatanId,
                    method: 'GET',
                    success: function(data) {
                        $.each(data, function(index, kelurahan) {
                            $('#modalKelurahanEdit').append('<option value="' + kelurahan.subdis_id + '|' + kelurahan.subdis_name + '">' + kelurahan.subdis_name + '</option>');
                            if (kelurahan.subdis_name === kelurahanName) {
                                $('#modalKelurahanEdit').val(kelurahan.subdis_id + '|' + kelurahan.subdis_name);
                            }
                        });
                    }
                });
            }

        });
    </script>

    <script>
        $(document).ready(function() {

            // Ambil kabupaten berdasarkan provinsi yang dipilih
            $('#modalProvinsiEdit').change(function() {
                var provinsiId = $(this).val();

                $('#modalKabupatenEdit').empty().append('<option value="">Pilih Kabupaten</option>'); // Reset kabupaten
                $('#modalKecamatanEdit').empty().append('<option value="">Pilih Kecamatan</option>'); // Reset kecamatan
                $('#modalKelurahanEdit').empty().append('<option value="">Pilih Kelurahan</option>'); // Reset kelurahan
                
                if(provinsiId) {
                    $.ajax({
                        url: '/get-kota/' + provinsiId,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, kota) {
                                $('#modalKabupatenEdit').append('<option value="' + kota.city_id + '|' + kota.city_name + '">' + kota.city_name + '</option>');
                            });
                        }
                    });
                }
            });

            // Ambil kecamatan berdasarkan kabupaten yang dipilih
            $('#modalKabupatenEdit').change(function() {
                var kotaId = $(this).val();
                $('#modalKecamatanEdit').empty().append('<option value="">Pilih Kecamatan</option>'); // Reset kecamatan
                $('#modalKelurahanEdit').empty().append('<option value="">Pilih Kelurahan</option>'); // Reset kelurahan
                
                if(kotaId) {
                    $.ajax({
                        url: '/get-kecamatan/' + kotaId,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, kecamatan) {
                                $('#modalKecamatanEdit').append('<option value="' + kecamatan.dis_id + '|' + kecamatan.dis_name + '">' + kecamatan.dis_name + '</option>');
                            });
                        }
                    });
                }
            });

            // Ambil kelurahan berdasarkan kecamatan yang dipilih
            $('#modalKecamatanEdit').change(function() {
                var kecamatanId = $(this).val();
                $('#modalKelurahanEdit').empty().append('<option value="">Pilih Kelurahan</option>'); // Reset kelurahan
                
                if(kecamatanId) {
                    $.ajax({
                        url: '/get-kelurahan/' + kecamatanId,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, kelurahan) {
                                $('#modalKelurahanEdit').append('<option value="' + kelurahan.subdis_id + '|' + kelurahan.subdis_name + '">' + kelurahan.subdis_name + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>

@endsection

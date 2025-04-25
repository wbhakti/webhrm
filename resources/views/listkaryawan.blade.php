@extends('sb-admin.layouts.app')

@section('content')

<!-- CSS custom -->
<style>
    .modal-body {
        overflow-y: auto;
    }
</style>

<h1 class="mt-4">List Karyawan</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    <li class="breadcrumb-item active">List Karyawan</li>
</ol>
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table me-1"></i>
        Data Karyawan
    </div>
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" action="/dashboard/list-karyawan">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="filterBagian">Bagian</label>
                        <select class="form-select" id="filterBagian" name="filterBagian">
                            <option value="all" {{ request('filterBagian') == 'all' ? 'selected' : '' }}>Semua Bagian</option>
                            <option value="headoffice" {{ request('filterBagian') == 'headoffice' ? 'selected' : '' }}>HEAD OFFICE</option>
                            <option value="gudang" {{ request('filterBagian') == 'gudang' ? 'selected' : '' }}>GUDANG</option>
                            <option value="outlet" {{ request('filterBagian') == 'outlet' ? 'selected' : '' }}>OUTLET</option>
                        </select>
                        <button class="btn btn-primary" type="submit" id="filterButtonBagian">Filter</button>
                    </div>

                    <div class="input-group mb-3" id="outletFilter" style="display: none;">
                        <label class="input-group-text" for="filterOutlet">Nama Outlet</label>
                        <select class="form-select" id="filterOutlet" name="filterOutlet">
                            @foreach($listOutlet as $item)
                            <option value="{{ $item->ID_OUTLET }}" {{ request('filterOutlet') == $item->ID_OUTLET ? 'selected' : '' }}>{{ $item->NAMA }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary" type="submit" id="filterButtonOutlet">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <table id="datatablesSimple" class="table table-bordered" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Nomor HP</th>
                    <th>Bagian</th>
                    <th>Jabatan</th>
                    <th>Outlet</th>
                    <th>Status Karyawan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($listData) && !$listData->isEmpty())
                @foreach ($listData as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->NAMA }}</td>
                    <td>{{ $item->NO_HP }}</td>
                    <td>{{ $item->nama_bagian }}</td>
                    <td>{{ $item->nama_jabatan }}</td>
                    <td>{{ $item->nama_toko }}</td>
                    <td>{{ $item->STATUS_KARYAWAN }}</td>
                    <td>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#detailsModal" data-nama="{{ $item->NAMA }}" data-nomorhp="{{ $item->NO_HP }}" data-email="{{ $item->EMAIL }}" data-bagian="{{ $item->nama_bagian }}" data-jabatan="{{ $item->nama_jabatan }}" data-status="{{ $item->STATUS_KARYAWAN }}" data-nik="{{ $item->NIK }}" data-tptlahir="{{ $item->TEMPAT_LAHIR }}" data-tgllahir="{{ $item->formatted_tanggal_lahir }}" data-alamatktp="{{ $item->ALAMAT_KTP }}" data-alamattgl="{{ $item->ALAMAT_TINGGAL }}" data-outlet="{{ $item->nama_toko }}" data-masakontrak="{{ $item->MASA_KONTRAK }}" data-awalkontrak="{{ $item->formatted_awal_kontrak }}" data-akhirkontrak="{{ $item->formatted_akhir_kontrak }}" data-saldocuti="{{ $item->SALDO_CUTI }}" data-tglgabung="{{ $item->formatted_tanggal_gabung }}" data-id="{{ $item->ID_KARYAWAN }}">
                            Detail
                        </button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal" data-nama="{{ $item->NAMA }}" data-nomorhp="{{ $item->NO_HP }}" data-email="{{ $item->EMAIL }}" data-bagian="{{ $item->nama_bagian }}" data-jabatan="{{ $item->nama_jabatan }}" data-status="{{ $item->STATUS_KARYAWAN }}" data-nik="{{ $item->NIK }}" data-tptlahir="{{ $item->TEMPAT_LAHIR }}" data-tgllahir="{{ $item->formatted_tanggal_lahir_edit }}" data-alamatktp="{{ $item->ALAMAT_KTP }}" data-alamattgl="{{ $item->ALAMAT_TINGGAL }}" data-outlet="{{ $item->nama_toko }}" data-masakontrak="{{ $item->MASA_KONTRAK }}" data-awalkontrak="{{ $item->formatted_awal_kontrak_edit }}" data-akhirkontrak="{{ $item->formatted_akhir_kontrak_edit }}" data-saldocuti="{{ $item->SALDO_CUTI }}" data-tglgabung="{{ $item->formatted_tanggal_gabung_edit }}" data-id="{{ $item->ID_KARYAWAN }}">
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
                    <h5 class="modal-title" id="detailsModalLabel">Detail Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label for="modalNama"><strong>Nama Karyawan:</strong></label>
                        <input type="text" class="form-control" id="modalNama" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalBagian"><strong>Bagian:</strong></label>
                        <input type="text" class="form-control" id="modalBagian" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalJabatan"><strong>Jabatan:</strong></label>
                        <input type="text" class="form-control" id="modalJabatan" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalOutlet"><strong>Outlet:</strong></label>
                        <input type="text" class="form-control" id="modalOutlet" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalNik"><strong>NIK (nomor induk kependudukan):</strong></label>
                        <input type="text" class="form-control" id="modalNik" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalNomorHp"><strong>Nomor HP:</strong></label>
                        <input type="text" class="form-control" id="modalNomorHp" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalEmail"><strong>Email:</strong></label>
                        <input type="text" class="form-control" id="modalEmail" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalTptLahir"><strong>Tempat Lahir:</strong></label>
                        <input type="text" class="form-control" id="modalTptLahir" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalTglLahir"><strong>Tanggal Lahir:</strong></label>
                        <input type="text" class="form-control" id="modalTglLahir" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalStatus"><strong>Status Karyawan:</strong></label>
                        <input type="text" class="form-control" id="modalStatus" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalMasaKontrak"><strong>Masa Kontrak:</strong></label>
                        <input type="text" class="form-control" id="modalMasaKontrak" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalTglGabung"><strong>Tanggal Bergabung:</strong></label>
                        <input type="text" class="form-control" id="modalTglGabung" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalAwalKontrak"><strong>Awal Kontrak:</strong></label>
                        <input type="text" class="form-control" id="modalAwalKontrak" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalAkhirKontrak"><strong>Akhir Kontrak:</strong></label>
                        <input type="text" class="form-control" id="modalAkhirKontrak" readonly disabled>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalAlamatKtp"><strong>Alamat KTP:</strong></label>
                        <textarea class="form-control" id="modalAlamatKtp" readonly disabled></textarea>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalAlamatTgl"><strong>Alamat Tinggal:</strong></label>
                        <textarea class="form-control" id="modalAlamatTgl" readonly disabled></textarea>
                    </div>
                    <div class="form-group mb-2">
                        <label for="modalSaldoCuti"><strong>Saldo Cuti:</strong></label>
                        <input type="text" class="form-control" id="modalSaldoCuti" readonly disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form method="POST" action="/postdeletekaryawan" onsubmit="return confirmDelete()">
                        @csrf
                        <input type="hidden" id="modalid" name="idkaryawan" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editKaryawanForm" action="/posteditkaryawan" method="POST">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="modalNama"><strong>Nama Karyawan:</strong></label>
                            <input type="hidden" id="modalid" name="idkaryawan" value="">
                            <input type="text" class="form-control" id="modalNama" name="nama_karyawan" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalBagian"><strong>Bagian:</strong></label>
                            <select class="form-select" id="modalBagian" name="bagian" required>
                                <option value="1">HEAD OFFICE</option>
                                <option value="2">GUDANG</option>
                                <option value="3">OUTLET</option>
                            </select>
                        </div>
                        <div class="form-group mb-2" id="outlet-container" style="display: none;">
                            <label for="modalOutlet"><strong>Outlet:</strong></label>
                            <select class="form-select" id="modalOutlet" name="outlet">
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalJabatan"><strong>Jabatan:</strong></label>
                            <select class="form-select" id="modalJabatan" name="jabatan" required>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalNik"><strong>NIK (nomor induk kependudukan):</strong></label>
                            <input type="text" class="form-control" id="modalNik" name="nik" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalNomorHp"><strong>Nomor HP:</strong></label>
                            <input type="text" class="form-control" id="modalNomorHp" name="nomor_hp" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalEmail"><strong>Email:</strong></label>
                            <input type="email" class="form-control" id="modalEmail" name="email" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalTptLahir"><strong>Tempat Lahir:</strong></label>
                            <input type="text" class="form-control" id="modalTptLahir" name="tempat_lahir" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalTglLahir"><strong>Tanggal Lahir:</strong></label>
                            <input type="date" class="form-control" id="modalTglLahir" name="tanggal_lahir" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalStatus"><strong>Status Karyawan:</strong></label>
                            <select class="form-select" id="modalStatusEdit" name="status_karyawan" required onchange="toggleKontrak()">
                                <option value="TETAP">TETAP</option>
                                <option value="KONTRAK">KONTRAK</option>
                            </select>
                        </div>
                        <div class="form-group mb-2" id="kontrak-container" style="display: none;">
                            <label for="modalMasaKontrak"><strong>Masa Kontrak:</strong></label>
                            <select class="form-select" id="modalMasaKontrakEdit" name="masa_kontrak">
                                <option value="1">1 Bulan</option>
                                <option value="3">3 Bulan</option>
                                <option value="6">6 Bulan</option>
                                <option value="12">12 Bulan</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalAwalKontrak"><strong>Awal Kontrak:</strong></label>
                            <input type="date" class="form-control" id="modalAwalKontrak" name="awal_kontrak" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalAkhirKontrak"><strong>Akhir Kontrak:</strong></label>
                            <input type="date" class="form-control" id="modalAkhirKontrak" name="akhir_kontrak" value="">
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalAlamatKtp"><strong>Alamat KTP:</strong></label>
                            <textarea class="form-control" id="modalAlamatKtp" name="alamat_ktp"></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalAlamatTgl"><strong>Alamat Tinggal:</strong></label>
                            <textarea class="form-control" id="modalAlamatTgl" name="alamat_tinggal"></textarea>
                        </div>
                        <div class="form-group mb-2">
                            <label for="modalSaldoCuti"><strong>Saldo Cuti:</strong></label>
                            <input type="text" class="form-control" id="modalSaldoCuti" name="saldo_cuti" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>


@if (session('success'))
<script>
    alert('{{ session('success') }}');
</script>
@endif

<script>
    function confirmDelete() {
        return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');
    }
</script>

<script>
    $(document).ready(function() {
        function toggleOutletFilter() {
            if ($('#filterBagian').val() === 'outlet') {
                $('#outletFilter').show();
                $('#filterButtonBagian').hide();
            } else {
                $('#outletFilter').hide();
                $('#filterButtonBagian').show();
            }
        }

        toggleOutletFilter();

        $('#filterBagian').on('change', function() {
            toggleOutletFilter();
        });
    });
</script>

<script>
    function toggleKontrak() {
        const status = document.getElementById('modalStatusEdit').value;
        const kontrakSelect = document.getElementById('modalMasaKontrakEdit');

        if (status === 'TETAP') {
            $('#kontrak-container').hide();
            kontrakSelect.disabled = true;
            kontrakSelect.value = "";
        } else if (status === 'KONTRAK') {
            $('#kontrak-container').show();
            kontrakSelect.disabled = false;
            kontrakSelect.value = "12";
        } else {
            $('#kontrak-container').hide();
            kontrakSelect.disabled = true;
        }
    }
</script>

<script>
    $(document).ready(function() {
        $('#datatablesSimple').DataTable({
            "lengthMenu": [10, 20, 50, 100],
            "pageLength": 10,
            responsive: true,
            searching: true
        });

        //detail
        $('#detailsModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Ambil data dari atribut tombol
            var id = button.data('id');
            var nama = button.data('nama');
            var nomorhp = button.data('nomorhp');
            var email = button.data('email');
            var bagian = button.data('bagian');
            var jabatan = button.data('jabatan');
            var status = button.data('status');
            var nik = button.data('nik');
            var tptlahir = button.data('tptlahir');
            var tgllahir = button.data('tgllahir');
            var alamatktp = button.data('alamatktp');
            var alamattgl = button.data('alamattgl');
            var outlet = button.data('outlet');
            var masakontrak = button.data('masakontrak');
            var awalkontrak = button.data('awalkontrak');
            var akhirkontrak = button.data('akhirkontrak');
            var saldocuti = button.data('saldocuti');
            var tglgabung = button.data('tglgabung');

            // Set data ke dalam modal
            modal.find('#modalid').val(id);
            modal.find('#modalNama').val(nama);
            modal.find('#modalNomorHp').val(nomorhp);
            modal.find('#modalEmail').val(email);
            modal.find('#modalBagian').val(bagian);
            modal.find('#modalJabatan').val(jabatan);
            modal.find('#modalStatus').val(status);
            modal.find('#modalNik').val(nik);
            modal.find('#modalTptLahir').val(tptlahir);
            modal.find('#modalTglLahir').val(tgllahir);
            modal.find('#modalAlamatKtp').val(alamatktp);
            modal.find('#modalAlamatTgl').val(alamattgl);
            modal.find('#modalOutlet').val(outlet);
            modal.find('#modalMasaKontrak').val(masakontrak);
            modal.find('#modalAwalKontrak').val(awalkontrak);
            modal.find('#modalAkhirKontrak').val(akhirkontrak);
            modal.find('#modalSaldoCuti').val(saldocuti);
            modal.find('#modalTglGabung').val(tglgabung);
        });

        //edit
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Ambil data dari atribut tombol
            var id = button.data('id');
            var nama = button.data('nama');
            var nomorhp = button.data('nomorhp');
            var email = button.data('email');
            var bagian = button.data('bagian');
            var jabatan = button.data('jabatan');
            var status = button.data('status');
            var nik = button.data('nik');
            var tptlahir = button.data('tptlahir');
            var tgllahir = button.data('tgllahir');
            var alamatktp = button.data('alamatktp');
            var alamattgl = button.data('alamattgl');
            var outlet = button.data('outlet');
            var masakontrak = button.data('masakontrak');
            var awalkontrak = button.data('awalkontrak');
            var akhirkontrak = button.data('akhirkontrak');
            var saldocuti = button.data('saldocuti');

            // Set data ke dalam modal
            modal.find('#modalid').val(id);
            modal.find('#modalNama').val(nama);
            modal.find('#modalNomorHp').val(nomorhp);
            modal.find('#modalEmail').val(email);

            modal.find('#modalBagian option').each(function() {
                if ($(this).text() == bagian) {
                    $(this).prop('selected', true);
                }
            });

            //ambil data jabatan berdasarkan bagian yang dipilih
            var bagianNew = modal.find('#modalBagian').val();
            if (bagianNew == '3') {
                $('#outlet-container').show();
            } else {
                $('#outlet-container').hide();
            }

            if (bagianNew) {
                $.ajax({
                    url: '/get-jabatan/' + bagianNew,
                    method: 'GET',
                    success: function(data) {
                        var jabatanSelect = modal.find('#modalJabatan');
                        jabatanSelect.empty(); // Hapus opsi yang ada
                        $.each(data, function(index, jabatan) {
                            jabatanSelect.append('<option value="' + jabatan.ID_JABATAN + '">' + jabatan.NAMA_JABATAN + '</option>');
                        });

                        // Setelah semua opsi ditambahkan, set opsi yang sesuai dengan data 'jabatan' dari tombol
                        jabatanSelect.find('option').each(function() {
                            if ($(this).text() == jabatan) {
                                $(this).prop('selected', true); // Set opsi jabatan yang sesuai sebagai terpilih
                            }
                        });
                    }
                });
            }

            // Tambahkan event change untuk dropdown Bagian di dalam modal
            modal.find('#modalBagian').change(function() {
                var selectedBagian = $(this).val();
                var jabatanSelect = modal.find('#modalJabatan');
                jabatanSelect.empty();

                if (selectedBagian == '3') { // Jika bagian adalah "OUTLET"
                    $('#outlet-container').show(); // Tampilkan dropdown Outlet
                } else {
                    $('#outlet-container').hide(); // Sembunyikan dropdown Outlet
                }

                if (selectedBagian) {
                    // Panggil AJAX lagi saat bagian diubah
                    $.ajax({
                        url: '/get-jabatan/' + selectedBagian,
                        method: 'GET',
                        success: function(data) {
                            $.each(data, function(index, jabatan) {
                                jabatanSelect.append('<option value="' + jabatan.ID_JABATAN + '">' + jabatan.NAMA_JABATAN + '</option>');
                            });
                        }
                    });
                }
            });

            //ambil value outlet
            if (outlet) {
                $.ajax({
                    url: '/get-outlet',
                    method: 'GET',
                    success: function(data) {
                        var outletSelect = modal.find('#modalOutlet');
                        outletSelect.empty(); // Hapus opsi yang ada
                        $.each(data, function(index, item) {
                            outletSelect.append('<option value="' + item.ID_OUTLET + '">' + item.NAMA + '</option>');
                        });

                        //set opsi yang sesuai
                        outletSelect.find('option').each(function() {
                            if ($(this).text() == outlet) {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                });
            }

            modal.find('#modalBagian').change(function() {
                var selectedOutlet = $(this).val();
                var outletSelect = modal.find('#modalOutlet');
                outletSelect.empty();

                if (selectedOutlet) {
                    $.ajax({
                        url: '/get-outlet',
                        method: 'GET',
                        success: function(data) {
                            var outletSelect = modal.find('#modalOutlet');
                            outletSelect.empty(); // Hapus opsi yang ada
                            $.each(data, function(index, item) {
                                outletSelect.append('<option value="' + item.ID_OUTLET + '">' + item.NAMA + '</option>');
                            });
                        }
                    });
                }
            });

            modal.find('#modalStatusEdit').val(status);
            modal.find('#modalNik').val(nik);
            modal.find('#modalTptLahir').val(tptlahir);
            modal.find('#modalTglLahir').val(tgllahir);
            modal.find('#modalAlamatKtp').val(alamatktp);
            modal.find('#modalAlamatTgl').val(alamattgl);

            modal.find('#modalMasaKontrakEdit option').each(function() {
                if ($(this).text() == masakontrak) {
                    $(this).prop('selected', true);
                }
            });

            modal.find('#modalAwalKontrak').val(awalkontrak);
            modal.find('#modalAkhirKontrak').val(akhirkontrak);
            modal.find('#modalSaldoCuti').val(saldocuti);

            if (status === 'TETAP') {
                $('#kontrak-container').hide();
            } else if (status === 'KONTRAK') {
                $('#kontrak-container').show();
            } else {
                $('#kontrak-container').hide();
            }
        });

    });
</script>

@endsection
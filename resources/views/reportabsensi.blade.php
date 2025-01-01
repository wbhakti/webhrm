@extends('sb-admin.layouts.app')

@section('content')
<h1 class="mt-4">Report Absensi Karyawan</h1>
<ol class="breadcrumb mb-4">
    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
    <li class="breadcrumb-item active">Report Absensi Karyawan</li>
</ol>
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-clock"></i> Report Absensi
    </div>
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="GET" action="/getreportabsen">
                    @csrf
                    @if(isset($outlets) && $outlets->isNotEmpty())
                        <div class="mb-3">
                            <label for="outlet" class="form-label">Outlet</label>
                            <select class="form-control" id="outlet" name="outlet" required>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->ID_OUTLET }}" {{ request('outlet') == $outlet->ID_OUTLET ? 'selected' : '' }}>
                                        {{ $outlet->NAMA }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ isset($start_date) ? $start_date : old('start_date') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ isset($end_date) ? $end_date : old('end_date') }}" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-50" name="action" value="report">Lihat Laporan</button>
                        <button type="submit" class="btn btn-success w-50" name="action" value="download">Download Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>

@if(isset($attendances) && count($attendances) > 0)
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-table"></i> Daftar Presensi Karyawan {{ $startdate }} sampai {{ $enddate }}
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="datatablesSimple" class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Keterangan</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($attendances as $index => $attendance)
                        @foreach ($attendance['absen'] as $absenIndex => $absen)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $attendance['name'] }}</td>
                                <td>{{ date('d-m-Y', strtotime($absen['datang'])) }}</td>
                                <td>{{ date('H:i:s', strtotime($absen['datang'])) }}</td>
                                <td>{{ date('H:i:s', strtotime($absen['pulang'])) }}</td>
                                <td>
                                    @if(!$absen['datang'])
                                        Lupa absen datang
                                    @elseif(!$absen['pulang'])
                                        Lupa absen pulang
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#fotoModal{{ $index }}_{{ $absenIndex }}">
                                        Show Foto
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        
    </div>
</div>
@else
<div class="card mb-4">
    <div class="card-body text-center">
        <p class="text-muted">Data presensi tidak tersedia untuk rentang tanggal yang dipilih.</p>
    </div>
</div>
@endif

<!-- Modal: Foto Presensi -->
@if(isset($attendances) && count($attendances) > 0)
@foreach ($attendances as $index => $attendance)
@foreach ($attendance['absen'] as $absenIndex => $absen)
    <div class="modal fade" id="fotoModal{{ $index }}_{{ $absenIndex }}" tabindex="-1" aria-labelledby="fotoModalLabel{{ $index }}_{{ $absenIndex }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel{{ $index }}_{{ $absenIndex }}">Foto Presensi {{ $attendance['name'] }} | {{ date('Y-m-d', strtotime($absen['datang'])) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="me-2">
                            <img src="{{ url('/webhrm/public/uploads').'/'.$absen['foto_datang'] }}" alt="Foto Datang" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <p class="mt-2">Datang</p>
                        </div>
                        <div class="ms-2">
                            <img src="{{ url('/webhrm/public/uploads').'/'.$absen['foto_pulang'] }}" alt="Foto Pulang" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                            <p class="mt-2">Pulang</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endforeach
@else
@endif

@if (session('error'))
        <script>
            alert('{{ session('error') }}');
        </script>
@endif

<script>
    $(document).ready(function() {
        // Inisialisasi DataTables
        $('#datatablesSimple').DataTable({
            "lengthMenu": [10, 20, 50, 100],
            "pageLength": 5,
            responsive: true,
            searching: true
        });
    });
</script>

@endsection

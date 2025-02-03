@extends('sb-admin.layouts.app')

@section('content')

    <h1 class="mt-4">Report Cuti</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Report Cuti</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-clock"></i> Report Cuti
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="GET" action="/dashboard/report-cuti">
                        @csrf
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ isset($start_date) ? $start_date : old('start_date') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ isset($end_date) ? $end_date : old('end_date') }}" required>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-50" name="action" value="report">Lihat Riwayat</button>
                            <button type="submit" class="btn btn-success w-50" name="action" value="download">Download Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    </div>

    @if(isset($listData) && !$listData->isEmpty())
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table"></i> Cuti Karyawan {{ $startdate }} sampai {{ $enddate }}
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Jumlah Cuti</th>
                        <th>Mulai Cuti</th>
                        <th>Selesai Cuti</th>
                        <th>Alasan Cuti</th>
                        <th>Status Cuti</th>
                        <th>Alasan Ditolak</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listData as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama_karyawan }}</td>
                        <td>{{ $item->JUMLAH_HARI }} hari</td>
                        <td>{{ \Carbon\Carbon::parse($item->TANGGAL_AWAL)->format('d-m-Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->TANGGAL_AKHIR)->format('d-m-Y') }}</td>
                        <td>{{ $item->alasan_cuti }}</td>
                        <td>
                            @if($item->status_cuti == 'DITERIMA')
                                <span class="text-success fw-bold">{{ $item->status_cuti }}</span>
                            @else
                                <span class="text-danger fw-bold">{{ $item->status_cuti }}</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status_cuti == 'DITOLAK')
                                {{ $item->KETERANGAN }}
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="card mb-4">
        <div class="card-body text-center">
            <p class="text-muted">Data riwayat cuti tidak tersedia untuk rentang tanggal yang dipilih.</p>
        </div>
    </div>
    @endif


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

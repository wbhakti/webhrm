@extends('sb-admin.layouts.app')

@section('content')

    <h1 class="mt-4">Riwayat Cuti</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Riwayat Cuti</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Riwayat Cuti
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
                    @if(isset($listData) && !$listData->isEmpty())
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
                    @else
                    @endif
                </tbody>
            </table>
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

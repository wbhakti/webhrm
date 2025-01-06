@extends('sb-admin.layouts.app')

@section('content')

    <h1 class="mt-4">Approval Cuti</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
        <li class="breadcrumb-item active">Approval Cuti</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Approval Cuti
        </div>
        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <form method="GET" action="/dashboard/approval-cuti">
                        <div class="input-group">
                            <label class="input-group-text" for="statusFilter">Status Cuti</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approve" {{ request('status') == 'approve' ? 'selected' : '' }}>Approve</option>
                                <option value="reject" {{ request('status') == 'reject' ? 'selected' : '' }}>Reject</option>
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
                        <th>Nama Karyawan</th>
                        <th>Jumlah Cuti</th>
                        <th>Mulai Cuti</th>
                        <th>Selesai Cuti</th>
                        <th>Alasan Cuti</th>
                        <th>Outlet</th>
                        <th>Status Cuti</th>
                        <th>Alasan Ditolak</th>
                        <th>Action</th>
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
                        <td>{{ $item->nama_outlet }}</td>
                        <td>{{ $item->status_cuti }}</td>
                        <td>
                            @if($item->status_cuti == 'DITOLAK')
                                {{ $item->KETERANGAN }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($item->status_cuti == 'PENDING')
                                <form action="/postapprovalcuti" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="idcuti" value="{{ $item->ID_CUTI }}">
                                    <!-- Tombol Approve -->
                                    <button type="submit" class="btn btn-success btn-sm me-2" name="action" value="approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui cuti ini?');">
                                        APPROVE
                                    </button>
                                    <!-- Tombol Reject -->
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="setRejectModalValue('{{ $item->ID_CUTI }}')">
                                        REJECT
                                    </button>
                                </form>
                            @else
                                <button type="submit" class="btn btn-success btn-sm me-2" disabled>
                                    APPROVE
                                </button>
                                <!-- Tombol Reject -->
                                <button type="button" class="btn btn-danger btn-sm" disabled>
                                    REJECT
                                </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    @endif
                </tbody>
            </table>
        </div>

         <!-- Modal Reject -->
         <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Alasan Penolakan Cuti</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="rejectForm" action="/postapprovalcuti" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="idcuti" id="rejectCutiId">
                            <input type="hidden" name="action" value="reject">
                            <div class="mb-3">
                                <label for="reason" class="form-label">Masukkan Alasan:</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>   
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Tolak Cuti</button>
                        </div>
                    </form>
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
        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#datatablesSimple').DataTable({
                "lengthMenu": [10, 20, 50, 100],
                "pageLength": 10,
                responsive: true,
                searching: true
            });
        });
    </script>
    
    <script>
        function setRejectModalValue(idCuti) {
            document.getElementById('rejectCutiId').value = idCuti;
        }
    </script>

@endsection

@extends('sb-admin.layouts.app')

@section('content')
    <style>
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #ced4da;
            color: black;
            font-weight: bold;
            border-radius: 12px 12px 0 0;
        }

        .profile-section {
            text-align: center;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #007bff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            margin-top: 15px;
        }

        .profile-info h5 {
            font-size: 20px;
            font-weight: bold;
        }

        .profile-info p {
            font-size: 14px;
            color: #6c757d;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .detail-row p {
            margin: 0;
            font-size: 16px;
        }

        .detail-row i {
            margin-right: 10px;
            color: #007bff;
        }

        hr {
            margin: 0;
        }
    </style>

    <!-- User Profile Section -->
    <div class="row justify-content-center">
        <div class="text-center">
            <h2 class="mt-4">Profile Karyawan</h4>
            </br> <!-- Title di atas profile image -->
        </div>
        <div class="col-lg-8">
            
            <div class="card mb-4">
                <!-- Profile Section -->
                <div class="profile-section card-header">
                    <img src="{{ asset('assets/img/img-profile.png') }}" alt="avatar" class="profile-image" style="margin-top: 15px;">
                    <div class="profile-info">
                        <h5 class="my-3">{{ $user->NAMA }}</h5>
                        <p class="text-muted mb-1">{{ $user->nama_bagian }}</p>
                        <p class="text-muted mb-4">{{ $user->nama_jabatan }}</p>
                    </div>
                </div>

                <!-- Detail Information Section -->
                <div class="card-body">
                    <!-- User Details -->
                    <div class="detail-row">
                        <p><i class="fas fa-user"></i> Nama Lengkap</p>
                        <p class="text-muted">{{ $user->NAMA }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-id-card"></i> NIK</p>
                        <p class="text-muted">{{ $user->NIK }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-briefcase"></i> Status Karyawan</p>
                        <p class="text-muted">{{ $user->STATUS_KARYAWAN }}</p>
                    </div>
                    <hr>

                    @if($user->nama_toko != null)
                        <div class="detail-row">
                            <p><i class="fas fa-store"></i> Outlet</p>
                            <p class="text-muted">{{ $user->nama_toko }}</p>
                        </div>
                        <hr>
                    @endif

                    <div class="detail-row">
                        <p><i class="fas fa-phone"></i> Nomor Telepon</p>
                        <p class="text-muted">{{ $user->NO_HP }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-envelope"></i> Email</p>
                        <p class="text-muted">{{ $user->EMAIL }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-map-marker-alt"></i> Tempat Lahir</p>
                        <p class="text-muted">{{ $user->TEMPAT_LAHIR }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-calendar-alt"></i> Tanggal Lahir</p>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($user->TANGGAL_LAHIR)->format('d-m-Y') }}</p>
                    </div>
                    <hr>
                    
                    <div class="detail-row">
                        <p><i class="fas fa-home"></i> Alamat KTP</p>
                        <p class="text-muted">{{ $user->ALAMAT_KTP }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-home"></i> Alamat Tinggal</p>
                        <p class="text-muted">{{ $user->ALAMAT_TINGGAL }}</p>
                    </div>
                    <hr>
                    <div class="detail-row">
                        <p><i class="fas fa-briefcase"></i> Tanggal Bergabung</p>
                        <p class="text-muted">{{ \Carbon\Carbon::parse($user->TANGGAL_BERGABUNG)->format('d-m-Y') }}</p>
                    </div>
                    <hr>

                    @if($user->STATUS_KARYAWAN !== 'TETAP')
                        <div class="detail-row">
                            <p><i class="fas fa-file-contract"></i> Masa Kontrak</p>
                            <p class="text-muted">{{ $user->MASA_KONTRAK }}</p>
                        </div>
                        <hr>
                        <div class="detail-row">
                            <p><i class="fas fa-calendar-alt"></i> Akhir Kontrak</p>
                            <p class="text-muted">
                                {{ \Carbon\Carbon::parse($user->AKHIR_KONTRAK)->format('d-m-Y') }}
                            </p>                        
                        </div>
                        <hr>
                    @endif
                    
                    <div class="detail-row">
                        <p><i class="fas fa-calendar-check"></i> Saldo Cuti</p>
                        <p class="text-muted">{{ $user->SALDO_CUTI }} hari</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of User Profile Section -->

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
@endsection

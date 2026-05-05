@extends('adminlte::page')

@section('title', 'Data Karyawan')

@section('content_header')
    <h1>Data Karyawan</h1>
@stop

@push('js')
    @include('partials.submit-loading-guard')
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Karyawan</h3>
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Karyawan
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('employees.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama, NIK, jabatan, atau no. HP..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak aktif" {{ request('status') == 'tidak aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="jabatan" class="form-control">
                                <option value="">-- Semua Jabatan --</option>
                                @foreach($jabatanList as $jab)
                                    <option value="{{ $jab }}" {{ request('jabatan') == $jab ? 'selected' : '' }}>{{ $jab }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'jabatan']))
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-block mt-2">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($employees->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tidak ada data karyawan.
                </div>
            @else
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Foto</th>
                                <th width="12%">NIK</th>
                                <th width="15%">Nama</th>
                                <th width="12%">Jabatan</th>
                                <th width="10%">No. HP</th>
                                <th width="10%">Tanggal Bergabung</th>
                                <th width="10%">Gaji</th>
                                <th width="8%">Status</th>
                                <th width="8%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                                    <td class="text-center">
                                        @if($employee->foto)
                                            <img src="{{ asset('storage/' . $employee->foto) }}" alt="{{ $employee->nama }}" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">
                                        @else
                                            <span class="badge badge-secondary">No Photo</span>
                                        @endif
                                    </td>
                                    <td>{{ $employee->nik }}</td>
                                    <td>{{ $employee->nama }}</td>
                                    <td>{{ $employee->jabatan }}</td>
                                    <td>{{ $employee->no_hp }}</td>
                                    <td>{{ $employee->tanggal_bergabung->format('d M Y') }}</td>
                                    <td>Rp {{ number_format($employee->gaji, 0, ',', '.') }}</td>
                                    <td>
                                        @if($employee->status == 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

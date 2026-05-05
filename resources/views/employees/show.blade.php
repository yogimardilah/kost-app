@extends('adminlte::page')

@section('title', 'Detail Karyawan')

@section('content_header')
    <h1>Detail Karyawan</h1>
@stop

@push('js')
    @include('partials.submit-loading-guard')
@endpush

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $employee->nama }}</h3>
            <div class="card-tools">
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">NIK</th>
                            <td>{{ $employee->nik }}</td>
                        </tr>
                        <tr>
                            <th>Nama Lengkap</th>
                            <td>{{ $employee->nama }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>{{ $employee->jabatan }}</td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td>{{ $employee->no_hp }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $employee->alamat ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Bergabung</th>
                            <td>{{ $employee->tanggal_bergabung->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Berakhir</th>
                            <td>{{ $employee->tanggal_berakhir ? $employee->tanggal_berakhir->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Gaji</th>
                            <td>Rp {{ number_format($employee->gaji, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Gajian</th>
                            <td>Setiap tanggal {{ $employee->tanggal_gajian }} per bulan</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($employee->status == 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $employee->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $employee->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    @if($employee->foto)
                        <div class="text-center">
                            <img src="{{ asset('storage/' . $employee->foto) }}" alt="{{ $employee->nama }}" class="img-fluid img-thumbnail">
                            <p class="mt-2 text-muted">Foto Karyawan</p>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-user fa-3x mb-2"></i>
                            <p>Tidak ada foto</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer">
            <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data karyawan ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
@stop

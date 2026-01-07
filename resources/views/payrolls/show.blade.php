@extends('adminlte::page')

@section('title', 'Detail Payroll')

@section('content_header')
    <h1>Detail Payroll</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $payroll->employee->nama }} - {{ $payroll->periode }}</h3>
            <div class="card-tools">
                <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Informasi Karyawan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Nama</th>
                            <td>{{ $payroll->employee->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIK</th>
                            <td>{{ $payroll->employee->nik }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan</th>
                            <td>{{ $payroll->employee->jabatan }}</td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td>{{ $payroll->employee->no_hp }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Detail Payroll</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Periode</th>
                            <td><strong>{{ $payroll->periode }}</strong></td>
                        </tr>
                        <tr>
                            <th>Gaji Pokok</th>
                            <td>Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Bonus</th>
                            <td class="text-success">+ Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Potongan</th>
                            <td class="text-danger">- Rp {{ number_format($payroll->potongan, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-light">
                            <th>Total Gaji</th>
                            <td><strong class="text-primary">Rp {{ number_format($payroll->total_gaji, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($payroll->status == 'dibayar')
                                    <span class="badge badge-success badge-lg">Dibayar</span>
                                @else
                                    <span class="badge badge-warning badge-lg">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <td>{{ $payroll->tanggal_bayar ? $payroll->tanggal_bayar->format('d F Y') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($payroll->keterangan)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5>Keterangan</h5>
                    <div class="card">
                        <div class="card-body">
                            {{ $payroll->keterangan }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="row mt-3">
                <div class="col-md-12">
                    <table class="table table-sm">
                        <tr>
                            <th width="20%">Dibuat</th>
                            <td>{{ $payroll->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $payroll->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            @if($payroll->status == 'pending')
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#markPaidModal">
                    <i class="fas fa-check"></i> Tandai Sebagai Dibayar
                </button>
            @endif
            <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data payroll ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- Mark as Paid Modal -->
    @if($payroll->status == 'pending')
    <div class="modal fade" id="markPaidModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('payrolls.mark-paid', $payroll) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Tandai Sebagai Dibayar</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Karyawan</label>
                            <input type="text" class="form-control" value="{{ $payroll->employee->nama }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Total Gaji</label>
                            <input type="text" class="form-control" value="Rp {{ number_format($payroll->total_gaji, 0, ',', '.') }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_bayar">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tandai Dibayar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@stop

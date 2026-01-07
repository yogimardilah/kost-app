@extends('adminlte::page')

@section('title', 'Payroll')

@section('content_header')
    <h1>Payroll</h1>
@stop

@section('content')
    <!-- Summary Cards -->
    <div class="row mb-3">
        <div class="col-lg-6 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Rp {{ number_format($totalPending, 0, ',', '.') }}</h3>
                    <p>Total Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h3>
                    <p>Total Dibayar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Payroll</h3>
                <a href="{{ route('payrolls.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Payroll
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Search & Filter Form -->
            <form method="GET" action="{{ route('payrolls.index') }}" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama/NIK karyawan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="bulan" class="form-control">
                                <option value="">-- Semua Bulan --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="tahun" class="form-control">
                                <option value="">-- Semua Tahun --</option>
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="">-- Semua Status --</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <select name="employee_id" class="form-control">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        @if(request()->anyFilled(['search', 'bulan', 'tahun', 'status', 'employee_id']))
                            <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            @if($payrolls->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Tidak ada data payroll.
                </div>
            @else
                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">Periode</th>
                                <th width="15%">Karyawan</th>
                                <th width="10%">Gaji Pokok</th>
                                <th width="10%">Bonus</th>
                                <th width="10%">Potongan</th>
                                <th width="10%">Total Gaji</th>
                                <th width="10%">Tanggal Bayar</th>
                                <th width="8%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payrolls as $payroll)
                                <tr>
                                    <td>{{ ($payrolls->currentPage() - 1) * $payrolls->perPage() + $loop->iteration }}</td>
                                    <td>{{ $payroll->periode }}</td>
                                    <td>
                                        <strong>{{ $payroll->employee->nama }}</strong><br>
                                        <small class="text-muted">{{ $payroll->employee->nik }}</small>
                                    </td>
                                    <td>Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($payroll->potongan, 0, ',', '.') }}</td>
                                    <td><strong>Rp {{ number_format($payroll->total_gaji, 0, ',', '.') }}</strong></td>
                                    <td>{{ $payroll->tanggal_bayar ? $payroll->tanggal_bayar->format('d M Y') : '-' }}</td>
                                    <td>
                                        @if($payroll->status == 'dibayar')
                                            <span class="badge badge-success">Dibayar</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($payroll->status == 'pending')
                                                <button type="button" class="btn btn-sm btn-success" title="Tandai Dibayar" 
                                                        data-toggle="modal" data-target="#markPaidModal{{ $payroll->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data payroll ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Mark as Paid Modal -->
                                @if($payroll->status == 'pending')
                                <div class="modal fade" id="markPaidModal{{ $payroll->id }}" tabindex="-1" role="dialog">
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
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

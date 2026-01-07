<div class="card-body">
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-6">
            <!-- Karyawan -->
            <div class="form-group">
                <label for="employee_id">Karyawan <span class="text-danger">*</span></label>
                <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" 
                                data-gaji="{{ $emp->gaji }}"
                                data-tanggal-bergabung="{{ $emp->tanggal_bergabung->format('Y-m-d') }}"
                                data-tanggal-berakhir="{{ $emp->tanggal_berakhir ? $emp->tanggal_berakhir->format('Y-m-d') : '' }}"
                                {{ old('employee_id', $payroll->employee_id ?? '') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->nama }} - {{ $emp->jabatan }}
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Bulan -->
            <div class="form-group">
                <label for="bulan">Periode Bulan <span class="text-danger">*</span></label>
                <select name="bulan" id="bulan" class="form-control @error('bulan') is-invalid @enderror" required>
                    <option value="">-- Pilih Bulan --</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('bulan', $payroll->bulan ?? '') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
                @error('bulan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tahun -->
            <div class="form-group">
                <label for="tahun">Periode Tahun <span class="text-danger">*</span></label>
                <input type="number" name="tahun" id="tahun" class="form-control @error('tahun') is-invalid @enderror" 
                       value="{{ old('tahun', $payroll->tahun ?? date('Y')) }}" required min="2020" max="2100">
                @error('tahun')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gaji Pokok -->
            <div class="form-group">
                <label for="gaji_pokok">Gaji Pokok <span class="text-danger">*</span></label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" name="gaji_pokok" id="gaji_pokok" class="form-control @error('gaji_pokok') is-invalid @enderror" 
                           value="{{ old('gaji_pokok', $payroll->gaji_pokok ?? '') }}" required min="0">
                </div>
                <small class="form-text text-muted">Akan otomatis terisi sesuai gaji karyawan</small>
                @error('gaji_pokok')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Prorate Info (Hidden initially) -->
            <div id="prorate-info" class="alert alert-info" style="display: none;">
                <h6><i class="fas fa-calculator"></i> Perhitungan Prorate</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td width="50%">Gaji Bulanan:</td>
                        <td class="text-right"><strong id="gaji-full"></strong></td>
                    </tr>
                    <tr>
                        <td>Total Hari Bulan Ini:</td>
                        <td class="text-right"><span id="total-hari"></span> hari</td>
                    </tr>
                    <tr>
                        <td>Hari Kerja:</td>
                        <td class="text-right"><span id="hari-kerja"></span> hari</td>
                    </tr>
                    <tr>
                        <td>Gaji Per Hari:</td>
                        <td class="text-right"><span id="gaji-per-hari"></span></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Total (Prorate):</strong></td>
                        <td class="text-right"><strong class="text-primary" id="gaji-prorate"></strong></td>
                    </tr>
                </table>
                <small class="text-muted" id="prorate-reason"></small>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-md-6">
            <!-- Bonus -->
            <div class="form-group">
                <label for="bonus">Bonus</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" name="bonus" id="bonus" class="form-control @error('bonus') is-invalid @enderror" 
                           value="{{ old('bonus', $payroll->bonus ?? 0) }}" min="0">
                </div>
                @error('bonus')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Potongan -->
            <div class="form-group">
                <label for="potongan">Potongan</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="number" name="potongan" id="potongan" class="form-control @error('potongan') is-invalid @enderror" 
                           value="{{ old('potongan', $payroll->potongan ?? 0) }}" min="0">
                </div>
                @error('potongan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Total Gaji (Display Only) -->
            <div class="form-group">
                <label>Total Gaji</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" id="total_gaji_display" class="form-control bg-light" readonly>
                </div>
                <small class="form-text text-muted">Gaji Pokok + Bonus - Potongan</small>
            </div>

            <!-- Status -->
            <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                    <option value="pending" {{ old('status', $payroll->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="dibayar" {{ old('status', $payroll->status ?? '') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                </select>
                @error('status')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tanggal Bayar (only show if status dibayar) -->
            <div class="form-group" id="tanggal_bayar_group" style="display: none;">
                <label for="tanggal_bayar">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" id="tanggal_bayar" class="form-control @error('tanggal_bayar') is-invalid @enderror" 
                       value="{{ old('tanggal_bayar', isset($payroll) && $payroll->tanggal_bayar ? $payroll->tanggal_bayar->format('Y-m-d') : '') }}">
                @error('tanggal_bayar')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $payroll->keterangan ?? '') }}</textarea>
                @error('keterangan')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- File Upload -->
            <div class="form-group">
                <label for="file">Upload File</label>
                <input type="file" name="file" id="file" class="form-control-file @error('file') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" onchange="previewPayrollFile(this)">
                @error('file')
                    <span class="invalid-feedback d-block">{{ $message }}</span>
                @enderror
                
                @if(isset($payroll) && $payroll->file_path)
                    <div class="mt-2" id="currentFilePreview">
                        <label class="d-block"><strong>File Saat Ini:</strong></label>
                        @php
                            $extension = strtolower(pathinfo($payroll->file_path, PATHINFO_EXTENSION));
                            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                        @endphp
                        
                        @if($isImage)
                            <div class="border rounded p-2" style="max-width: 400px;">
                                <img src="{{ Storage::url($payroll->file_path) }}" alt="File Payroll" class="img-fluid rounded" style="max-height: 300px;">
                                <div class="mt-2">
                                    <a href="{{ Storage::url($payroll->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-external-link-alt"></i> Lihat Full Size
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-file"></i> 
                                <a href="{{ Storage::url($payroll->file_path) }}" target="_blank" class="alert-link">
                                    {{ basename($payroll->file_path) }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
                
                <div id="newFilePreview" class="mt-2" style="display: none;">
                    <label class="d-block"><strong>Preview File Baru:</strong></label>
                    <div class="border rounded p-2" style="max-width: 400px;">
                        <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="max-height: 300px; display: none;">
                        <div id="previewDoc" class="alert alert-info" style="display: none;">
                            <i class="fas fa-file"></i> <span id="docFileName"></span>
                        </div>
                    </div>
                </div>
                
                <small class="form-text text-muted">
                    Format: PDF, JPG, PNG, DOC, DOCX (Max: 5MB)<br>
                    <i class="fas fa-info-circle"></i> Gambar akan dikompres otomatis
                </small>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    function previewPayrollFile(input) {
        const preview = document.getElementById('newFilePreview');
        const previewImage = document.getElementById('previewImage');
        const previewDoc = document.getElementById('previewDoc');
        const docFileName = document.getElementById('docFileName');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            
            preview.style.display = 'block';
            
            if (file.type.startsWith('image/')) {
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewDoc.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.style.display = 'none';
                previewDoc.style.display = 'block';
                docFileName.textContent = file.name;
            }
        } else {
            preview.style.display = 'none';
        }
    }

    let employeeData = {
        gaji: 0,
        tanggalBergabung: null,
        tanggalBerakhir: null
    };

    // Auto fill gaji pokok when employee selected
    document.getElementById('employee_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const gaji = selectedOption.getAttribute('data-gaji');
        const tanggalBergabung = selectedOption.getAttribute('data-tanggal-bergabung');
        const tanggalBerakhir = selectedOption.getAttribute('data-tanggal-berakhir');
        
        if (gaji) {
            employeeData.gaji = parseFloat(gaji);
            employeeData.tanggalBergabung = tanggalBergabung;
            employeeData.tanggalBerakhir = tanggalBerakhir;
            
            calculateProrate();
        }
    });

    // Calculate prorate when bulan or tahun changes
    document.getElementById('bulan').addEventListener('change', calculateProrate);
    document.getElementById('tahun').addEventListener('change', calculateProrate);

    function calculateProrate() {
        const bulan = parseInt(document.getElementById('bulan').value);
        const tahun = parseInt(document.getElementById('tahun').value);
        
        if (!bulan || !tahun || !employeeData.gaji) {
            document.getElementById('prorate-info').style.display = 'none';
            return;
        }

        // Get first and last day of the month
        const firstDay = new Date(tahun, bulan - 1, 1);
        const lastDay = new Date(tahun, bulan, 0);
        const daysInMonth = lastDay.getDate();

        let startDay = 1;
        let endDay = daysInMonth;
        let needProrate = false;
        let prorateReason = '';

        // Check if employee joined in this month
        if (employeeData.tanggalBergabung) {
            const joinDate = new Date(employeeData.tanggalBergabung);
            const joinYear = joinDate.getFullYear();
            const joinMonth = joinDate.getMonth() + 1;
            
            if (joinYear === tahun && joinMonth === bulan) {
                startDay = joinDate.getDate();
                needProrate = true;
                prorateReason = 'Karyawan bergabung tanggal ' + joinDate.getDate() + ' ' + firstDay.toLocaleDateString('id-ID', {month: 'long', year: 'numeric'});
            }
        }

        // Check if employee left in this month
        if (employeeData.tanggalBerakhir) {
            const endDate = new Date(employeeData.tanggalBerakhir);
            const endYear = endDate.getFullYear();
            const endMonth = endDate.getMonth() + 1;
            
            if (endYear === tahun && endMonth === bulan) {
                endDay = endDate.getDate();
                needProrate = true;
                prorateReason = 'Karyawan berakhir tanggal ' + endDate.getDate() + ' ' + firstDay.toLocaleDateString('id-ID', {month: 'long', year: 'numeric'});
            }
        }

        if (needProrate) {
            const workingDays = endDay - startDay + 1;
            const gajiPerHari = employeeData.gaji / daysInMonth;
            const gajiProrate = Math.round((gajiPerHari * workingDays) / 100) * 100; // Round to nearest 100

            // Show prorate info
            document.getElementById('gaji-full').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(employeeData.gaji);
            document.getElementById('total-hari').textContent = daysInMonth;
            document.getElementById('hari-kerja').textContent = workingDays;
            document.getElementById('gaji-per-hari').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(gajiPerHari));
            document.getElementById('gaji-prorate').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(gajiProrate);
            document.getElementById('prorate-reason').textContent = prorateReason;
            document.getElementById('prorate-info').style.display = 'block';

            // Set gaji pokok to prorated amount
            document.getElementById('gaji_pokok').value = gajiProrate;
        } else {
            // Full month salary
            document.getElementById('gaji_pokok').value = employeeData.gaji;
            document.getElementById('prorate-info').style.display = 'none';
        }

        calculateTotal();
    }

    // Calculate total gaji
    function calculateTotal() {
        const gajiPokok = parseFloat(document.getElementById('gaji_pokok').value) || 0;
        const bonus = parseFloat(document.getElementById('bonus').value) || 0;
        const potongan = parseFloat(document.getElementById('potongan').value) || 0;
        
        const total = gajiPokok + bonus - potongan;
        
        // Format as currency
        document.getElementById('total_gaji_display').value = new Intl.NumberFormat('id-ID').format(total);
    }

    // Calculate on input change
    document.getElementById('gaji_pokok').addEventListener('input', calculateTotal);
    document.getElementById('bonus').addEventListener('input', calculateTotal);
    document.getElementById('potongan').addEventListener('input', calculateTotal);

    // Show/hide tanggal bayar based on status
    function toggleTanggalBayar() {
        const status = document.getElementById('status').value;
        const tanggalBayarGroup = document.getElementById('tanggal_bayar_group');
        
        if (status === 'dibayar') {
            tanggalBayarGroup.style.display = 'block';
            document.getElementById('tanggal_bayar').required = true;
        } else {
            tanggalBayarGroup.style.display = 'none';
            document.getElementById('tanggal_bayar').required = false;
        }
    }

    document.getElementById('status').addEventListener('change', toggleTanggalBayar);

    // Initial calculation and toggle
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
        toggleTanggalBayar();
    });
</script>
@endpush

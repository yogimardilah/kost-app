@extends('layouts.app')

@section('title','Tambah Addon ke Billing')

@section('content_header')
<h1>Tambah Addon ke Billing</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div id="billing-info" style="display:none;" class="alert alert-info">
            <div class="row">
                <div class="col-md-6">
                    <strong>Invoice Billing:</strong> <span id="billing-invoice"></span><br>
                    <strong>Total Saat Ini:</strong> <span id="billing-total"></span>
                </div>
                <div class="col-md-6">
                    <strong>Total Setelah Addon:</strong> <span id="billing-new-total" class="text-success"><strong></strong></span>
                </div>
            </div>
        </div>
        
        <form action="{{ route('addon-transactions.store') }}" method="POST" id="transaction-form">
            @csrf
            
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Penyewa <span class="text-danger">*</span></label>
                    <select name="consumer_id" id="consumer_id" class="form-control" required>
                        <option value="">-- Pilih Penyewa --</option>
                        @foreach($consumers as $c)
                            <option value="{{ $c->id }}" {{ $selectedConsumerId == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->nik }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>

            <h5>Tambah Addon</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="items-table">
                    <thead>
                        <tr>
                            <th style="width: 40%">Addon</th>
                            <th style="width: 10%">Qty</th>
                            <th style="width: 20%">Harga</th>
                            <th style="width: 20%">Subtotal</th>
                            <th style="width: 10%"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total Addon</strong></td>
                            <td><strong id="grand-total">Rp 0</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addRow()">Tambah Baris</button>

            <div class="mt-3">
                <button class="btn btn-primary" id="submit-btn" type="submit">Simpan Addon</button>
                <a href="{{ route('addon-transactions.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
    const addons = @json($addons->map(function($a){ return ['id'=>$a->id,'nama'=>$a->nama_addon,'harga'=>$a->harga]; }));

    function formatRupiah(n){
        n = Number(n||0);
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    let rowIndex = 0;
    let currentBillingTotal = 0;

    async function setConsumer(consumerId){
        const billingInfo = document.getElementById('billing-info');
        
        if(!consumerId){ 
            billingInfo.style.display = 'none';
            currentBillingTotal = 0;
            return; 
        }
        
        try{
            const res = await fetch(`{{ url('addon-transactions/consumer') }}/${consumerId}/active-room`);
            const data = await res.json();
            
            if(data && data.billing){
                // Show billing info
                document.getElementById('billing-invoice').textContent = data.billing.invoice_number;
                document.getElementById('billing-total').textContent = formatRupiah(data.billing.total_tagihan);
                currentBillingTotal = Number(data.billing.total_tagihan || 0);
                billingInfo.style.display = 'block';
                updateNewTotal();
            } else {
                billingInfo.style.display = 'none';
                currentBillingTotal = 0;
                alert('Tidak ada Billing aktif untuk penyewa ini!');
            }
        }catch(e){
            console.error(e);
            billingInfo.style.display = 'none';
            currentBillingTotal = 0;
        }
    }

    function addRow(){
        const tbody = document.querySelector('#items-table tbody');
        const idx = rowIndex++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="items[${idx}][addon_id]" class="form-control addon-select" required>
                    <option value="">-- Pilih --</option>
                    ${addons.map(a=>`<option value="${a.id}" data-harga="${a.harga}">${a.nama}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="items[${idx}][qty]" class="form-control qty" min="1" value="1" required></td>
            <td><input type="number" name="items[${idx}][harga]" class="form-control harga" min="0" value="0" step="1" required></td>
            <td class="subtotal">Rp 0</td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove();calcTotal();">Hapus</button></td>
        `;
        tbody.appendChild(tr);
        bindRow(tr);
    }

    function bindRow(tr){
        const select = tr.querySelector('.addon-select');
        const qty = tr.querySelector('.qty');
        const harga = tr.querySelector('.harga');
        const subtotalCell = tr.querySelector('.subtotal');

        select.addEventListener('change', e=>{
            const opt = select.options[select.selectedIndex];
            const h = Number(opt.getAttribute('data-harga')||0);
            if(h>0){ harga.value = h; }
            calc();
        });
        qty.addEventListener('input', calc);
        harga.addEventListener('input', calc);

        function calc(){
            const sub = Number(qty.value||0) * Number(harga.value||0);
            subtotalCell.textContent = formatRupiah(sub);
            calcTotal();
        }
        calc();
    }

    function getAddonTotal(){
        let total = 0;
        document.querySelectorAll('#items-table tbody tr').forEach(tr=>{
            const qty = Number(tr.querySelector('.qty').value||0);
            const harga = Number(tr.querySelector('.harga').value||0);
            total += qty*harga;
        });
        return total;
    }

    function calcTotal(){
        const addonTotal = getAddonTotal();
        document.getElementById('grand-total').textContent = formatRupiah(addonTotal);
        updateNewTotal();
    }

    function updateNewTotal(){
        const addonTotal = getAddonTotal();
        const newTotal = currentBillingTotal + addonTotal;
        document.getElementById('billing-new-total').textContent = formatRupiah(newTotal);
    }

    // Init
    document.addEventListener('DOMContentLoaded', ()=> {
        addRow();
        
        const consumerSelect = document.getElementById('consumer_id');
        consumerSelect.addEventListener('change', (e)=> {
            setConsumer(e.target.value);
        });
    });
</script>
@endsection

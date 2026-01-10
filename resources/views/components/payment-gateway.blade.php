<div class="mt-3 shadow-sm card">
    <div class="card-header bg-light fw-bold">
        Metode Pembayaran
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="payment_method" class="form-label">Metode Pembayaran</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="">-- Pilih Metode --</option>
                <option value="cash" {{ old('payment_method', $payment_method ?? '') == 'cash' ? 'selected' : '' }}>
                    Tunai</option>
                <option value="transfer"
                    {{ old('payment_method', $payment_method ?? '') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="cash_transfer"
                    {{ old('payment_method', $payment_method ?? '') == 'cash_transfer' ? 'selected' : '' }}>Tunai &
                    Transfer</option>
            </select>
        </div>

        {{-- Transfer --}}
        <div id="transfer-section" class="mt-3" style="display:none;">
            <div class="mb-2">
                <label>Rekening Tujuan</label>
                <select name="bank_account_id" class="form-control">
                    <option value="">-- Pilih Rekening --</option>
                    @foreach ($bankAccounts as $account)
                        <option value="{{ $account->id }}"
                            {{ old('bank_account_id', $bank_account_id ?? '') == $account->id ? 'selected' : '' }}>
                            {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->account_holder }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-2">
                <label>Nominal Transfer</label>
                <input type="number" name="transfer_amount" class="form-control" step="0.01"
                    value="{{ old('transfer_amount', $transfer_amount ?? '') }}">
            </div>
        </div>

        {{-- Tunai --}}
        <div id="cash-section" class="mt-3" style="display:none;">
            <div class="mb-2">
                <label>Nominal Tunai</label>
                <input type="number" name="cash_amount" class="form-control" step="0.01"
                    value="{{ old('cash_amount', $cash_amount ?? '') }}">
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentSelect = document.getElementById('payment_method');
            const cashSection = document.getElementById('cash-section');
            const transferSection = document.getElementById('transfer-section');
            const cashInput = document.querySelector('[name="cash_amount"]');
            const transferInput = document.querySelector('[name="transfer_amount"]');

            // current total (number). Start 0 until event datang.
            let currentTotal = 0;

            // helper format to 2 decimals (string) for inputs
            function formatNum(n) {
                return Number(n || 0).toFixed(2);
            }

            // update UI according to payment method and currentTotal
            function updateAmountsUI() {
                const val = paymentSelect.value;

                // default hide both
                cashSection.style.display = 'none';
                transferSection.style.display = 'none';

                // remove any previous cash input listener to avoid duplicates
                cashInput.removeEventListener('input', cashInputHandler);

                if (val === 'cash') {
                    cashSection.style.display = 'block';
                    cashInput.value = formatNum(currentTotal);
                    transferInput.value = formatNum(0);
                    cashInput.readOnly = true;
                    transferInput.readOnly = true;
                } else if (val === 'transfer') {
                    transferSection.style.display = 'block';
                    cashInput.value = formatNum(0);
                    transferInput.value = formatNum(currentTotal);
                    cashInput.readOnly = true;
                    transferInput.readOnly = true;
                } else if (val === 'cash_transfer') {
                    // show both; user will input cash, transfer auto = total - cash
                    cashSection.style.display = 'block';
                    transferSection.style.display = 'block';

                    // default cash empty (user fills), transfer = total
                    // but if old value exists (editing), preserve it
                    if (!cashInput.value || parseFloat(cashInput.value) === 0) {
                        cashInput.value = '';
                    }
                    transferInput.value = formatNum(currentTotal - (parseFloat(cashInput.value) || 0));
                    cashInput.readOnly = false;
                    transferInput.readOnly = true;

                    // attach handler
                    cashInput.addEventListener('input', cashInputHandler);
                } else {
                    // none selected
                    cashInput.value = '';
                    transferInput.value = '';
                    cashInput.readOnly = false;
                    transferInput.readOnly = false;
                }
            }

            // handler for cash input when in cash_transfer mode
            function cashInputHandler() {
                const cashVal = parseFloat(cashInput.value) || 0;
                let transferVal = currentTotal - cashVal;
                if (transferVal < 0) transferVal = 0;
                transferInput.value = formatNum(transferVal);
            }

            // listen to change on payment method
            paymentSelect.addEventListener('change', updateAmountsUI);

            // Listen to grandTotal change event (dispatched from updateGrandTotal)
            document.addEventListener('grandTotalChanged', function(e) {
                // e.detail.total is a number (float)
                currentTotal = parseFloat(e.detail.total) || 0;
                updateAmountsUI();
            });

            // Initial: try to read grandTotal text if present (fallback)
            (function tryInitTotalFromDOM() {
                const el = document.getElementById('grandTotal');
                if (el) {
                    // remove non-numeric chars, handle id format like "1.234,56"
                    let txt = el.textContent || '';
                    txt = txt.replace(/[^\d,.-]/g, '');
                    if (txt.includes(',')) {
                        // e.g. "1.234,56" -> "1234.56"
                        currentTotal = parseFloat(txt.replace(/\./g, '').replace(',', '.')) || 0;
                    } else {
                        currentTotal = parseFloat(txt.replace(/\./g, '')) || 0;
                    }
                }
                // set UI initially
                updateAmountsUI();
            })();
        });
    </script>
@endpush

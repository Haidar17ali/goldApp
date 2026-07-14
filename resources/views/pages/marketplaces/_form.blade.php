<div class="card">

    <div class="card-header">
        <h3 class="card-title">
            Informasi Marketplace
        </h3>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <div class="form-group">

                    <label>
                        Nama Marketplace
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $marketplace->name ?? '') }}" placeholder="Contoh : TikTok Shop">

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

            <div class="col-md-6">

                <div class="form-group">

                    <label>
                        Code
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code', $marketplace->code ?? '') }}" placeholder="Contoh : tiktok">

                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

        <div class="form-group">

            <label>Logo</label>

            <input type="file" name="logo" class="form-control-file @error('logo') is-invalid @enderror">

            @error('logo')
                <div class="mt-1 text-danger">
                    {{ $message }}
                </div>
            @enderror

            @isset($marketplace)
                @if ($marketplace->logo)
                    <div class="mt-3">
                        <img src="{{ asset('storage/' . $marketplace->logo) }}" width="80" class="img-thumbnail">
                    </div>
                @endif
            @endisset

        </div>

        <div class="form-group">

            <label>Status</label>

            <select name="is_active" class="form-control">

                <option value="1" {{ old('is_active', $marketplace->is_active ?? 1) == 1 ? 'selected' : '' }}>
                    Aktif
                </option>

                <option value="0" {{ old('is_active', $marketplace->is_active ?? 1) == 0 ? 'selected' : '' }}>
                    Nonaktif
                </option>

            </select>

        </div>

    </div>

    <div class="card-footer">

        <button class="btn btn-primary">

            <i class="fas fa-save"></i>

            Simpan

        </button>

        <a href="{{ route('marketplace.index') }}" class="btn btn-secondary">

            Batal

        </a>

    </div>

</div>

<div class="card mt-4">
    <div class="card-body">
        <form action="{{ route('admin.business-settings.store.store-kilometer-wise-delivery-charge') }}" method="POST">
            @csrf
            <div class="row">
                <input type="hidden" name="branch_id" id="" value="{{ $branch->id }}">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="per_km_charge">Biaya Pengiriman Per KM (Rp)</label>
                        <input type="number" class="form-control" name="delivery_charge_per_kilometer" min="0" max="99999999" step="0.0001"
                               value="{{ $branch?->delivery_charge_setup?->delivery_charge_per_kilometer }}" id="delivery_charge_per_kilometer" placeholder="Contoh : 7000" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_delivery_charge">Biaya Pengiriman Minimum (Rp)</label>
                        <input type="number" class="form-control" name="minimum_delivery_charge" min="0" max="99999999" step="0.0001"
                               value="{{ $branch?->delivery_charge_setup?->minimum_delivery_charge }}" id="minimum_delivery_charge" placeholder="Contoh : 7000" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="min_distance_free_delivery">Jarak Minimum Pengiriman Gratis (Km)</label>
                        <input type="number" class="form-control" name="minimum_distance_for_free_delivery" min="0" max="99999999" step="0.0001"
                               value="{{ $branch?->delivery_charge_setup?->minimum_distance_for_free_delivery }}" id="minimum_distance_for_free_delivery" placeholder="Contoh : 10" required>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-3 mt-4">
                <button type="reset" id="reset" class="btn btn-secondary">Atur Ulang</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

@extends('user.layouts.dashboard')

@section('dashboard-content')
    <div class="page-header">
        <h1>📍 My Addresses</h1>
        <p>Manage your shipping and billing addresses</p>
    </div>

    {{-- ADD NEW ADDRESS BUTTON --}}
    <div style="margin-bottom: 2rem; text-align: right;">
        <button class="btn btn-primary" onclick="toggleAddressForm()">
            <i class="fas fa-plus"></i>
            <span>Add New Address</span>
        </button>
    </div>

    {{-- ADD ADDRESS FORM (HIDDEN BY DEFAULT) --}}
    <div id="address-form" style="display: none; background: var(--white); border-radius: var(--radius-lg); padding: 2rem; border: 1px solid var(--gray-200); margin-bottom: 2rem;">
        <h3 style="color: var(--gray-900); margin-bottom: 1.5rem;">Add New Address</h3>
        <form id="new-address-form" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <div class="form-group">
                <label for="address_name">Address Name *</label>
                <input type="text" id="address_name" name="address_name" class="form-control" placeholder="e.g., Home, Office" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" placeholder="John Doe" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="street_address">Street Address *</label>
                <input type="text" id="street_address" name="street_address" class="form-control" placeholder="123 Main Street, Apartment 4B" required>
            </div>
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" class="form-control" placeholder="Nairobi" required>
            </div>
            <div class="form-group">
                <label for="state">State/County *</label>
                <input type="text" id="state" name="state" class="form-control" placeholder="Nairobi County" required>
            </div>
            <div class="form-group">
                <label for="postal_code">Postal Code *</label>
                <input type="text" id="postal_code" name="postal_code" class="form-control" placeholder="00100" required>
            </div>
            <div class="form-group">
                <label for="country">Country *</label>
                <select id="country" name="country" class="form-control" required>
                    <option value="">Select Country</option>
                    <option value="Kenya">Kenya</option>
                    <option value="Tanzania">Tanzania</option>
                    <option value="Uganda">Uganda</option>
                </select>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+254 7XX XXX XXX" required>
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="landmark">Landmark (Optional)</label>
                <input type="text" id="landmark" name="landmark" class="form-control" placeholder="Near ABC Mall">
            </div>
            <div style="grid-column: 1 / -1; display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    <span>Save Address</span>
                </button>
                <button type="button" class="btn btn-secondary" onclick="toggleAddressForm()">
                    <i class="fas fa-times"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>

    {{-- ADDRESSES GRID --}}
    <div id="addresses-container">
        {{-- PLACEHOLDER: This would be populated with actual addresses --}}
        <x-empty-state
            icon="fas fa-map-marker-alt"
            title="No Addresses Saved"
            description="Add your first shipping address to make checkout faster and easier."
            action="#"
            actionText="Add Your First Address"
            actionOnclick="toggleAddressForm()"
        />

        {{-- EXAMPLE ADDRESS CARD (would be generated dynamically) --}}
        {{-- 
        <div class="address-card" style="background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1rem; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <h4 style="margin: 0 0 0.25rem 0; color: var(--gray-900);">Home</h4>
                    <span class="badge badge-success">Default</span>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button class="btn btn-icon btn-secondary btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-icon btn-secondary btn-sm" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div style="color: var(--gray-700);">
                <div>John Doe</div>
                <div>123 Main Street, Apartment 4B</div>
                <div>Nairobi, Nairobi County 00100</div>
                <div>Kenya</div>
                <div style="margin-top: 0.5rem;">📞 +254 712 345 678</div>
            </div>
        </div>
        --}}
    </div>
@endsection

@push('scripts')
<script>
function toggleAddressForm() {
    const form = document.getElementById('address-form');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
    
    if (form.style.display === 'block') {
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

// Form submission would be handled here
document.getElementById('new-address-form').addEventListener('submit', function(e) {
    e.preventDefault();
    // Handle form submission
    alert('Address form submitted! (Backend integration needed)');
});
</script>
@endpush

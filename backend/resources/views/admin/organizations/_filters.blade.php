<div class="card shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters & Actions
            @if(request()->hasAny(['search', 'category_id', 'region', 'city', 'verified']))
                <span class="badge bg-primary ms-2">Active</span>
            @endif
        </h6>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.organizations.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Search
                        </label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Search organizations...">
                    </div>

                    <!-- Category -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-tag me-1"></i>Category
                        </label>
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Verification Status -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="bi bi-shield-check me-1"></i>Status
                        </label>
                        <select name="verified" class="form-select">
                            <option value="">All</option>
                            <option value="verified" {{ request('verified') == 'verified' ? 'selected' : '' }}>
                                Verified
                            </option>
                            <option value="pending" {{ request('verified') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="blocked" {{ request('verified') == 'blocked' ? 'selected' : '' }}>
                                Blocked
                            </option>
                        </select>
                    </div>

                    <!-- Region -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="bi bi-map me-1"></i>Region
                        </label>
                        <input type="text" 
                               name="region" 
                               class="form-control" 
                               value="{{ request('region') }}" 
                               placeholder="Region">
                    </div>

                    <!-- City -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="bi bi-pin-map me-1"></i>City
                        </label>
                        <input type="text" 
                               name="city" 
                               class="form-control" 
                               value="{{ request('city') }}" 
                               placeholder="City">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-3 d-flex gap-2 align-items-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'region', 'city', 'verified']))
                        <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Clear All
                        </a>
                    @endif
                    
                    <!-- Export Dropdown -->
                    <div class="dropdown ms-auto">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.organizations.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export to CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.organizations.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Export to PDF
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Bulk Actions Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle" type="button" id="bulkActions" data-bs-toggle="dropdown" disabled>
                            <i class="bi bi-lightning me-1"></i>Bulk Actions (<span id="selectedCount">0</span>)
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="#" onclick="bulkAction('verify')">
                                    <i class="bi bi-check-circle me-2 text-success"></i>Verify Selected
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="bulkAction('unverify')">
                                    <i class="bi bi-x-circle me-2 text-warning"></i>Unverify Selected
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" onclick="bulkAction('reject')">
                                    <i class="bi bi-ban me-2 text-danger"></i>Reject Selected
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Action Forms (Hidden) -->
<form id="bulkVerifyForm" action="{{ route('admin.organizations.bulk-verify') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="organization_ids" id="verifyIds">
    <input type="text" name="reason" id="verifyReason" placeholder="Reason (optional)">
</form>

<form id="bulkUnverifyForm" action="{{ route('admin.organizations.bulk-unverify') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="organization_ids" id="unverifyIds">
    <input type="text" name="reason" id="unverifyReason" placeholder="Reason (optional)">
</form>

<form id="bulkRejectForm" action="{{ route('admin.organizations.bulk-reject') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="organization_ids" id="rejectIds">
    <input type="text" name="reason" id="rejectReason" placeholder="Reason (required)">
</form>

@push('scripts')
<script>
// Track selected organizations
let selectedOrgs = [];

function updateBulkButton() {
    const count = selectedOrgs.length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActions').disabled = count === 0;
}

function toggleOrganization(checkbox, orgId) {
    if (checkbox.checked) {
        if (!selectedOrgs.includes(orgId)) {
            selectedOrgs.push(orgId);
        }
    } else {
        selectedOrgs = selectedOrgs.filter(id => id !== orgId);
    }
    updateBulkButton();
}

function toggleAllOrganizations(checkbox) {
    const checkboxes = document.querySelectorAll('.org-checkbox');
    selectedOrgs = [];
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
        if (checkbox.checked) {
            selectedOrgs.push(parseInt(cb.dataset.orgId));
        }
    });
    updateBulkButton();
}

function bulkAction(action) {
    if (selectedOrgs.length === 0) {
        alert('Please select at least one organization');
        return;
    }

    let message = '';
    let formId = '';
    let idsField = '';

    if (action === 'verify') {
        message = `Are you sure you want to verify ${selectedOrgs.length} organization(s)?`;
        formId = 'bulkVerifyForm';
        idsField = 'verifyIds';
    } else if (action === 'unverify') {
        message = `Are you sure you want to unverify ${selectedOrgs.length} organization(s)?`;
        formId = 'bulkUnverifyForm';
        idsField = 'unverifyIds';
    } else if (action === 'reject') {
        const reason = prompt(`Enter reason for rejecting ${selectedOrgs.length} organization(s):`);
        if (!reason) {
            alert('Reason is required for rejection');
            return;
        }
        document.getElementById('rejectReason').value = reason;
        formId = 'bulkRejectForm';
        idsField = 'rejectIds';
    }

    if (action !== 'reject' && !confirm(message)) {
        return;
    }

    document.getElementById(idsField).value = JSON.stringify(selectedOrgs);
    document.getElementById(formId).submit();
}
</script>
@endpush

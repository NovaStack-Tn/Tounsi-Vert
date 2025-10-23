<div class="card shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="bi bi-funnel me-2"></i>Filters
            @if(request()->hasAny(['search', 'category_id', 'type', 'region', 'city', 'start_date', 'end_date', 'status', 'published']))
                <span class="badge bg-primary ms-2">Active</span>
            @endif
        </h6>
        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="bi bi-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.events.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Search -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-search me-1"></i>Search
                        </label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               value="{{ request('search') }}" 
                               placeholder="Search events...">
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

                    <!-- Type -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="bi bi-geo me-1"></i>Type
                        </label>
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="in-person" {{ request('type') == 'in-person' ? 'selected' : '' }}>In-Person</option>
                            <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-check me-1"></i>Status
                        </label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>Past</option>
                        </select>
                    </div>

                    <!-- Published -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">
                            <i class="bi bi-eye me-1"></i>Published
                        </label>
                        <select name="published" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ request('published') == '1' ? 'selected' : '' }}>Published</option>
                            <option value="0" {{ request('published') == '0' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>

                    <!-- Region -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-map me-1"></i>Region
                        </label>
                        <input type="text" 
                               name="region" 
                               class="form-control" 
                               value="{{ request('region') }}" 
                               placeholder="e.g., Tunis">
                    </div>

                    <!-- City -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-pin-map me-1"></i>City
                        </label>
                        <input type="text" 
                               name="city" 
                               class="form-control" 
                               value="{{ request('city') }}" 
                               placeholder="e.g., Ariana">
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range me-1"></i>Start Date
                        </label>
                        <input type="date" 
                               name="start_date" 
                               class="form-control" 
                               value="{{ request('start_date') }}">
                    </div>

                    <!-- End Date -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range me-1"></i>End Date
                        </label>
                        <input type="date" 
                               name="end_date" 
                               class="form-control" 
                               value="{{ request('end_date') }}">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>Apply Filters
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'type', 'region', 'city', 'start_date', 'end_date', 'status', 'published']))
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
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
                                <a class="dropdown-item" href="{{ route('admin.events.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export to CSV
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.events.export', array_merge(request()->all(), ['format' => 'pdf'])) }}">
                                    <i class="bi bi-file-earmark-pdf me-2"></i>Export to PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

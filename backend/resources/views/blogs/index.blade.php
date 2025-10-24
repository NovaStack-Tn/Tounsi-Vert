@extends('layouts.public')

@section('title', 'Community Blogs - TounsiVert')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Main Content - Blog Feed -->
        <div class="col-lg-8">
            <!-- Create Blog Section (Auth Users Only) -->
            @auth
            <div class="card shadow-sm mb-4" style="border-radius: 15px; border: none;">
                <div class="card-body p-4">
                    <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control border-0 fs-5 fw-bold" 
                                   name="title" 
                                   placeholder="What's your environmental story?"
                                   style="background: transparent;"
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <textarea class="form-control border-0" 
                                      name="content"
                                      rows="3" 
                                      placeholder="Share your thoughts with the community..."
                                      style="background: #f8f9fa; border-radius: 12px; resize: none;"
                                      required></textarea>
                        </div>

                        <div id="mediaPreview" class="mb-3" style="display: none;"></div>
                        
                        <input type="hidden" name="ai_assisted" id="aiAssisted" value="0">
                        <div id="aiGeneratedImageContainer"></div>
                        
                        <!-- AI Assistance Section -->
                        <div class="mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-warning btn-sm" onclick="showWebAIImageUpload()">
                                    <i class="bi bi-magic me-1"></i>AI from Image
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="enhanceWebContent()">
                                    <i class="bi bi-stars me-1"></i>Enhance Writing
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="generateWebBanner()">
                                    <i class="bi bi-image me-1"></i>Generate Banner
                                </button>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <label class="btn btn-light btn-sm rounded-pill" style="cursor: pointer;" title="Upload Images (Max 5)">
                                    <i class="bi bi-images text-success"></i>
                                    <input type="file" name="images[]" id="regularImages" accept="image/*" multiple style="display: none;" onchange="previewImages(this)">
                                </label>
                                <label class="btn btn-light btn-sm rounded-pill" style="cursor: pointer;" title="Upload Video">
                                    <i class="bi bi-camera-video text-success"></i>
                                    <input type="file" name="video" accept="video/*" style="display: none;" onchange="previewVideo(this)">
                                </label>
                            </div>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-send-fill me-1"></i>Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endauth

            <!-- Filters & Sort -->
            <div class="card shadow-sm mb-4" style="border-radius: 15px; border: none;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('blogs.index') }}" id="filterForm" class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Search blogs..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Popular</option>
                                <option value="most_liked" {{ request('sort') == 'most_liked' ? 'selected' : '' }}>Most Liked</option>
                                <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>Most Viewed</option>
                                <option value="most_commented" {{ request('sort') == 'most_commented' ? 'selected' : '' }}>Most Commented</option>
                            </select>
                        </div>
                        @auth
                        <div class="col-md-2">
                            @if(request('my_blogs'))
                                <a href="{{ route('blogs.index', array_merge(request()->except('my_blogs'), ['search' => request('search'), 'sort' => request('sort')])) }}" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-grid me-1"></i>All Blogs
                                </a>
                            @else
                                <a href="{{ route('blogs.index', array_merge(request()->all(), ['my_blogs' => '1'])) }}" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="bi bi-person-circle me-1"></i>My Blogs
                                </a>
                            @endif
                        </div>
                        @endauth
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    @if(request('my_blogs'))
                    <div class="mt-3">
                        <div class="alert alert-info mb-0 py-2 d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-funnel-fill me-2"></i>Showing only your blogs</span>
                            <a href="{{ route('blogs.index', request()->except('my_blogs')) }}" class="btn btn-sm btn-outline-primary">
                                Clear Filter
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Blog Feed -->
            @forelse($blogs as $blog)
            <div class="card shadow-sm mb-3 blog-card" style="border-radius: 15px; border: none; transition: all 0.3s; cursor: pointer; overflow: hidden;" 
                 onclick="window.location='{{ route('blogs.show', $blog) }}'">
                <div class="card-body p-0">
                    <!-- Blog Header -->
                    <div class="p-4 pb-3">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 45px; height: 45px; background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $blog->user->full_name }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>{{ $blog->created_at->diffForHumans() }}
                                    @if($blog->ai_assisted)
                                        <span class="badge bg-warning text-dark ms-2"><i class="bi bi-stars"></i> AI Assisted</span>
                                    @endif
                                </small>
                            </div>
                            @auth
                                @if(auth()->id() === $blog->user_id)
                                <div class="dropdown" onclick="event.stopPropagation();">
                                    <button class="btn btn-link text-muted" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form action="{{ route('blogs.destroy', $blog) }}" method="POST" id="deleteBlogForm{{ $blog->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $blog->id }}, '{{ addslashes($blog->title) }}')">
                                                    <i class="bi bi-trash me-2"></i>Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                                @endif
                            @endauth
                        </div>

                        <!-- Blog Content -->
                        <div class="mt-3">
                            <h5 class="mb-2 fw-bold text-dark">{{ $blog->title }}</h5>
                            <p class="text-muted mb-3">{{ Str::limit($blog->content, 200) }}</p>
                        </div>
                    </div>

                    <!-- Media Display -->
                    @if($blog->has_images && $blog->images_paths && count($blog->images_paths) > 0)
                    <div class="blog-media mb-2">
                        @if(count($blog->images_paths) === 1)
                            <img src="{{ Storage::url($blog->images_paths[0]) }}" 
                                 alt="{{ $blog->title }}" 
                                 class="img-fluid w-100" 
                                 style="max-height: 500px; object-fit: cover; border-radius: 0;">
                        @else
                            <!-- Mini Carousel for multiple images -->
                            <div id="blogCarousel{{ $blog->id }}" class="carousel slide" data-bs-ride="carousel" onclick="event.stopPropagation();">
                                <div class="carousel-indicators" style="margin-bottom: 10px;">
                                    @foreach($blog->images_paths as $index => $imagePath)
                                    <button type="button" 
                                            data-bs-target="#blogCarousel{{ $blog->id }}" 
                                            data-bs-slide-to="{{ $index }}" 
                                            class="{{ $index === 0 ? 'active' : '' }}" 
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                            aria-label="Slide {{ $index + 1 }}"
                                            style="width: 8px; height: 8px; border-radius: 50%;"></button>
                                    @endforeach
                                </div>
                                <div class="carousel-inner">
                                    @foreach($blog->images_paths as $index => $imagePath)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::url($imagePath) }}" 
                                             class="d-block w-100" 
                                             alt="{{ $blog->title }}" 
                                             style="height: 450px; object-fit: cover;">
                                    </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#blogCarousel{{ $blog->id }}" data-bs-slide="prev" style="width: 40px;">
                                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 15px;"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#blogCarousel{{ $blog->id }}" data-bs-slide="next" style="width: 40px;">
                                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 15px;"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                                <!-- Image counter badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-dark bg-opacity-75">
                                        <i class="bi bi-images me-1"></i>{{ count($blog->images_paths) }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif

                    @if($blog->has_video && $blog->video_path)
                    <div class="blog-media mb-2" onclick="event.stopPropagation();">
                        <video class="w-100" controls style="max-height: 500px; background: #000;">
                            <source src="{{ Storage::url($blog->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    @endif

                    <!-- Blog Actions -->
                    <div class="p-4 pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-4">
                                <!-- Like -->
                                @auth
                                <form action="{{ route('blogs.like', $blog) }}" method="POST" class="d-inline" onclick="event.stopPropagation();">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-decoration-none p-0 {{ $blog->isLikedBy() ? 'text-danger' : 'text-muted' }}">
                                        <i class="bi bi-heart{{ $blog->isLikedBy() ? '-fill' : '' }} me-1"></i>
                                        <span>{{ $blog->likes_count }}</span>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted">
                                    <i class="bi bi-heart me-1"></i>{{ $blog->likes_count }}
                                </span>
                                @endauth

                                <!-- Comments -->
                                <span class="text-muted">
                                    <i class="bi bi-chat me-1"></i>{{ $blog->comments_count }}
                                </span>

                                <!-- Views -->
                                <span class="text-muted">
                                    <i class="bi bi-eye me-1"></i>{{ $blog->views_count }}
                                </span>
                            </div>

                            <!-- Share -->
                            <button class="btn btn-link text-muted text-decoration-none p-0" onclick="event.stopPropagation();">
                                <i class="bi bi-share"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body text-center py-5">
                    <i class="bi {{ request('my_blogs') ? 'bi-person-x' : 'bi-newspaper' }} text-muted" style="font-size: 4rem;"></i>
                    @if(request('my_blogs'))
                        <h4 class="mt-3">You haven't created any blogs yet</h4>
                        <p class="text-muted">Share your environmental story with the community!</p>
                        @auth
                        <a href="#" class="btn btn-success rounded-pill" onclick="document.querySelector('input[name=title]').focus(); return false;">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Blog
                        </a>
                        @endauth
                    @else
                        <h4 class="mt-3">No blogs found</h4>
                        <p class="text-muted">Try adjusting your search filters</p>
                        <a href="{{ route('blogs.index') }}" class="btn btn-outline-primary rounded-pill">
                            <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                        </a>
                    @endif
                </div>
            </div>
            @endforelse

            <!-- Pagination -->
            <div class="mt-4">
                {{ $blogs->links() }}
            </div>
        </div>

        <!-- Sidebar - Top Organizations & Ads -->
        <div class="col-lg-4">
            <!-- Top Organizations -->
            <div class="card shadow-sm mb-4" style="border-radius: 15px; border: none; position: sticky; top: 20px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Organizations</h5>
                    <small class="text-muted">Supporting our community</small>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($topOrganizations as $org)
                    <a href="{{ route('organizations.show', $org) }}" class="text-decoration-none">
                        <div class="d-flex align-items-center mb-3 p-3 rounded hover-bg" style="transition: all 0.3s; background: #f8f9fa;">
                            <div class="me-3">
                                @if($org->logo_path)
                                <img src="{{ Storage::url($org->logo_path) }}" 
                                     alt="{{ $org->name }}" 
                                     class="rounded-circle"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-building"></i>
                                </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark">{{ Str::limit($org->name, 25) }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-star-fill text-warning"></i> {{ $org->owner->score }} pts
                                    @if($org->is_verified)
                                    <i class="bi bi-patch-check-fill text-success ms-1"></i>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </a>
                    @empty
                    <p class="text-muted text-center py-3">No organizations yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Community Stats -->
            <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-success me-2"></i>Community Stats</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Blogs</span>
                        <span class="fw-bold">{{ $blogs->total() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Active Writers</span>
                        <span class="fw-bold">{{ $blogs->pluck('user_id')->unique()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Blog
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-2">Are you sure you want to delete this blog?</p>
                <p class="fw-bold text-dark mb-3" id="blogTitleToDelete"></p>
                <div class="alert alert-warning d-flex align-items-center" style="border-radius: 10px;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <small>This action cannot be undone. All comments and likes will be permanently deleted.</small>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- AI Loading Overlay -->
<div id="aiLoadingOverlay" class="ai-loading-overlay" style="display: none;">
    <div class="ai-loading-content">
        <div class="spinner-container">
            <div class="ai-spinner"></div>
        </div>
        <h4 class="mt-4 mb-2">🤖 AI is working its magic...</h4>
        <p id="aiLoadingText" class="text-muted">Processing your request...</p>
        <div class="progress mt-3" style="height: 4px; width: 300px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" 
                 role="progressbar" style="width: 100%"></div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container-custom" id="toastContainer"></div>

<!-- AI Image Upload Modal -->
<div class="modal fade" id="webAiImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-warning bg-opacity-10">
                <h5 class="modal-title">
                    <i class="bi bi-magic me-2"></i>Generate Blog from Image
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted">Upload an image and AI will create a blog post about it!</p>
                <div class="mb-3">
                    <label class="btn btn-outline-warning w-100 py-3" style="cursor: pointer; border-style: dashed;">
                        <i class="bi bi-cloud-upload fs-1 d-block mb-2"></i>
                        <span class="d-block">Click to Upload Image</span>
                        <small class="text-muted">AI will analyze and create content</small>
                        <input type="file" id="webAiImageInput" accept="image/*" class="d-none" onchange="generateWebFromImage(this)">
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .blog-card {
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid #e9ecef;
    }
    
    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    
    .blog-avatar {
        transition: transform 0.3s;
    }
    
    .blog-card:hover .blog-avatar {
        transform: scale(1.1);
    }
    
    .blog-media {
        position: relative;
        overflow: hidden;
    }
    
    .blog-media img, 
    .blog-media video {
        transition: transform 0.3s;
    }
    
    .blog-card:hover .blog-media img,
    .blog-card:hover .blog-media video {
        transform: scale(1.02);
    }

    /* Carousel styling */
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }
    
    .carousel-indicators [data-bs-target] {
        background-color: rgba(255,255,255,0.6);
        border: 1px solid rgba(0,0,0,0.2);
    }
    
    .carousel-indicators .active {
        background-color: #fff;
    }

    /* AI Loading Overlay */
    .ai-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease;
    }

    .ai-loading-content {
        text-align: center;
        color: white;
    }

    .ai-spinner {
        width: 80px;
        height: 80px;
        border: 8px solid rgba(255, 255, 255, 0.1);
        border-top-color: #ffc107;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Toast Notifications */
    .toast-container-custom {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
    }

    .toast-custom {
        min-width: 350px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        margin-bottom: 15px;
        animation: slideInRight 0.3s ease;
        overflow: hidden;
    }

    .toast-custom.success { border-left: 4px solid #52b788; }
    .toast-custom.error { border-left: 4px solid #dc3545; }
    .toast-custom.warning { border-left: 4px solid #ffc107; }
    .toast-custom.info { border-left: 4px solid #0dcaf0; }

    .toast-header-custom {
        padding: 12px 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .toast-body-custom {
        padding: 15px;
    }

    .toast-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }

    .toast-icon.success { background: #d1f4e0; color: #52b788; }
    .toast-icon.error { background: #f8d7da; color: #dc3545; }
    .toast-icon.warning { background: #fff3cd; color: #ffc107; }
    .toast-icon.info { background: #cff4fc; color: #0dcaf0; }

    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
    
    .toast-closing {
        animation: slideOutRight 0.3s ease;
    }
</style>

<script>
// Toast Notification System
function showToast(message, type = 'info', duration = 5000) {
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Info'
    };

    const toastHtml = `
        <div class="toast-custom ${type}">
            <div class="toast-header-custom">
                <div class="d-flex align-items-center">
                    <div class="toast-icon ${type}">${icons[type]}</div>
                    <strong>${titles[type]}</strong>
                </div>
                <button type="button" class="btn-close btn-sm" onclick="this.closest('.toast-custom').remove()"></button>
            </div>
            <div class="toast-body-custom">${message}</div>
        </div>`;

    const container = document.getElementById('toastContainer');
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHtml;
    container.appendChild(toastElement.firstElementChild);

    setTimeout(() => {
        const toast = container.lastElementChild;
        if (toast) {
            toast.classList.add('toast-closing');
            setTimeout(() => toast.remove(), 300);
        }
    }, duration);
}

function previewImages(input) {
    const preview = document.getElementById('mediaPreview');
    
    if (input.files && input.files.length > 0) {
        if (input.files.length > 5) {
            showToast('Maximum 5 images allowed!', 'warning');
            input.value = '';
            return;
        }
        
        preview.innerHTML = '<div class="row g-2"></div>';
        preview.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-4';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                onclick="removeImagePreview(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                        <span class="badge bg-success position-absolute bottom-0 start-0 m-1">${index + 1}</span>
                    </div>`;
                preview.querySelector('.row').appendChild(col);
            }
            reader.readAsDataURL(file);
        });
    }
}

function previewVideo(input) {
    const preview = document.getElementById('mediaPreview');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const videoPreview = document.createElement('div');
            videoPreview.id = 'videoPreviewContainer';
            videoPreview.innerHTML = `
                <div class="position-relative mt-3">
                    <video src="${e.target.result}" class="w-100 rounded" controls style="max-height: 300px; background: #000;"></video>
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                            onclick="clearVideo()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>`;
            
            // Remove existing video preview if any
            const existingVideo = document.getElementById('videoPreviewContainer');
            if (existingVideo) existingVideo.remove();
            
            preview.appendChild(videoPreview);
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    }
}

function removeImagePreview(index) {
    const input = document.querySelector('input[name="images[]"]');
    if (input) {
        // Create a new FileList without the removed file
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        input.files = dt.files;
        
        // Refresh preview
        previewImages(input);
    }
}

function clearVideo() {
    const input = document.querySelector('input[name="video"]');
    if (input) input.value = '';
    
    const videoContainer = document.getElementById('videoPreviewContainer');
    if (videoContainer) videoContainer.remove();
}

function requestAIHelp() {
    const title = document.querySelector('input[name="title"]').value;
    const content = document.querySelector('textarea[name="content"]').value;
    
    if (!title && !content) {
        showToast('Please write something first!', 'warning');
        return;
    }
    
    // Mark as AI assisted
    document.getElementById('aiAssisted').value = '1';
    
    // Placeholder for AI integration
    showToast('AI assistance will help enhance your blog post with better grammar and structure!', 'info', 5000);
}

// Delete Confirmation
let deleteFormId = null;

function confirmDelete(blogId, blogTitle) {
    deleteFormId = blogId;
    document.getElementById('blogTitleToDelete').textContent = blogTitle;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteFormId) {
        document.getElementById('deleteBlogForm' + deleteFormId).submit();
    }
});

// Custom Toast Notification System
function showToast(message, type = 'success', duration = 3000) {
    const iconMap = {
        success: 'bi-check-circle-fill',
        danger: 'bi-exclamation-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const bgMap = {
        success: 'bg-success',
        danger: 'bg-danger',
        warning: 'bg-warning',
        info: 'bg-success'
    };
    
    const textColorMap = {
        success: 'text-white',
        danger: 'text-white',
        warning: 'text-dark',
        info: 'text-white'
    };
    
    const toastHtml = `
        <div class="toast custom-toast show" role="alert">
            <div class="toast-header ${bgMap[type]} ${textColorMap[type]} border-0">
                <i class="bi ${iconMap[type]} me-2"></i>
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close ${type === 'warning' ? '' : 'btn-close-white'}" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    const toastContainer = document.createElement('div');
    toastContainer.innerHTML = toastHtml;
    document.body.appendChild(toastContainer);
    
    const toastElement = toastContainer.querySelector('.toast');
    
    // Auto dismiss
    setTimeout(() => {
        toastElement.classList.add('toast-closing');
        setTimeout(() => {
            toastContainer.remove();
        }, 300);
    }, duration);
    
    // Manual dismiss
    toastElement.querySelector('.btn-close').addEventListener('click', () => {
        toastElement.classList.add('toast-closing');
        setTimeout(() => {
            toastContainer.remove();
        }, 300);
    });
}

// Show Laravel session messages as toasts
@if(session('success'))
    showToast("{{ session('success') }}", 'success');
@endif

@if(session('error'))
    showToast("{{ session('error') }}", 'danger');
@endif

@if($errors->any())
    @foreach($errors->all() as $error)
        showToast("{{ $error }}", 'danger');
    @endforeach
@endif
</script>
@endpush
@endsection

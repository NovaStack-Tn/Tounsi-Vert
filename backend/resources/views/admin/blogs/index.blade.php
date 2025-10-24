@extends('layouts.admin')

@section('title', 'Blog Management - Admin')

@section('page-title', 'Blog Management')
@section('page-subtitle', 'Manage all user and organizer blogs')

@section('content')
<div class="container-fluid p-4">
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-start text-white">
                        <div>
                            <p class="mb-2 opacity-75">Total Blogs</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_blogs']) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-journal-text" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="d-flex justify-content-between align-items-start text-white">
                        <div>
                            <p class="mb-2 opacity-75">Total Comments</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_comments']) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-chat-dots" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="d-flex justify-content-between align-items-start text-white">
                        <div>
                            <p class="mb-2 opacity-75">Total Likes</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_likes']) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-heart-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-4" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="d-flex justify-content-between align-items-start text-white">
                        <div>
                            <p class="mb-2 opacity-75">Total Views</p>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_views']) }}</h2>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-eye-fill" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background: rgba(102, 126, 234, 0.1);">
                                <i class="bi bi-magic text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ number_format($stats['ai_assisted_blogs']) }}</h5>
                            <small class="text-muted">AI-Assisted Blogs</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background: rgba(82, 183, 136, 0.1);">
                                <i class="bi bi-images text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ number_format($stats['blogs_with_images']) }}</h5>
                            <small class="text-muted">With Images</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 50px; height: 50px; background: rgba(245, 87, 108, 0.1);">
                                <i class="bi bi-camera-video text-danger" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ number_format($stats['blogs_with_videos']) }}</h5>
                            <small class="text-muted">With Videos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.blogs.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-start-0" 
                                   placeholder="Search blogs, authors..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Sort By</label>
                        <select name="sort" class="form-select" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="most_liked" {{ request('sort') == 'most_liked' ? 'selected' : '' }}>Most Liked</option>
                            <option value="most_commented" {{ request('sort') == 'most_commented' ? 'selected' : '' }}>Most Commented</option>
                            <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>Most Viewed</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Media Type</label>
                        <select name="media_type" class="form-select" onchange="this.form.submit()">
                            <option value="">All Media</option>
                            <option value="images" {{ request('media_type') == 'images' ? 'selected' : '' }}>With Images</option>
                            <option value="video" {{ request('media_type') == 'video' ? 'selected' : '' }}>With Video</option>
                            <option value="none" {{ request('media_type') == 'none' ? 'selected' : '' }}>No Media</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">AI Assisted</label>
                        <select name="ai_assisted" class="form-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <option value="yes" {{ request('ai_assisted') == 'yes' ? 'selected' : '' }}>AI-Assisted</option>
                            <option value="no" {{ request('ai_assisted') == 'no' ? 'selected' : '' }}>Manual</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1">Actions</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Blogs Table -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2 text-primary"></i>All Blogs
                </h5>
                <span class="badge bg-primary rounded-pill">{{ $blogs->total() }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0">Blog</th>
                            <th class="px-4 py-3 border-0">Author</th>
                            <th class="px-4 py-3 border-0 text-center">Stats</th>
                            <th class="px-4 py-3 border-0 text-center">Media</th>
                            <th class="px-4 py-3 border-0">Date</th>
                            <th class="px-4 py-3 border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                        <tr class="blog-row" data-blog-id="{{ $blog->id }}" style="transition: all 0.3s;">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-start">
                                    @if($blog->has_images && $blog->images_paths && count($blog->images_paths) > 0)
                                    <img src="{{ Storage::url($blog->images_paths[0]) }}" 
                                         alt="{{ $blog->title }}" 
                                         class="rounded me-3"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                    @elseif($blog->has_video)
                                    <div class="rounded me-3 d-flex align-items-center justify-content-center bg-dark text-white"
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-play-circle" style="font-size: 1.5rem;"></i>
                                    </div>
                                    @else
                                    <div class="rounded me-3 d-flex align-items-center justify-content-center bg-light"
                                         style="width: 60px; height: 60px;">
                                        <i class="bi bi-file-text text-muted" style="font-size: 1.5rem;"></i>
                                    </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('admin.blogs.show', $blog) }}" class="text-dark text-decoration-none">
                                                {{ Str::limit($blog->title, 50) }}
                                            </a>
                                        </h6>
                                        <p class="text-muted small mb-1">{{ Str::limit($blog->content, 80) }}</p>
                                        <div class="d-flex gap-1">
                                            @if($blog->ai_assisted)
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                                <i class="bi bi-magic"></i> AI
                                            </span>
                                            @endif
                                            @if($blog->is_published)
                                            <span class="badge bg-success" style="font-size: 0.7rem;">Published</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                         style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        {{ strtoupper(substr($blog->user->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $blog->user->full_name }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <span class="badge bg-secondary">{{ ucfirst($blog->user->role) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-3 small">
                                    <div title="Likes">
                                        <i class="bi bi-heart-fill text-danger"></i>
                                        <strong>{{ $blog->likes_count }}</strong>
                                    </div>
                                    <div title="Comments">
                                        <i class="bi bi-chat-fill text-primary"></i>
                                        <strong>{{ $blog->comments_count }}</strong>
                                    </div>
                                    <div title="Views">
                                        <i class="bi bi-eye-fill text-info"></i>
                                        <strong>{{ $blog->views_count }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($blog->has_images)
                                    <span class="badge bg-success" title="Has Images">
                                        <i class="bi bi-images"></i> {{ count($blog->images_paths) }}
                                    </span>
                                    @endif
                                    @if($blog->has_video)
                                    <span class="badge bg-danger" title="Has Video">
                                        <i class="bi bi-camera-video"></i>
                                    </span>
                                    @endif
                                    @if(!$blog->has_images && !$blog->has_video)
                                    <span class="text-muted small">No media</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="small">
                                    <div>{{ $blog->created_at->format('M d, Y') }}</div>
                                    <div class="text-muted">{{ $blog->created_at->format('h:i A') }}</div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="btn-group">
                                    <a href="{{ route('admin.blogs.show', $blog) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete({{ $blog->id }}, '{{ addslashes($blog->title) }}')"
                                            title="Delete Blog">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <p class="text-muted mt-3">No blogs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($blogs->hasPages())
        <div class="card-footer bg-white border-0 p-4">
            {{ $blogs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Blog
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">Are you sure you want to delete this blog?</p>
                <div class="alert alert-warning mb-0">
                    <strong id="blogTitle"></strong>
                    <p class="small mb-0 mt-2">This action cannot be undone. All comments and likes will also be deleted.</p>
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

@endsection

@push('scripts')
<style>
    .stat-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
    }
    .blog-row:hover {
        background: rgba(82, 183, 136, 0.05);
        transform: scale(1.01);
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .blog-row {
        animation: fadeIn 0.5s ease;
    }
</style>

<script>
let blogIdToDelete = null;

function confirmDelete(blogId, blogTitle) {
    blogIdToDelete = blogId;
    document.getElementById('blogTitle').textContent = blogTitle;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!blogIdToDelete) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

    try {
        const response = await fetch(`/admin/blogs/${blogIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            // Close modal
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            
            // Show success message
            showAlert('success', data.message);
            
            // Remove row with animation
            const row = document.querySelector(`tr[data-blog-id="${blogIdToDelete}"]`);
            if (row) {
                row.style.animation = 'fadeOut 0.5s ease';
                setTimeout(() => {
                    row.remove();
                    // Reload if no more blogs
                    if (document.querySelectorAll('.blog-row').length === 0) {
                        location.reload();
                    }
                }, 500);
            }
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Error deleting blog: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
    }
});

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999; min-width: 300px; animation: slideDown 0.5s ease;" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    setTimeout(() => {
        document.querySelector('.alert').remove();
    }, 3000);
}
</script>

<style>
    @keyframes fadeOut {
        to {
            opacity: 0;
            transform: translateX(-20px);
        }
    }
    @keyframes slideDown {
        from {
            transform: translate(-50%, -100%);
            opacity: 0;
        }
        to {
            transform: translate(-50%, 0);
            opacity: 1;
        }
    }
</style>
@endpush

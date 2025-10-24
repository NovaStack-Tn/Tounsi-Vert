@extends('layouts.admin')

@section('title', 'Blog Details - Admin')

@section('page-title', 'Blog Details')
@section('page-subtitle', 'View complete blog information and comments')

@section('content')
<div class="container-fluid p-4">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Blogs
        </a>
    </div>

    <div class="row g-4">
        <!-- Main Blog Content -->
        <div class="col-lg-8">
            <!-- Blog Card -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; animation: fadeIn 0.5s;">
                <div class="card-body p-4">
                    <!-- Header with Actions -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h2 class="mb-2">{{ $blog->title }}</h2>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($blog->ai_assisted)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-magic me-1"></i>AI-Assisted
                                </span>
                                @endif
                                @if($blog->is_published)
                                <span class="badge bg-success">Published</span>
                                @else
                                <span class="badge bg-secondary">Draft</span>
                                @endif
                                <span class="badge bg-info">ID: {{ $blog->id }}</span>
                            </div>
                        </div>
                        <button class="btn btn-danger" onclick="confirmDeleteBlog({{ $blog->id }}, '{{ addslashes($blog->title) }}')">
                            <i class="bi bi-trash me-1"></i>Delete Blog
                        </button>
                    </div>

                    <!-- Media -->
                    @if($blog->has_images && $blog->images_paths && count($blog->images_paths) > 0)
                    <div class="mb-4">
                        @if(count($blog->images_paths) === 1)
                            <img src="{{ Storage::url($blog->images_paths[0]) }}" 
                                 alt="{{ $blog->title }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 500px; width: 100%; object-fit: cover;">
                        @else
                            <div id="blogImagesCarousel" class="carousel slide shadow-sm rounded" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    @foreach($blog->images_paths as $index => $imagePath)
                                    <button type="button" data-bs-target="#blogImagesCarousel" data-bs-slide-to="{{ $index }}" 
                                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                                    @endforeach
                                </div>
                                <div class="carousel-inner">
                                    @foreach($blog->images_paths as $index => $imagePath)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::url($imagePath) }}" 
                                             class="d-block w-100" 
                                             style="height: 500px; object-fit: cover;">
                                    </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#blogImagesCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#blogImagesCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        @endif
                    </div>
                    @endif

                    @if($blog->has_video && $blog->video_path)
                    <div class="mb-4">
                        <video class="w-100 rounded shadow-sm" controls style="max-height: 500px; background: #000;">
                            <source src="{{ Storage::url($blog->video_path) }}" type="video/mp4">
                        </video>
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="blog-content mb-4" style="font-size: 1.1rem; line-height: 1.8;">
                        {!! nl2br(e($blog->content)) !!}
                    </div>

                    <!-- Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card bg-danger bg-opacity-10 border-0">
                                <div class="card-body text-center p-3">
                                    <i class="bi bi-heart-fill text-danger fs-2"></i>
                                    <h4 class="mb-0 mt-2">{{ $blog->likes_count }}</h4>
                                    <small class="text-muted">Likes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary bg-opacity-10 border-0">
                                <div class="card-body text-center p-3">
                                    <i class="bi bi-chat-dots-fill text-primary fs-2"></i>
                                    <h4 class="mb-0 mt-2">{{ $blog->comments_count }}</h4>
                                    <small class="text-muted">Comments</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info bg-opacity-10 border-0">
                                <div class="card-body text-center p-3">
                                    <i class="bi bi-eye-fill text-info fs-2"></i>
                                    <h4 class="mb-0 mt-2">{{ $blog->views_count }}</h4>
                                    <small class="text-muted">Views</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="border-top pt-3">
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <strong>Created:</strong> {{ $blog->created_at->format('F d, Y \a\t h:i A') }}
                            </div>
                            <div class="col-md-6">
                                <strong>Updated:</strong> {{ $blog->updated_at->format('F d, Y \a\t h:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots me-2 text-primary"></i>Comments ({{ $comments->total() }})
                        </h5>
                    </div>
                    
                    <!-- Comment Search -->
                    <form method="GET" action="{{ route('admin.blogs.show', $blog) }}">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   name="comment_search" 
                                   class="form-control" 
                                   placeholder="Search comments, user name, user ID..." 
                                   value="{{ request('comment_search') }}">
                            <button type="submit" class="btn btn-primary">Search</button>
                            @if(request('comment_search'))
                            <a href="{{ route('admin.blogs.show', $blog) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
                <div class="card-body p-4">
                    @forelse($comments as $comment)
                    <div class="comment-item border-bottom pb-3 mb-3" data-comment-id="{{ $comment->id }}" style="animation: slideIn 0.3s;">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px;">
                                    {{ strtoupper(substr($comment->user->first_name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>{{ $comment->user->full_name }}</strong>
                                        <span class="badge bg-secondary ms-2">{{ ucfirst($comment->user->role) }}</span>
                                        <span class="badge bg-info ms-1">ID: {{ $comment->user->id }}</span>
                                        <div class="small text-muted">
                                            <i class="bi bi-envelope me-1"></i>{{ $comment->user->email }}
                                        </div>
                                        <div class="small text-muted">
                                            {{ $comment->created_at->diffForHumans() }} • {{ $comment->created_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDeleteComment({{ $comment->id }}, '{{ addslashes($comment->user->full_name) }}')"
                                            title="Delete Comment">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                                <p class="mb-0">{{ $comment->content }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-chat display-1 text-muted"></i>
                        <p class="text-muted mt-3">No comments yet</p>
                    </div>
                    @endforelse

                    @if($comments->hasPages())
                    <div class="mt-4">
                        {{ $comments->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Author Info -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2 text-primary"></i>Author Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ strtoupper(substr($blog->user->first_name, 0, 1)) }}
                        </div>
                        <h5 class="mb-1">{{ $blog->user->full_name }}</h5>
                        <span class="badge bg-primary">{{ ucfirst($blog->user->role) }}</span>
                    </div>

                    <div class="mb-2">
                        <strong class="text-muted small">User ID:</strong>
                        <div>{{ $blog->user->id }}</div>
                    </div>
                    <div class="mb-2">
                        <strong class="text-muted small">Email:</strong>
                        <div>{{ $blog->user->email }}</div>
                    </div>
                    <div class="mb-2">
                        <strong class="text-muted small">Phone:</strong>
                        <div>{{ $blog->user->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-2">
                        <strong class="text-muted small">Member Since:</strong>
                        <div>{{ $blog->user->created_at->format('F Y') }}</div>
                    </div>
                </div>
            </div>

            <!-- Organization Info (if organizer) -->
            @if($organization)
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-building me-2 text-success"></i>Organization
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($organization->logo)
                    <div class="text-center mb-3">
                        <img src="{{ Storage::url($organization->logo) }}" 
                             alt="{{ $organization->name }}"
                             class="rounded"
                             style="max-height: 100px;">
                    </div>
                    @endif
                    
                    <h5 class="mb-3">{{ $organization->name }}</h5>
                    
                    <div class="mb-2">
                        <strong class="text-muted small">Type:</strong>
                        <div>{{ $organization->type }}</div>
                    </div>
                    <div class="mb-2">
                        <strong class="text-muted small">Location:</strong>
                        <div>{{ $organization->address }}</div>
                    </div>
                    @if($organization->website)
                    <div class="mb-2">
                        <strong class="text-muted small">Website:</strong>
                        <div>
                            <a href="{{ $organization->website }}" target="_blank" class="text-decoration-none">
                                {{ $organization->website }}
                            </a>
                        </div>
                    </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('admin.organizations.show', $organization) }}" class="btn btn-outline-success btn-sm w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>View Organization
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning-charge me-2 text-warning"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('blogs.show', $blog) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View on Website
                        </a>
                        <button class="btn btn-outline-danger" onclick="confirmDeleteBlog({{ $blog->id }}, '{{ addslashes($blog->title) }}')">
                            <i class="bi bi-trash me-1"></i>Delete Blog
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Blog Modal -->
<div class="modal fade" id="deleteBlogModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Blog
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure you want to delete this blog?</p>
                <div class="alert alert-warning">
                    <strong id="blogTitle"></strong>
                    <p class="small mb-0 mt-2">This will delete all comments, likes, and media. This action cannot be undone.</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger rounded-pill" id="confirmDeleteBlogBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Delete Comment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>Are you sure you want to delete this comment by <strong id="commentAuthor"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger rounded-pill" id="confirmDeleteCommentBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes fadeOut {
        to { opacity: 0; transform: translateX(20px); }
    }
    .comment-item:hover {
        background: rgba(82, 183, 136, 0.05);
        border-radius: 10px;
        padding: 10px !important;
        transition: all 0.3s;
    }
</style>

<script>
let blogIdToDelete = null;
let commentIdToDelete = null;

// Delete Blog
function confirmDeleteBlog(blogId, blogTitle) {
    blogIdToDelete = blogId;
    document.getElementById('blogTitle').textContent = blogTitle;
    new bootstrap.Modal(document.getElementById('deleteBlogModal')).show();
}

document.getElementById('confirmDeleteBlogBtn').addEventListener('click', async function() {
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
            showAlert('success', data.message);
            setTimeout(() => {
                window.location.href = '{{ route("admin.blogs.index") }}';
            }, 1500);
        } else {
            showAlert('danger', data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
        }
    } catch (error) {
        showAlert('danger', 'Error: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
    }
});

// Delete Comment
function confirmDeleteComment(commentId, authorName) {
    commentIdToDelete = commentId;
    document.getElementById('commentAuthor').textContent = authorName;
    new bootstrap.Modal(document.getElementById('deleteCommentModal')).show();
}

document.getElementById('confirmDeleteCommentBtn').addEventListener('click', async function() {
    if (!commentIdToDelete) return;

    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

    try {
        const response = await fetch(`/admin/blog-comments/${commentIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });

        const data = await response.json();

        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteCommentModal')).hide();
            showAlert('success', data.message);
            
            const commentItem = document.querySelector(`[data-comment-id="${commentIdToDelete}"]`);
            if (commentItem) {
                commentItem.style.animation = 'fadeOut 0.5s ease';
                setTimeout(() => {
                    commentItem.remove();
                    if (document.querySelectorAll('.comment-item').length === 0) {
                        location.reload();
                    }
                }, 500);
            }
        } else {
            showAlert('danger', data.message);
        }
    } catch (error) {
        showAlert('danger', 'Error: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-trash me-1"></i>Delete';
    }
});

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
             style="z-index: 9999; min-width: 300px; animation: slideDown 0.5s ease;">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) alert.remove();
    }, 3000);
}
</script>

<style>
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

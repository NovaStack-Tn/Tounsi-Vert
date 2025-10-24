@extends('layouts.public')

@section('title', $blog->title . ' - TounsiVert Blogs')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Back Button -->
            <a href="{{ route('blogs.index') }}" class="btn btn-link text-muted mb-3">
                <i class="bi bi-arrow-left me-2"></i>Back to Blogs
            </a>

            <!-- Blog Post -->
            <article class="card shadow-sm mb-4" style="border-radius: 15px; border: none;">
                <div class="card-body p-5">
                    <!-- Author Info -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="user-avatar text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 60px; height: 60px; background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $blog->user->full_name }}</h5>
                            <small class="text-muted">
                                Posted {{ $blog->created_at->diffForHumans() }}
                                @if($blog->ai_assisted)
                                    <span class="badge bg-warning text-dark ms-2"><i class="bi bi-stars"></i> AI Assisted</span>
                                @endif
                            </small>
                        </div>
                    </div>

                    <!-- Title -->
                    <h1 class="mb-4 fw-bold" style="font-size: 2.5rem; line-height: 1.2;">{{ $blog->title }}</h1>

                    <!-- Media Section -->
                    @if($blog->has_images && $blog->images_paths && count($blog->images_paths) > 0)
                    <div class="mb-4">
                        @if(count($blog->images_paths) === 1)
                            <!-- Single Image -->
                            <img src="{{ Storage::url($blog->images_paths[0]) }}" 
                                 alt="{{ $blog->title }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="width: 100%; max-height: 600px; object-fit: cover;">
                        @else
                            <!-- Image Carousel -->
                            <div id="blogImagesCarousel" class="carousel slide shadow-sm rounded overflow-hidden" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    @foreach($blog->images_paths as $index => $imagePath)
                                    <button type="button" data-bs-target="#blogImagesCarousel" data-bs-slide-to="{{ $index }}" 
                                            class="{{ $index === 0 ? 'active' : '' }}" 
                                            aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                            aria-label="Slide {{ $index + 1 }}"></button>
                                    @endforeach
                                </div>
                                <div class="carousel-inner">
                                    @foreach($blog->images_paths as $index => $imagePath)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ Storage::url($imagePath) }}" 
                                             class="d-block w-100" 
                                             alt="{{ $blog->title }}" 
                                             style="height: 600px; object-fit: cover;">
                                    </div>
                                    @endforeach
                                </div>
                                @if(count($blog->images_paths) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#blogImagesCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#blogImagesCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                                @endif
                            </div>
                        @endif
                    </div>
                    @endif

                    @if($blog->has_video && $blog->video_path)
                    <div class="mb-4">
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm" style="background: #000;">
                            <video controls class="w-100">
                                <source src="{{ Storage::url($blog->video_path) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="blog-content mb-4" style="font-size: 1.15rem; line-height: 1.9; color: #2c3e50;">
                        {!! nl2br(e($blog->content)) !!}
                    </div>

                    <!-- Stats & Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <div class="d-flex gap-4">
                            @auth
                            <form action="{{ route('blogs.like', $blog) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link text-decoration-none p-0 {{ $blog->isLikedBy() ? 'text-danger' : 'text-muted' }}">
                                    <i class="bi bi-heart{{ $blog->isLikedBy() ? '-fill' : '' }} me-1"></i>
                                    <span>{{ $blog->likes_count }} Likes</span>
                                </button>
                            </form>
                            @else
                            <span class="text-muted">
                                <i class="bi bi-heart me-1"></i>{{ $blog->likes_count }} Likes
                            </span>
                            @endauth

                            <span class="text-muted">
                                <i class="bi bi-chat me-1"></i>{{ $blog->comments_count }} Comments
                            </span>

                            <span class="text-muted">
                                <i class="bi bi-eye me-1"></i>{{ $blog->views_count }} Views
                            </span>
                        </div>
                    </div>
                </div>
            </article>

            <!-- Comments Section -->
            <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Comments ({{ $blog->comments_count }})</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Add Comment Form -->
                    @auth
                    <form action="{{ route('blogs.comments.store', $blog) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-3">
                            <textarea name="comment" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Add a comment..." 
                                      required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success rounded-pill">
                            <i class="bi bi-send-fill me-1"></i>Post Comment
                        </button>
                    </form>
                    @else
                    <div class="alert alert-success">
                        <a href="{{ route('login') }}" class="text-dark fw-bold">Login</a> to add a comment
                    </div>
                    @endauth

                    <!-- Comments List -->
                    @forelse($blog->comments as $comment)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex align-items-start">
                            <div class="user-avatar text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold">{{ $comment->user->full_name }}</h6>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                <p class="mt-2 mb-0">{{ $comment->comment }}</p>

                                <!-- Replies -->
                                @if($comment->replies->count() > 0)
                                <div class="mt-3 ms-4">
                                    @foreach($comment->replies as $reply)
                                    <div class="border-start ps-3 mb-2">
                                        <h6 class="mb-1 fw-bold small">{{ $reply->user->full_name }}</h6>
                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                        <p class="mt-1 mb-0 small">{{ $reply->comment }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @auth
                                @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                <form action="{{ route('blogs.comments.destroy', $comment) }}" method="POST" id="deleteCommentForm{{ $comment->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-link text-danger p-0" onclick="confirmDeleteComment({{ $comment->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-4">No comments yet. Be the first to comment!</p>
                    @endforelse
                </div>
            </div>

            <!-- Related Blogs -->
            @if($relatedBlogs->count() > 0)
            <div class="mt-5">
                <h4 class="mb-4 fw-bold">More from {{ $blog->user->full_name }}</h4>
                <div class="row">
                    @foreach($relatedBlogs as $related)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('blogs.show', $related) }}" class="text-decoration-none">
                            <div class="card h-100 shadow-sm hover-card" style="border-radius: 12px; transition: all 0.3s;">
                                @if($related->media_type === 'image' && $related->image_path)
                                <img src="{{ Storage::url($related->image_path) }}" 
                                     class="card-img-top" 
                                     style="height: 180px; object-fit: cover; border-radius: 12px 12px 0 0;">
                                @elseif($related->media_type === 'video' && $related->video_path)
                                <div class="position-relative" style="height: 180px; background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%); border-radius: 12px 12px 0 0;">
                                    <div class="position-absolute top-50 start-50 translate-middle text-white">
                                        <i class="bi bi-play-circle" style="font-size: 4rem;"></i>
                                    </div>
                                </div>
                                @else
                                <div style="height: 180px; background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%); border-radius: 12px 12px 0 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-card-text text-muted" style="font-size: 3rem;"></i>
                                </div>
                                @endif
                                <div class="card-body">
                                    <h6 class="card-title text-dark fw-bold">{{ Str::limit($related->title, 45) }}</h6>
                                    <p class="card-text text-muted small">{{ Str::limit($related->content, 70) }}</p>
                                    <div class="d-flex gap-3 text-muted small mt-2">
                                        <span><i class="bi bi-heart"></i> {{ $related->likes_count }}</span>
                                        <span><i class="bi bi-chat"></i> {{ $related->comments_count }}</span>
                                        <span><i class="bi bi-eye"></i> {{ $related->views_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Comment Confirmation Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Comment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">Are you sure you want to delete this comment?</p>
                <div class="alert alert-warning d-flex align-items-center" style="border-radius: 10px;">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <small>This action cannot be undone.</small>
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger rounded-pill px-4" id="confirmDeleteCommentBtn">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
    /* Carousel styling */
    #blogImagesCarousel .carousel-control-prev-icon,
    #blogImagesCarousel .carousel-control-next-icon {
        background-color: rgba(0,0,0,0.5);
        border-radius: 50%;
        padding: 20px;
        filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3));
    }
    
    #blogImagesCarousel .carousel-indicators {
        margin-bottom: 15px;
    }
    
    #blogImagesCarousel .carousel-indicators [data-bs-target] {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: rgba(255,255,255,0.6);
        border: 2px solid rgba(0,0,0,0.3);
    }
    
    #blogImagesCarousel .carousel-indicators .active {
        background-color: #fff;
    }

    .hover-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
    }
    
    /* Custom Toast Notifications */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }
    
    .custom-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        min-width: 300px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
    }
    
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
// Delete Comment Confirmation
let deleteCommentFormId = null;

function confirmDeleteComment(commentId) {
    deleteCommentFormId = commentId;
    const modal = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
    modal.show();
}

document.getElementById('confirmDeleteCommentBtn').addEventListener('click', function() {
    if (deleteCommentFormId) {
        document.getElementById('deleteCommentForm' + deleteCommentFormId).submit();
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

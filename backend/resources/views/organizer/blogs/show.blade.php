@extends('layouts.organizer')

@section('title', $blog->title . ' - Organizer Panel')

@section('page-title', 'Blog Details')
@section('page-subtitle', 'View and manage your blog')

@section('content')
<div class="container-fluid p-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('organizer.blogs.index') }}" class="btn btn-link text-muted mb-3 ps-0">
                <i class="bi bi-arrow-left me-2"></i>Back to My Blogs
            </a>

            <!-- Blog Card -->
            <div class="card shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <!-- Header Actions -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            @if($blog->is_published)
                                <span class="badge bg-success mb-2"><i class="bi bi-eye me-1"></i>Published</span>
                            @else
                                <span class="badge bg-secondary mb-2"><i class="bi bi-eye-slash me-1"></i>Draft</span>
                            @endif
                            @if($blog->ai_assisted)
                                <span class="badge bg-warning text-dark mb-2"><i class="bi bi-stars me-1"></i>AI Assisted</span>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('organizer.blogs.edit', $blog) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="{{ route('blogs.show', $blog) }}" class="btn btn-success btn-sm" target="_blank">
                                <i class="bi bi-box-arrow-up-right me-1"></i>View Public
                            </a>
                        </div>
                    </div>

                    <!-- Title -->
                    <h2 class="mb-3 fw-bold">{{ $blog->title }}</h2>

                    <!-- Meta Info -->
                    <div class="mb-4 text-muted">
                        <small><i class="bi bi-clock me-1"></i>Created {{ $blog->created_at->diffForHumans() }}</small>
                        @if($blog->updated_at != $blog->created_at)
                        <span class="mx-2">•</span>
                        <small><i class="bi bi-pencil me-1"></i>Updated {{ $blog->updated_at->diffForHumans() }}</small>
                        @endif
                    </div>

                    <!-- Media -->
                    @if($blog->media_type === 'image' && $blog->image_path)
                    <div class="mb-4">
                        <img src="{{ Storage::url($blog->image_path) }}" 
                             alt="{{ $blog->title }}" 
                             class="img-fluid rounded shadow-sm"
                             style="width: 100%; max-height: 500px; object-fit: cover;">
                    </div>
                    @endif

                    @if($blog->media_type === 'video' && $blog->video_path)
                    <div class="mb-4">
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                            <video controls class="w-100">
                                <source src="{{ Storage::url($blog->video_path) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    @endif

                    <!-- Content -->
                    <div class="blog-content" style="font-size: 1.1rem; line-height: 1.8;">
                        {!! nl2br(e($blog->content)) !!}
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-chat-dots me-2"></i>Comments ({{ $blog->comments_count }})
                    </h5>
                </div>
                <div class="card-body p-4">
                    @forelse($blog->comments as $comment)
                    <div class="d-flex mb-4 pb-3 border-bottom">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center"
                                 style="width: 45px; height: 45px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold">{{ $comment->user->full_name }}</h6>
                            <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            <p class="mt-2 mb-2">{{ $comment->comment }}</p>

                            @if($comment->replies->count() > 0)
                            <div class="mt-3 ms-4">
                                @foreach($comment->replies as $reply)
                                <div class="d-flex mb-2 pb-2 border-start ps-3">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                             style="width: 35px; height: 35px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 small fw-bold">{{ $reply->user->full_name }}</h6>
                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                        <p class="mb-0 mt-1 small">{{ $reply->comment }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted py-4">
                        <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                        No comments yet
                    </p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Stats Card -->
            <div class="card shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up text-success me-2"></i>Performance</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <div class="text-center flex-fill">
                            <div class="text-danger fs-3">{{ $blog->likes_count }}</div>
                            <small class="text-muted"><i class="bi bi-heart-fill"></i> Likes</small>
                        </div>
                        <div class="text-center flex-fill border-start">
                            <div class="text-primary fs-3">{{ $blog->comments_count }}</div>
                            <small class="text-muted"><i class="bi bi-chat-fill"></i> Comments</small>
                        </div>
                        <div class="text-center flex-fill border-start">
                            <div class="text-success fs-3">{{ $blog->views_count }}</div>
                            <small class="text-muted"><i class="bi bi-eye-fill"></i> Views</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted d-block mb-2">Engagement Rate</small>
                        @php
                            $engagementRate = $blog->views_count > 0 
                                ? round((($blog->likes_count + $blog->comments_count) / $blog->views_count) * 100, 1) 
                                : 0;
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ min($engagementRate, 100) }}%"></div>
                        </div>
                        <small class="text-muted">{{ $engagementRate }}%</small>
                    </div>
                </div>
            </div>

            <!-- Who Liked This Blog -->
            @if($blog->likedBy->count() > 0)
            <div class="card shadow-sm mb-4" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-heart-fill text-danger me-2"></i>Liked By</h6>
                </div>
                <div class="card-body p-4">
                    @foreach($blog->likedBy->take(10) as $user)
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2"
                             style="width: 35px; height: 35px;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $user->full_name }}</div>
                            <small class="text-muted">{{ $user->pivot->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @endforeach
                    @if($blog->likedBy->count() > 10)
                    <small class="text-muted">+ {{ $blog->likedBy->count() - 10 }} more</small>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h6 class="mb-3 fw-bold">Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('organizer.blogs.edit', $blog) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i>Edit Blog
                        </a>
                        <a href="{{ route('blogs.show', $blog) }}" class="btn btn-success" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-2"></i>View Public Page
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $blog->id }}, '{{ addslashes($blog->title) }}')">
                            <i class="bi bi-trash me-2"></i>Delete Blog
                        </button>
                    </div>

                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST" id="deleteBlogForm{{ $blog->id }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Delete Blog
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-2">Are you sure you want to delete this blog?</p>
                <p class="fw-bold text-dark mb-3" id="blogTitleToDelete"></p>
                <div class="alert alert-warning d-flex align-items-center">
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

@push('scripts')
<script>
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
</script>
@endpush
@endsection

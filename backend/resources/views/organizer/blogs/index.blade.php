@extends('layouts.organizer')

@section('title', 'My Blogs - Organizer Panel')

@section('page-title', 'My Blogs')
@section('page-subtitle', 'Manage your blog posts')

@section('content')
<div class="container-fluid p-4">
    <!-- Header Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">All Blogs ({{ $blogs->total() }})</h4>
            <small class="text-muted">Share your environmental stories with the community</small>
        </div>
        <a href="{{ route('organizer.blogs.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-2"></i>Create New Blog
        </a>
    </div>

    @if($blogs->count() > 0)
    <!-- Blogs Grid -->
    <div class="row">
        @foreach($blogs as $blog)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100 shadow-sm blog-card" style="border-radius: 15px; transition: all 0.3s; overflow: hidden;">
                <!-- Media -->
                @if($blog->has_images && $blog->images_paths && count($blog->images_paths) > 0)
                    @if(count($blog->images_paths) === 1)
                        <img src="{{ Storage::url($blog->images_paths[0]) }}" 
                             class="card-img-top" 
                             style="height: 200px; object-fit: cover;">
                    @else
                        <!-- Mini Carousel -->
                        <div id="orgBlogCarousel{{ $blog->id }}" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-indicators" style="margin-bottom: 5px;">
                                @foreach($blog->images_paths as $index => $imagePath)
                                <button type="button" 
                                        data-bs-target="#orgBlogCarousel{{ $blog->id }}" 
                                        data-bs-slide-to="{{ $index }}" 
                                        class="{{ $index === 0 ? 'active' : '' }}"
                                        style="width: 6px; height: 6px; border-radius: 50%;"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($blog->images_paths as $index => $imagePath)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ Storage::url($imagePath) }}" 
                                         class="d-block w-100" 
                                         style="height: 200px; object-fit: cover;">
                                </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#orgBlogCarousel{{ $blog->id }}" data-bs-slide="prev" style="width: 30px;">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 10px;"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#orgBlogCarousel{{ $blog->id }}" data-bs-slide="next" style="width: 30px;">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; padding: 10px;"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-dark bg-opacity-75 small">
                                    <i class="bi bi-images me-1"></i>{{ count($blog->images_paths) }}
                                </span>
                            </div>
                        </div>
                    @endif
                @elseif($blog->has_video && $blog->video_path)
                <div class="position-relative" style="height: 200px; background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                    <div class="position-absolute top-50 start-50 translate-middle text-white">
                        <i class="bi bi-play-circle" style="font-size: 4rem;"></i>
                    </div>
                    <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2">
                        <i class="bi bi-camera-video me-1"></i>Video
                    </span>
                </div>
                @else
                <div style="height: 200px; background: linear-gradient(135deg, #e9ecef 0%, #f8f9fa 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-card-text text-muted" style="font-size: 3rem;"></i>
                </div>
                @endif

                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-2">
                        @if($blog->is_published)
                            <span class="badge bg-success"><i class="bi bi-eye me-1"></i>Published</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-eye-slash me-1"></i>Draft</span>
                        @endif
                        @if($blog->ai_assisted)
                            <span class="badge bg-warning text-dark"><i class="bi bi-stars me-1"></i>AI Assisted</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h5 class="card-title fw-bold">{{ Str::limit($blog->title, 50) }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($blog->content, 100) }}</p>

                    <!-- Stats -->
                    <div class="d-flex gap-3 mb-3 text-muted small">
                        <span title="Likes"><i class="bi bi-heart text-danger"></i> {{ $blog->likes_count }}</span>
                        <span title="Comments"><i class="bi bi-chat"></i> {{ $blog->comments_count }}</span>
                        <span title="Views"><i class="bi bi-eye"></i> {{ $blog->views_count }}</span>
                    </div>

                    <small class="text-muted d-block mb-3">
                        <i class="bi bi-clock me-1"></i>{{ $blog->created_at->diffForHumans() }}
                    </small>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('organizer.blogs.show', $blog) }}" class="btn btn-sm btn-success flex-fill">
                            <i class="bi bi-eye me-1"></i>View
                        </a>
                        <a href="{{ route('organizer.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete({{ $blog->id }}, '{{ addslashes($blog->title) }}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>

                    <!-- Hidden delete form -->
                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST" id="deleteBlogForm{{ $blog->id }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $blogs->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-newspaper" style="font-size: 5rem; color: #d1d5db;"></i>
        </div>
        <h3 class="mb-2">No Blogs Yet</h3>
        <p class="text-muted mb-4">Start sharing your environmental stories with the community!</p>
        <a href="{{ route('organizer.blogs.create') }}" class="btn btn-success btn-lg">
            <i class="bi bi-plus-circle me-2"></i>Create Your First Blog
        </a>
    </div>
    @endif
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
<style>
    .blog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
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
</style>

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

@extends('layouts.organizer')

@section('title', 'Edit Blog - Organizer Panel')

@section('page-title', 'Edit Blog')
@section('page-subtitle', 'Update your blog post')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('organizer.blogs.show', $blog) }}" class="btn btn-link text-muted mb-3 ps-0">
                <i class="bi bi-arrow-left me-2"></i>Back to Blog
            </a>

            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Blog Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title"
                                   name="title" 
                                   value="{{ old('title', $blog->title) }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <label for="content" class="form-label fw-bold">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content"
                                      name="content" 
                                      rows="12" 
                                      required>{{ old('content', $blog->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Media -->
                        @if($blog->media_type !== 'none')
                        <div class="mb-4">
                            <label class="form-label fw-bold">Current Media</label>
                            @if($blog->media_type === 'image' && $blog->image_path)
                            <div class="position-relative d-inline-block">
                                <img src="{{ Storage::url($blog->image_path) }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 200px;">
                                <span class="badge bg-success position-absolute top-0 start-0 m-2">Current Image</span>
                            </div>
                            @elseif($blog->media_type === 'video' && $blog->video_path)
                            <div class="position-relative">
                                <video src="{{ Storage::url($blog->video_path) }}" 
                                       class="rounded" 
                                       controls 
                                       style="max-height: 300px; max-width: 100%;"></video>
                                <span class="badge bg-success position-absolute top-0 start-0 m-2">Current Video</span>
                            </div>
                            @endif
                            <div class="form-text">Upload a new file below to replace the current media</div>
                        </div>
                        @endif

                        <!-- Upload New Media -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Upload New Media (Optional)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-image fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload New Image</span>
                                        <small class="text-muted">Max: 5MB</small>
                                        <input type="file" name="image" accept="image/*" class="d-none" onchange="previewMedia(this, 'image')">
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-camera-video fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload New Video</span>
                                        <small class="text-muted">Max: 50MB</small>
                                        <input type="file" name="video" accept="video/*" class="d-none" onchange="previewMedia(this, 'video')">
                                    </label>
                                </div>
                            </div>
                            @error('image')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            @error('video')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- New Media Preview -->
                        <div id="mediaPreview" class="mb-4" style="display: none;"></div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 justify-content-between">
                            <a href="{{ route('organizer.blogs.show', $blog) }}" class="btn btn-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-check-circle me-1"></i>Update Blog
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewMedia(input, type) {
    const preview = document.getElementById('mediaPreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.style.display = 'block';
            
            if (type === 'image') {
                // Clear video input
                const videoInput = document.querySelector('input[name="video"]');
                if (videoInput) videoInput.value = '';
                
                preview.innerHTML = `
                    <div class="alert alert-success">
                        <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>New Image Preview</h6>
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 400px;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                    onclick="clearMedia('image')">
                                <i class="bi bi-x"></i> Remove
                            </button>
                        </div>
                    </div>`;
            } else if (type === 'video') {
                // Clear image input
                const imageInput = document.querySelector('input[name="image"]');
                if (imageInput) imageInput.value = '';
                
                preview.innerHTML = `
                    <div class="alert alert-success">
                        <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>New Video Preview</h6>
                        <div class="position-relative">
                            <video src="${e.target.result}" class="w-100 rounded" controls style="max-height: 400px;"></video>
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                    onclick="clearMedia('video')">
                                <i class="bi bi-x"></i> Remove
                            </button>
                        </div>
                    </div>`;
            }
        }
        
        reader.readAsDataURL(file);
    }
}

function clearMedia(type) {
    const input = document.querySelector(`input[name="${type}"]`);
    if (input) input.value = '';
    
    const preview = document.getElementById('mediaPreview');
    preview.innerHTML = '';
    preview.style.display = 'none';
}
</script>
@endpush
@endsection

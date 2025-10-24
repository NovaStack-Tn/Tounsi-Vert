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

                        <!-- Current Images -->
                        @if($blog->has_images && $blog->images_paths)
                        <div class="mb-4">
                            <label class="form-label fw-bold">Current Images</label>
                            <div class="row g-2" id="currentImages">
                                @foreach($blog->images_paths as $index => $imagePath)
                                <div class="col-md-3" id="currentImage{{ $index }}">
                                    <div class="position-relative">
                                        <img src="{{ Storage::url($imagePath) }}" 
                                             class="img-fluid rounded" 
                                             style="height: 150px; width: 100%; object-fit: cover;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                                onclick="removeCurrentImage('{{ $imagePath }}', {{ $index }})">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        <span class="badge bg-success position-absolute bottom-0 start-0 m-1">{{ $index + 1 }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div id="removeImagesInput"></div>
                        </div>
                        @endif

                        <!-- Current Video -->
                        @if($blog->has_video && $blog->video_path)
                        <div class="mb-4">
                            <label class="form-label fw-bold">Current Video</label>
                            <div class="position-relative" id="currentVideoContainer">
                                <video src="{{ Storage::url($blog->video_path) }}" 
                                       class="rounded" 
                                       controls 
                                       style="max-height: 300px; max-width: 100%;"></video>
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                        onclick="removeCurrentVideo()">
                                    <i class="bi bi-x"></i> Remove
                                </button>
                            </div>
                            <input type="hidden" name="remove_video" id="removeVideoInput" value="0">
                        </div>
                        @endif

                        <!-- Upload New Media -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Add More Media (Optional)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-images fs-3 d-block mb-2"></i>
                                        <span class="d-block">Add More Images</span>
                                        <small class="text-muted">Max: 5 total, 5MB each</small>
                                        <input type="file" name="images[]" accept="image/*" multiple class="d-none" onchange="previewImages(this)">
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-camera-video fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload New Video</span>
                                        <small class="text-muted">Max: 50MB</small>
                                        <input type="file" name="video" accept="video/*" class="d-none" onchange="previewVideo(this)">
                                    </label>
                                </div>
                            </div>
                            @error('images')
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
let imagesToRemove = [];

function removeCurrentImage(imagePath, index) {
    // Add to remove list
    imagesToRemove.push(imagePath);
    
    // Update hidden inputs
    const removeInputContainer = document.getElementById('removeImagesInput');
    removeInputContainer.innerHTML = '';
    imagesToRemove.forEach(path => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_images[]';
        input.value = path;
        removeInputContainer.appendChild(input);
    });
    
    // Remove from UI
    const imageElement = document.getElementById('currentImage' + index);
    if (imageElement) imageElement.remove();
}

function removeCurrentVideo() {
    document.getElementById('removeVideoInput').value = '1';
    const videoContainer = document.getElementById('currentVideoContainer');
    if (videoContainer) videoContainer.remove();
}

function previewImages(input) {
    const preview = document.getElementById('mediaPreview');
    
    if (input.files && input.files.length > 0) {
        if (input.files.length > 5) {
            alert('Maximum 5 images allowed!');
            input.value = '';
            return;
        }
        
        preview.innerHTML = '<div class="alert alert-success"><h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>New Images Preview</h6><div class="row g-2" id="newImagesGrid"></div></div>';
        preview.style.display = 'block';
        
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                onclick="removeNewImage(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                        <span class="badge bg-success position-absolute bottom-0 start-0 m-1">New ${index + 1}</span>
                    </div>`;
                document.getElementById('newImagesGrid').appendChild(col);
            }
            reader.readAsDataURL(file);
        });
    }
}

function removeNewImage(index) {
    const input = document.querySelector('input[name="images[]"]');
    if (input) {
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        input.files = dt.files;
        previewImages(input);
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
                <div class="alert alert-success">
                    <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>New Video Preview</h6>
                    <div class="position-relative">
                        <video src="${e.target.result}" class="w-100 rounded" controls style="max-height: 400px;"></video>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                onclick="clearNewVideo()">
                            <i class="bi bi-x"></i> Remove
                        </button>
                    </div>
                </div>`;
            
            const existingVideo = document.getElementById('videoPreviewContainer');
            if (existingVideo) existingVideo.remove();
            
            preview.appendChild(videoPreview);
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    }
}

function clearNewVideo() {
    const input = document.querySelector('input[name="video"]');
    if (input) input.value = '';
    
    const videoContainer = document.getElementById('videoPreviewContainer');
    if (videoContainer) videoContainer.remove();
}
</script>
@endpush
@endsection

@extends('layouts.organizer')

@section('title', 'Create Blog - Organizer Panel')

@section('page-title', 'Create New Blog')
@section('page-subtitle', 'Share your environmental story')

@section('content')
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Blog Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                   id="title"
                                   name="title" 
                                   placeholder="Enter an engaging title..." 
                                   value="{{ old('title') }}"
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
                                      placeholder="Share your thoughts, experiences, and ideas..."
                                      required>{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 10 characters</small>
                        </div>

                        <!-- Media Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Media (Optional)</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-image fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload Image</span>
                                        <small class="text-muted">Max: 5MB</small>
                                        <input type="file" name="image" accept="image/*" class="d-none" onchange="previewMedia(this, 'image')">
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-camera-video fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload Video</span>
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

                        <!-- Media Preview -->
                        <div id="mediaPreview" class="mb-4" style="display: none;"></div>

                        <!-- AI Assistance -->
                        <div class="mb-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><i class="bi bi-stars text-warning me-2"></i>AI Assistance</h6>
                                            <small class="text-muted">Let AI help improve your writing</small>
                                        </div>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="requestAIHelp()">
                                            <i class="bi bi-magic me-1"></i>Enhance
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="ai_assisted" id="aiAssisted" value="0">
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('organizer.blogs.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-x-circle me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="bi bi-send-fill me-1"></i>Publish Blog
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
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 400px;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                onclick="clearMedia('image')">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>`;
            } else if (type === 'video') {
                // Clear image input
                const imageInput = document.querySelector('input[name="image"]');
                if (imageInput) imageInput.value = '';
                
                preview.innerHTML = `
                    <div class="position-relative">
                        <video src="${e.target.result}" class="w-100 rounded" controls style="max-height: 400px;"></video>
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                onclick="clearMedia('video')">
                            <i class="bi bi-x"></i>
                        </button>
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

function requestAIHelp() {
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    if (!title && !content) {
        alert('Please write something first!');
        return;
    }
    
    // Mark as AI assisted
    document.getElementById('aiAssisted').value = '1';
    
    // Placeholder for AI integration
    alert('AI assistance will help enhance your blog post with better grammar and structure! (Feature coming soon)');
}
</script>
@endpush
@endsection

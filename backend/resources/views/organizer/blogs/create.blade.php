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
                                        <i class="bi bi-images fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload Images</span>
                                        <small class="text-muted">Max: 5 images, 5MB each</small>
                                        <input type="file" name="images[]" accept="image/*" multiple class="d-none" onchange="previewImages(this)">
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="btn btn-outline-success w-100 py-3" style="cursor: pointer; border-style: dashed;">
                                        <i class="bi bi-camera-video fs-3 d-block mb-2"></i>
                                        <span class="d-block">Upload Video</span>
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

                        <!-- Media Preview -->
                        <div id="mediaPreview" class="mb-4" style="display: none;"></div>

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

                        <!-- AI Assistance -->
                        <div class="mb-4">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="mb-3"><i class="bi bi-stars text-warning me-2"></i>AI Assistance</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-warning btn-sm w-100" onclick="showAIImageUploadModal()">
                                                <i class="bi bi-magic me-1"></i>Generate from Image
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-warning btn-sm w-100" onclick="enhanceOrgContent()">
                                                <i class="bi bi-stars me-1"></i>Enhance Content
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-warning btn-sm w-100" onclick="generateOrgBanner()">
                                                <i class="bi bi-image me-1"></i>Generate Banner
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">AI will help create and improve your content</small>
                                </div>
                            </div>
                            <input type="hidden" name="ai_assisted" id="aiAssisted" value="0">
                            <div id="aiGeneratedImageContainer"></div>
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
<style>
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

    .spinner-container {
        display: inline-block;
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

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
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

    @keyframes slideOutRight {
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }

    .toast-closing {
        animation: slideOutRight 0.3s ease;
    }
</style>

<!-- Toast Container -->
<div class="toast-container-custom" id="toastContainer"></div>

<script>
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
                        <img src="${e.target.result}" class="img-fluid rounded" style="height: 200px; width: 100%; object-fit: cover;">
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
                    <video src="${e.target.result}" class="w-100 rounded" controls style="max-height: 400px;"></video>
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                            onclick="clearVideo()">
                        <i class="bi bi-x"></i>
                    </button>
                </div>`;
            
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
        const dt = new DataTransfer();
        const files = Array.from(input.files);
        files.splice(index, 1);
        files.forEach(file => dt.items.add(file));
        input.files = dt.files;
        previewImages(input);
    }
}

function clearVideo() {
    const input = document.querySelector('input[name="video"]');
    if (input) input.value = '';
    
    const videoContainer = document.getElementById('videoPreviewContainer');
    if (videoContainer) videoContainer.remove();
}

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

// Show AI Image Upload Modal
function showAIImageUploadModal() {
    const modalHtml = `
        <div class="modal fade" id="aiImageModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 15px;">
                    <div class="modal-header border-0 bg-warning bg-opacity-10">
                        <h5 class="modal-title"><i class="bi bi-magic me-2"></i>Generate Blog from Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted">Upload an image and AI will create a blog post about it!</p>
                        <label class="btn btn-outline-warning w-100 py-4" style="cursor: pointer; border-style: dashed;">
                            <i class="bi bi-cloud-upload fs-1 d-block mb-2"></i>
                            <span class="d-block">Click to Upload Image</span>
                            <small class="text-muted">AI will analyze and create content</small>
                            <input type="file" id="aiImageInput" accept="image/*" class="d-none" onchange="generateFromOrgImage(this)">
                        </label>
                    </div>
                </div>
            </div>
        </div>`;
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('aiImageModal'));
    modal.show();
}

// Generate from image
function generateFromOrgImage(input) {
    if (!input.files || !input.files[0]) return;

    const formData = new FormData();
    formData.append('image', input.files[0]);

    showAILoading('🔍 Analyzing image and generating content...');

    fetch('{{ route("blogs.ai.generateFromImage") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            document.getElementById('title').value = data.title;
            document.getElementById('content').value = data.content;
            document.getElementById('aiAssisted').value = '1';
            
            if (data.image_path) {
                const container = document.getElementById('aiGeneratedImageContainer');
                container.innerHTML = `<input type="hidden" name="ai_generated_image" value="${data.image_path}">`;
                
                const preview = document.getElementById('mediaPreview');
                preview.innerHTML = `<div class="alert alert-warning"><i class="bi bi-magic me-2"></i>AI Generated Image Added</div>`;
                preview.style.display = 'block';
            }
            
            bootstrap.Modal.getInstance(document.getElementById('aiImageModal')).hide();
            showToast(data.message || 'Content generated successfully!', 'success');
        } else {
            showToast(data.message || 'Failed to generate content', 'error');
        }
    })
    .catch(error => {
        hideAILoading();
        showToast('Error: ' + error.message, 'error');
    });
}

// Enhance content
function enhanceOrgContent() {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();

    if (!title && !content) {
        showToast('Please write something first!', 'warning');
        return;
    }

    showAILoading('✨ Enhancing your content with AI...');

    fetch('{{ route("blogs.ai.enhanceContent") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ title, content })
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            document.getElementById('title').value = data.title;
            document.getElementById('content').value = data.content;
            document.getElementById('aiAssisted').value = '1';
            showToast(data.message || 'Content enhanced successfully!', 'success');
        } else {
            showToast(data.message || 'Failed to enhance content', 'error');
        }
    })
    .catch(error => {
        hideAILoading();
        showToast('Error: ' + error.message, 'error');
    });
}

// Generate banner
function generateOrgBanner() {
    const title = document.getElementById('title').value.trim();

    if (!title) {
        showToast('Please enter a title first!', 'warning');
        return;
    }

    showAILoading('🎨 Generating banner image with DALL-E...');

    fetch('{{ route("blogs.ai.generateBanner") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ title, content: document.getElementById('content').value })
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        if (data.success) {
            const container = document.getElementById('aiGeneratedImageContainer');
            container.innerHTML = `<input type="hidden" name="ai_banner_image" value="${data.image_path}">`;
            
            const preview = document.getElementById('mediaPreview');
            preview.innerHTML = `<div class="alert alert-warning">
                <i class="bi bi-magic me-2"></i>AI Generated Banner Image Added
                <img src="${data.image_url}" class="img-fluid rounded mt-2" style="max-height: 200px;">
            </div>`;
            preview.style.display = 'block';
            
            document.getElementById('aiAssisted').value = '1';
            showToast(data.message || 'Banner generated successfully!', 'success');
        } else {
            showToast(data.message || 'Failed to generate banner', 'error');
        }
    })
    .catch(error => {
        hideAILoading();
        showToast('Error: ' + error.message, 'error');
    });
}

// Show/Hide AI loading overlay
function showAILoading(message) {
    document.getElementById('aiLoadingText').textContent = message;
    document.getElementById('aiLoadingOverlay').style.display = 'flex';
}

function hideAILoading() {
    document.getElementById('aiLoadingOverlay').style.display = 'none';
}
</script>
@endpush
@endsection

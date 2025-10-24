<!-- AI-Enhanced Blog Creation Form -->
<div class="card shadow-sm mb-4" style="border-radius: 15px; border: none;">
    <div class="card-body p-4">
        <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data" id="createBlogForm">
            @csrf
            
            <!-- AI Loading Indicator -->
            <div id="aiLoadingIndicator" class="alert alert-info mb-3" style="display: none;">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-3" role="status"></div>
                    <div>
                        <strong>🤖 AI is working...</strong>
                        <div id="aiLoadingText" class="small">Processing your request...</div>
                    </div>
                </div>
            </div>

            <!-- Title Input -->
            <div class="mb-3">
                <input type="text" 
                       class="form-control border-0 fs-5 fw-bold" 
                       name="title" 
                       id="blogTitle"
                       placeholder="What's your environmental story?"
                       style="background: transparent;"
                       required>
            </div>

            <!-- AI-generated content indicator -->
            <input type="hidden" name="ai_assisted" id="aiAssisted" value="0">

            <!-- Content Textarea -->
            <div class="mb-3">
                <textarea class="form-control border-0" 
                          name="content"
                          id="blogContent"
                          rows="4" 
                          placeholder="Share your thoughts with the community..."
                          style="background: #f8f9fa; border-radius: 12px; resize: none;"
                          required></textarea>
            </div>

            <!-- Media Preview -->
            <div id="mediaPreview" class="mb-3" style="display: none;"></div>

            <!-- AI Hidden Image Path (for AI generated content from image) -->
            <div id="aiGeneratedImageContainer"></div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 flex-wrap">
                    <!-- Upload Images -->
                    <label class="btn btn-light btn-sm rounded-pill" style="cursor: pointer;" title="Upload Images (Max 5)">
                        <i class="bi bi-images text-success"></i>
                        <input type="file" name="images[]" id="imageInput" accept="image/*" multiple style="display: none;" onchange="handleImageUpload(this)">
                    </label>

                    <!-- Upload Video -->
                    <label class="btn btn-light btn-sm rounded-pill" style="cursor: pointer;" title="Upload Video">
                        <i class="bi bi-camera-video text-success"></i>
                        <input type="file" name="video" accept="video/*" style="display: none;" onchange="previewVideo(this)">
                    </label>

                    <!-- AI Generate from Image -->
                    <button type="button" class="btn btn-warning btn-sm rounded-pill" onclick="showAIImageUpload()" title="AI: Generate from Image">
                        <i class="bi bi-magic me-1"></i>AI Image
                    </button>

                    <!-- AI Enhance Content -->
                    <button type="button" class="btn btn-warning btn-sm rounded-pill" onclick="enhanceContent()" title="AI: Enhance Writing">
                        <i class="bi bi-stars me-1"></i>AI Enhance
                    </button>

                    <!-- AI Generate Banner -->
                    <button type="button" class="btn btn-warning btn-sm rounded-pill" onclick="generateBanner()" title="AI: Generate Banner">
                        <i class="bi bi-image me-1"></i>AI Banner
                    </button>
                </div>

                <button type="submit" class="btn btn-success rounded-pill px-4">
                    <i class="bi bi-send-fill me-1"></i>Post
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AI Image Upload Modal -->
<div class="modal fade" id="aiImageModal" tabindex="-1" aria-hidden="true">
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
                        <input type="file" id="aiImageInput" accept="image/*" class="d-none" onchange="generateFromImage(this)">
                    </label>
                </div>
                <div id="aiImagePreview"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let aiImageModal = null;

// Initialize modal
document.addEventListener('DOMContentLoaded', function() {
    aiImageModal = new bootstrap.Modal(document.getElementById('aiImageModal'));
});

// Show AI Image Upload Modal
function showAIImageUpload() {
    aiImageModal.show();
}

// Generate blog content from uploaded image
function generateFromImage(input) {
    if (!input.files || !input.files[0]) return;

    const formData = new FormData();
    formData.append('image', input.files[0]);

    showAILoading('Analyzing image and generating content...');

    fetch('{{ route('blogs.ai.generateFromImage') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        
        if (data.success) {
            // Fill in the form
            document.getElementById('blogTitle').value = data.title;
            document.getElementById('blogContent').value = data.content;
            document.getElementById('aiAssisted').value = '1';
            
            // Add the generated image to the form
            if (data.image_path) {
                const imageContainer = document.getElementById('aiGeneratedImageContainer');
                imageContainer.innerHTML = `<input type="hidden" name="ai_generated_image" value="${data.image_path}">`;
                
                // Show preview
                const preview = document.getElementById('mediaPreview');
                preview.innerHTML = `<div class="position-relative">
                    <img src="/storage/${data.image_path}" class="img-fluid rounded" style="max-height: 300px;">
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                        <i class="bi bi-magic me-1"></i>AI Generated
                    </span>
                </div>`;
                preview.style.display = 'block';
            }
            
            aiImageModal.hide();
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Failed to generate content', 'danger');
        }
    })
    .catch(error => {
        hideAILoading();
        showToast('Error: ' + error.message, 'danger');
    });
}

// Enhance existing content with AI
function enhanceContent() {
    const title = document.getElementById('blogTitle').value.trim();
    const content = document.getElementById('blogContent').value.trim();

    if (!title && !content) {
        showToast('Please write something first!', 'warning');
        return;
    }

    showAILoading('Enhancing your content with AI...');

    fetch('{{ route('blogs.ai.enhanceContent') }}', {
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
            document.getElementById('blogTitle').value = data.title;
            document.getElementById('blogContent').value = data.content;
            document.getElementById('aiAssisted').value = '1';
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Failed to enhance content', 'danger');
        }
    })
    .catch(error => {
        hideAILoading();
        showToast('Error: ' + error.message, 'danger');
    });
}

// Generate banner image with AI
function generateBanner() {
    const title = document.getElementById('blogTitle').value.trim();

    if (!title) {
        showToast('Please enter a title first!', 'warning');
        return;
    }

    showAILoading('Generating banner image with DALL-E...');

    fetch('{{ route('blogs.ai.generateBanner') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ title, content: document.getElementById('blogContent').value })
    })
    .then(response => response.json())
    .then(data => {
        hideAILoading();
        
        if (data.success) {
            // Add generated image to preview
            const preview = document.getElementById('mediaPreview');
            preview.innerHTML = `<div class="position-relative">
                <img src="${data.image_url}" class="img-fluid rounded" style="max-height: 300px;">
                <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                    <i class="bi bi-magic me-1"></i>AI Generated Banner
                </span>
            </div>`;
            preview.style.display = 'block';
            
            // Add hidden input with image path
            const imageContainer = document.getElementById('aiGeneratedImageContainer');
            imageContainer.innerHTML = `<input type="hidden" name="ai_banner_image" value="${data.image_path}">`;
            
            document.getElementById('aiAssisted').value = '1';
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Failed to generate banner', 'danger');
        }
    })
    .catch(error => {
        hideAILoading();
        showToast('Error: ' + error.message, 'danger');
    });
}

// Show AI loading indicator
function showAILoading(message) {
    const indicator = document.getElementById('aiLoadingIndicator');
    const text = document.getElementById('aiLoadingText');
    text.textContent = message;
    indicator.style.display = 'block';
}

// Hide AI loading indicator
function hideAILoading() {
    const indicator = document.getElementById('aiLoadingIndicator');
    indicator.style.display = 'none';
}

// Handle regular image upload
function handleImageUpload(input) {
    previewImages(input);
}
</script>
@endpush

/**
 * InfoKobuleti — Image Upload Preview
 * Drag & drop + click to upload with preview grid
 */

document.addEventListener('DOMContentLoaded', function() {
    const zone = document.getElementById('upload-zone');
    const input = document.getElementById('file-input');
    const grid = document.getElementById('preview-grid');
    
    if (!zone || !input || !grid) return;
    
    let selectedFiles = [];
    
    // ─── Click to upload ───────────────────────────────────
    zone.addEventListener('click', () => input.click());
    
    // ─── File input change ─────────────────────────────────
    input.addEventListener('change', function() {
        addFiles(this.files);
    });
    
    // ─── Drag & Drop ───────────────────────────────────────
    zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });
    
    zone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });
    
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
        addFiles(e.dataTransfer.files);
    });
    
    // ─── Add files to preview ──────────────────────────────
    function addFiles(fileList) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        Array.from(fileList).forEach(file => {
            // Validate
            if (!allowedTypes.includes(file.type)) {
                alert('Allowed: JPG, PNG, WEBP only');
                return;
            }
            if (file.size > maxSize) {
                alert('Max file size: 5MB');
                return;
            }
            
            selectedFiles.push(file);
        });
        
        renderPreviews();
        updateFileInput();
    }
    
    // ─── Render preview grid ───────────────────────────────
    function renderPreviews() {
        grid.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const item = document.createElement('div');
            item.className = 'upload-preview-item';
            
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = file.name;
            item.appendChild(img);
            
            // First = main photo badge
            if (index === 0) {
                const badge = document.createElement('span');
                badge.className = 'main-badge';
                badge.textContent = '⭐';
                item.appendChild(badge);
            }
            
            // Remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '<i class="ph ph-x"></i>';
            removeBtn.addEventListener('click', () => {
                selectedFiles.splice(index, 1);
                renderPreviews();
                updateFileInput();
            });
            item.appendChild(removeBtn);
            
            grid.appendChild(item);
        });
    }
    
    // ─── Update file input with selected files ─────────────
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        input.files = dt.files;
    }
});

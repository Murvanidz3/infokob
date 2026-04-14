/**
 * InfoKobuleti — AJAX Filters
 * Handles listing page filtering without full page reload
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filter-form');
    const grid = document.getElementById('property-grid');
    const paginationWrap = document.getElementById('pagination-wrap');
    const totalEl = document.getElementById('results-total');
    const sortSelect = document.getElementById('sort-select');
    const sidebar = document.getElementById('filters-sidebar');
    const toggleBar = document.querySelector('.filter-toggle-bar');
    
    if (!form || !grid) return;

    // ─── Mobile sidebar toggle ─────────────────────────────
    if (sidebar && toggleBar) {
        toggleBar.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
    }
    
    // ─── Deal type pills ───────────────────────────────────
    document.querySelectorAll('#deal-type-pills .filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('#deal-type-pills .filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('deal_type').value = this.dataset.value;
            submitFilters();
        });
    });
    
    // ─── Rooms pills ───────────────────────────────────────
    document.querySelectorAll('#rooms-pills .filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('#rooms-pills .filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('rooms').value = this.dataset.value;
            submitFilters();
        });
    });
    
    // ─── Type checkboxes ───────────────────────────────────
    document.querySelectorAll('.filter-check input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            this.closest('.filter-check').classList.toggle('active', this.checked);
            submitFilters();
        });
    });
    
    // ─── Select changes ────────────────────────────────────
    form.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', submitFilters);
    });
    
    // ─── Price inputs (debounced) ──────────────────────────
    let priceTimer;
    form.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(priceTimer);
            priceTimer = setTimeout(submitFilters, 500);
        });
    });
    
    // ─── Sort select ───────────────────────────────────────
    if (sortSelect) {
        sortSelect.addEventListener('change', submitFilters);
    }
    
    // ─── Clear filters ─────────────────────────────────────
    document.getElementById('clear-filters')?.addEventListener('click', function() {
        form.reset();
        document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.filter-check').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.filter-pill[data-value=""]').forEach(p => p.classList.add('active'));
        document.getElementById('deal_type').value = '';
        document.getElementById('rooms').value = '';
        submitFilters();
    });
    
    // ─── Form submit prevention ────────────────────────────
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitFilters();
    });
    
    // ─── Submit filters via AJAX ───────────────────────────
    function submitFilters(page) {
        const params = new URLSearchParams();
        
        // Deal type
        const dealType = document.getElementById('deal_type').value;
        if (dealType) params.append('deal_type', dealType);
        
        // Property types
        const types = [];
        form.querySelectorAll('input[name="type[]"]:checked').forEach(cb => types.push(cb.value));
        if (types.length) params.append('type', types.join(','));
        
        // Price
        const priceMin = form.querySelector('[name="price_min"]')?.value;
        const priceMax = form.querySelector('[name="price_max"]')?.value;
        if (priceMin) params.append('price_min', priceMin);
        if (priceMax) params.append('price_max', priceMax);
        
        // Rooms
        const rooms = document.getElementById('rooms').value;
        if (rooms) params.append('rooms', rooms);
        
        // Sea distance
        const sea = form.querySelector('[name="sea_distance"]')?.value;
        if (sea) params.append('sea_distance', sea);
        
        // District
        const district = form.querySelector('[name="district"]')?.value;
        if (district) params.append('district', district);
        
        // Sort
        const sort = sortSelect?.value || 'newest';
        params.append('sort', sort);
        
        // Page
        if (page) params.append('page', page);
        
        // Show skeleton loading
        showSkeletons();
        
        // Update URL
        const url = new URL(window.location.href);
        url.search = params.toString();
        window.history.replaceState({}, '', url);
        
        // Fetch
        fetch(window.BASE_URL + '/api/listings?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            grid.innerHTML = data.html;
            if (paginationWrap) paginationWrap.innerHTML = data.pagination;
            if (totalEl) totalEl.textContent = data.total;
            
            // Rebind pagination links
            bindPaginationLinks();
        })
        .catch(err => {
            console.error('Filter error:', err);
            grid.innerHTML = '<p class="text-center text-muted" style="grid-column:1/-1;padding:2rem;">Error loading results</p>';
        });
    }
    
    // ─── Skeleton loading ──────────────────────────────────
    function showSkeletons() {
        let skeletonHTML = '';
        for (let i = 0; i < 6; i++) {
            skeletonHTML += `
                <div class="skeleton-card">
                    <div class="skeleton skeleton-image"></div>
                    <div class="skeleton skeleton-line" style="margin-top:16px;width:40%"></div>
                    <div class="skeleton skeleton-line" style="width:80%"></div>
                    <div class="skeleton skeleton-line skeleton-line-short" style="margin-bottom:16px"></div>
                </div>
            `;
        }
        grid.innerHTML = skeletonHTML;
    }
    
    // ─── Pagination AJAX links ─────────────────────────────
    function bindPaginationLinks() {
        if (!paginationWrap) return;
        paginationWrap.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                submitFilters(page);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }
    
    // Initial bind
    bindPaginationLinks();
});

// Set BASE_URL for AJAX
window.BASE_URL = document.querySelector('meta[name="base-url"]')?.content || 
                   window.location.origin + window.location.pathname.split('/listings')[0];

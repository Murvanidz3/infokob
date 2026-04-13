/**
 * InfoKobuleti — Main JavaScript
 * Header scroll, count-up animation, lightbox keyboard
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ─── Header scroll effect ──────────────────────────────
    const header = document.getElementById('site-header');
    if (header) {
        let lastScroll = 0;
        window.addEventListener('scroll', function() {
            const currentScroll = window.scrollY;
            if (currentScroll > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            lastScroll = currentScroll;
        }, { passive: true });
    }
    
    // ─── Count-up animation ────────────────────────────────
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length > 0) {
        const animateCounters = () => {
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000; // ms
                const startTime = performance.now();
                
                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Ease out quad
                    const eased = 1 - (1 - progress) * (1 - progress);
                    const current = Math.floor(eased * target);
                    
                    counter.textContent = current.toLocaleString() + (target > 0 ? '+' : '');
                    
                    if (progress < 1) {
                        requestAnimationFrame(update);
                    }
                }
                
                requestAnimationFrame(update);
            });
        };
        
        // Use Intersection Observer to start animation when visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.disconnect();
                }
            });
        }, { threshold: 0.5 });
        
        const statsSection = counters[0]?.closest('.stats-bar');
        if (statsSection) {
            observer.observe(statsSection);
        } else {
            animateCounters();
        }
    }
    
    // ─── Lightbox keyboard navigation ──────────────────────
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (!lightbox || !lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') {
            lightbox.classList.remove('active');
        }
        if (e.key === 'ArrowLeft') {
            lightbox.querySelector('.lightbox-prev')?.click();
        }
        if (e.key === 'ArrowRight') {
            lightbox.querySelector('.lightbox-next')?.click();
        }
    });
    
    // ─── Flash message auto close ──────────────────────────
    document.querySelectorAll('.flash-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.flash-message').style.display = 'none';
        });
    });
    
    // ─── Image delete checkboxes (edit form) ───────────────
    document.querySelectorAll('.upload-preview-item .remove-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                this.closest('.upload-preview-item').style.opacity = checkbox.checked ? '0.3' : '1';
            }
        });
    });
});

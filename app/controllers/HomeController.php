<?php
/**
 * Home Controller
 * Handles the homepage
 */

class HomeController {
    
    public function index() {
        $propertyModel = new Property();
        
        // Get featured listings
        $featured = $propertyModel->getFeatured(6);
        
        // Get homepage stats
        $stats = $propertyModel->getStats();
        
        // SEO
        SEO::set(
            __('hero_title') . ' | ' . SITE_NAME,
            __('hero_subtitle')
        );
        
        // Render
        $noHeaderPadding = true;
        ob_start();
        require VIEW_PATH . '/home/index.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
}

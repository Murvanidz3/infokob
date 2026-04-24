<?php
/**
 * Property Controller
 * Handles listings page and single property page
 */

class PropertyController {
    private Property $propertyModel;
    
    public function __construct() {
        $this->propertyModel = new Property();
    }
    
    /**
     * Listings page with filters
     */
    public function index() {
        $filters = [
            'type'        => $_GET['type'] ?? '',
            'deal_type'   => $_GET['deal_type'] ?? '',
            'price_min'   => $_GET['price_min'] ?? '',
            'price_max'   => $_GET['price_max'] ?? '',
            'rooms'       => $_GET['rooms'] ?? '',
            'district'    => $_GET['district'] ?? '',
            'sea_distance'=> $_GET['sea_distance'] ?? '',
            'sort'        => $_GET['sort'] ?? 'newest',
        ];
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $this->propertyModel->getAll($filters, $page);
        
        $properties = $result['data'];
        $pagination = $result['pagination'];
        
        $seoTitle = __('listings_title');
        $seoDescription = __('hero_subtitle');
        if (($filters['type'] ?? '') === 'hotel_room') {
            $seoTitle = __('menu_hotels');
            $seoDescription = __('menu_hotels_subtitle');
        }
        
        SEO::set($seoTitle . ' | ' . SITE_NAME, $seoDescription);
        
        $scripts = ['filters.js'];
        
        ob_start();
        require VIEW_PATH . '/properties/index.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
    
    /**
     * AJAX API for filtered listings
     */
    public function apiIndex() {
        header('Content-Type: application/json; charset=utf-8');
        
        $filters = [
            'type'        => $_GET['type'] ?? '',
            'deal_type'   => $_GET['deal_type'] ?? '',
            'price_min'   => $_GET['price_min'] ?? '',
            'price_max'   => $_GET['price_max'] ?? '',
            'rooms'       => $_GET['rooms'] ?? '',
            'district'    => $_GET['district'] ?? '',
            'sea_distance'=> $_GET['sea_distance'] ?? '',
            'sort'        => $_GET['sort'] ?? 'newest',
        ];
        
        // Handle array type filter
        if (is_string($filters['type']) && strpos($filters['type'], ',') !== false) {
            $filters['type'] = explode(',', $filters['type']);
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $this->propertyModel->getAll($filters, $page);
        
        // Render cards HTML
        ob_start();
        if (!empty($result['data'])) {
            foreach ($result['data'] as $property) {
                require VIEW_PATH . '/partials/property-card.php';
            }
        } else {
            echo '<div class="text-center text-muted" style="grid-column: 1/-1; padding: var(--space-12) 0;">';
            echo '<i class="ph ph-magnifying-glass" style="font-size: 3rem; display: block; margin-bottom: var(--space-4);"></i>';
            echo '<h3>' . __('no_results') . '</h3>';
            echo '<p>' . __('no_results_desc') . '</p>';
            echo '</div>';
        }
        $html = ob_get_clean();
        
        // Render pagination HTML
        $pagination = $result['pagination'];
        $baseUrl = BASE_URL . '/listings';
        ob_start();
        require VIEW_PATH . '/partials/pagination.php';
        $paginationHtml = ob_get_clean();
        
        echo json_encode([
            'html' => $html,
            'pagination' => $paginationHtml,
            'total' => $result['pagination']['total_items'],
            'page' => $result['pagination']['current_page'],
            'pages' => $result['pagination']['total_pages'],
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Single property page
     */
    public function show($slug) {
        $property = $this->propertyModel->getBySlug($slug);
        
        if (!$property) {
            show404();
            return;
        }
        
        // Increment views
        $this->propertyModel->incrementViews($property['id']);
        $property['views']++;
        
        // Get similar listings
        $similar = $this->propertyModel->getSimilar($property, 3);
        
        // Determine contact info (listing override or user defaults)
        $contactPhone = $property['contact_phone'] ?: $property['user_phone'];
        $contactName = $property['contact_name'] ?: $property['user_name'];
        $contactWhatsapp = $property['contact_whatsapp'] ?: ($property['user_whatsapp'] ?? '');
        $contactTelegram = $property['contact_telegram'] ?: ($property['user_telegram'] ?? '');
        
        // SEO
        SEO::setProperty($property, [
            'title' => $property['title'] ?? '',
            'description' => $property['description'] ?? ''
        ]);
        
        $loadMaps = true;
        
        ob_start();
        require VIEW_PATH . '/properties/single.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
}

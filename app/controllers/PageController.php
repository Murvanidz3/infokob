<?php
/**
 * Page Controller
 * Handles static pages: About Kobuleti, Contact, Sitemap
 */

class PageController {
    private function renderInfoPage(array $page): void {
        SEO::set($page['title'] . ' | ' . SITE_NAME, $page['subtitle']);
        
        ob_start();
        require VIEW_PATH . '/pages/info-page.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
    
    public function kobuleti() {
        $db = Database::getInstance();
        $lang = Language::get();
        
        // Get CMS content
        $stmt = $db->prepare("SELECT * FROM kobuleti_info WHERE lang = :lang ORDER BY id ASC");
        $stmt->execute([':lang' => $lang]);
        $sections = $stmt->fetchAll();
        
        SEO::set(__('kobuleti_title') . ' — ' . __('kobuleti_subtitle') . ' | ' . SITE_NAME);
        
        ob_start();
        require VIEW_PATH . '/pages/about-kobuleti.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
    
    public function contact() {
        SEO::set(__('contact_title') . ' | ' . SITE_NAME);
        
        ob_start();
        require VIEW_PATH . '/pages/contact.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
    
    public function hotels() {
        $hotelIds = [1, 2, 3, 4, 5, 6];
        
        SEO::set(__('menu_hotels') . ' | ' . SITE_NAME, __('hotels_page_meta_desc'));
        
        ob_start();
        require VIEW_PATH . '/pages/hotels.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/main.php';
    }
    
    public function announcements() {
        $page = [
            'title' => __('menu_announcements'),
            'subtitle' => __('menu_announcements_subtitle'),
            'icon' => 'ph-megaphone-simple',
            'items' => [
                __('menu_announcements_item_1'),
                __('menu_announcements_item_2'),
                __('menu_announcements_item_3'),
            ],
            'primary_link' => Auth::isLoggedIn() ? BASE_URL . '/my/listings/create' : BASE_URL . '/login',
            'primary_text' => __('menu_announcements_cta_primary'),
            'secondary_link' => BASE_URL . '/listings',
            'secondary_text' => __('menu_announcements_cta_secondary'),
        ];
        
        $this->renderInfoPage($page);
    }
    
    public function employment() {
        $page = [
            'title' => __('menu_employment'),
            'subtitle' => __('menu_employment_subtitle'),
            'icon' => 'ph-briefcase',
            'items' => [
                __('menu_employment_item_1'),
                __('menu_employment_item_2'),
                __('menu_employment_item_3'),
            ],
            'primary_link' => Auth::isLoggedIn() ? BASE_URL . '/my/listings/create' : BASE_URL . '/register',
            'primary_text' => __('menu_employment_cta_primary'),
            'secondary_link' => BASE_URL . '/contact',
            'secondary_text' => __('menu_employment_cta_secondary'),
        ];
        
        $this->renderInfoPage($page);
    }
    
    public function education() {
        $page = [
            'title' => __('menu_education'),
            'subtitle' => __('menu_education_subtitle'),
            'icon' => 'ph-graduation-cap',
            'items' => [
                __('menu_education_item_1'),
                __('menu_education_item_2'),
                __('menu_education_item_3'),
            ],
            'primary_link' => BASE_URL . '/contact',
            'primary_text' => __('menu_education_cta_primary'),
            'secondary_link' => BASE_URL . '/kobuleti',
            'secondary_text' => __('menu_education_cta_secondary'),
        ];
        
        $this->renderInfoPage($page);
    }
    
    public function tourism() {
        $page = [
            'title' => __('menu_tourism'),
            'subtitle' => __('menu_tourism_subtitle'),
            'icon' => 'ph-map-trifold',
            'items' => [
                __('menu_tourism_item_1'),
                __('menu_tourism_item_2'),
                __('menu_tourism_item_3'),
            ],
            'primary_link' => BASE_URL . '/contact',
            'primary_text' => __('menu_tourism_cta_primary'),
            'secondary_link' => BASE_URL . '/kobuleti',
            'secondary_text' => __('menu_tourism_cta_secondary'),
        ];
        
        $this->renderInfoPage($page);
    }
    
    public function sendContact() {
        verify_csrf();
        
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        
        if (empty($name) || empty($email) || empty($message)) {
            flash('error', __('error_required'));
            redirect(BASE_URL . '/contact');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', __('error_invalid_email'));
            redirect(BASE_URL . '/contact');
        }
        
        // Send email (basic PHP mail)
        $adminEmail = (new Setting)->get('contact_email', 'info@infokobuleti.com');
        $subject = "InfoKobuleti Contact: " . $name;
        $body = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
        $headers = "From: noreply@infokobuleti.com\r\nReply-To: {$email}";
        
        @mail($adminEmail, $subject, $body, $headers);
        
        flash('success', __('contact_sent'));
        redirect(BASE_URL . '/contact');
    }
    
    public function sitemap() {
        header('Content-Type: application/xml; charset=utf-8');
        echo SEO::generateSitemap();
        exit;
    }
}

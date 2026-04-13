<?php
/**
 * Page Controller
 * Handles static pages: About Kobuleti, Contact, Sitemap
 */

class PageController {
    
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

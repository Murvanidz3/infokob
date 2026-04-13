<?php
/**
 * SEO Helper
 * Manages meta tags, Open Graph, JSON-LD for property listings
 */

class SEO {
    private static $title = '';
    private static $description = '';
    private static $image = '';
    private static $url = '';
    private static $type = 'website';
    private static $jsonLd = null;
    
    /**
     * Set page SEO data
     */
    public static function set($title, $description = '', $image = '') {
        self::$title = $title;
        self::$description = $description;
        self::$image = $image;
        self::$url = currentUrl();
    }
    
    /**
     * Set Open Graph type
     */
    public static function setType($type) {
        self::$type = $type;
    }
    
    /**
     * Set JSON-LD structured data
     */
    public static function setJsonLd($data) {
        self::$jsonLd = $data;
    }
    
    /**
     * Set SEO for a property listing
     */
    public static function setProperty($property, $translation) {
        $title = ($translation['title'] ?? 'Property') . ' - ' . formatPrice($property['price'], $property['currency']) . ' | ' . SITE_NAME;
        $desc = truncate(strip_tags($translation['description'] ?? ''), 160);
        
        self::set($title, $desc);
        self::setType('product');
        
        // Set main image
        if (!empty($property['main_image'])) {
            self::$image = Image::getUrl($property['main_image'], 'medium');
        }
        
        // JSON-LD for Real Estate
        self::$jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $translation['title'] ?? '',
            'description' => $desc,
            'url' => BASE_URL . '/listings/' . $property['slug'],
            'datePosted' => $property['created_at'],
            'image' => self::$image,
            'offers' => [
                '@type' => 'Offer',
                'price' => $property['price'],
                'priceCurrency' => $property['currency'],
            ],
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Kobuleti',
                'addressRegion' => 'Adjara',
                'addressCountry' => 'GE',
                'streetAddress' => $property['address'] ?? '',
            ]
        ];
        
        if (!empty($property['lat']) && !empty($property['lng'])) {
            self::$jsonLd['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => $property['lat'],
                'longitude' => $property['lng'],
            ];
        }
    }
    
    /**
     * Render all SEO meta tags
     */
    public static function render() {
        $title = self::$title ?: SITE_NAME;
        $desc = self::$description ?: 'InfoKobuleti — real estate marketplace for Kobuleti, Georgia';
        $image = self::$image ?: BASE_URL . '/public/assets/img/og-default.jpg';
        $url = self::$url ?: BASE_URL;
        
        $html = '';
        
        // Basic meta
        $html .= '<title>' . e($title) . '</title>' . "\n";
        $html .= '<meta name="description" content="' . e($desc) . '">' . "\n";
        $html .= '<link rel="canonical" href="' . e($url) . '">' . "\n";
        
        // Open Graph
        $html .= '<meta property="og:title" content="' . e($title) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . e($desc) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . e($image) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . e($url) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e(self::$type) . '">' . "\n";
        $html .= '<meta property="og:site_name" content="' . SITE_NAME . '">' . "\n";
        $html .= '<meta property="og:locale" content="' . self::getLocale() . '">' . "\n";
        
        // Twitter Card
        $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '<meta name="twitter:title" content="' . e($title) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . e($desc) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . e($image) . '">' . "\n";
        
        // JSON-LD
        if (self::$jsonLd) {
            $html .= '<script type="application/ld+json">' . "\n";
            $html .= json_encode(self::$jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $html .= "\n</script>" . "\n";
        }
        
        return $html;
    }
    
    /**
     * Get locale for Open Graph
     */
    private static function getLocale() {
        $locales = [
            'ka' => 'ka_GE',
            'ru' => 'ru_RU',
            'en' => 'en_US'
        ];
        return $locales[Language::get()] ?? 'ka_GE';
    }
    
    /**
     * Generate sitemap XML content
     */
    public static function generateSitemap() {
        $db = Database::getInstance();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Static pages
        $staticPages = ['', '/listings', '/kobuleti', '/contact'];
        foreach ($staticPages as $page) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . BASE_URL . $page . '</loc>' . "\n";
            $xml .= '    <changefreq>daily</changefreq>' . "\n";
            $xml .= '    <priority>' . ($page === '' ? '1.0' : '0.8') . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        // Active property listings
        $stmt = $db->query("SELECT slug, updated_at FROM properties WHERE status = 'active' ORDER BY updated_at DESC");
        while ($prop = $stmt->fetch()) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . BASE_URL . '/listings/' . $prop['slug'] . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d', strtotime($prop['updated_at'])) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>weekly</changefreq>' . "\n";
            $xml .= '    <priority>0.7</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
}

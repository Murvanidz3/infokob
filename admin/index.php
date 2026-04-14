<?php
/**
 * Admin Panel — Front Controller
 * Separate entry point for admin routes
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/helpers/Helpers.php';
require_once __DIR__ . '/../app/helpers/Language.php';
require_once __DIR__ . '/../app/helpers/Auth.php';
require_once __DIR__ . '/../app/helpers/Image.php';
require_once __DIR__ . '/../app/helpers/SEO.php';

// Load models
$modelFiles = glob(APP_PATH . '/models/*.php');
foreach ($modelFiles as $modelFile) {
    require_once $modelFile;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

Language::load();

// ─── Auth Check ────────────────────────────────────────────
if (!Auth::isAdmin()) {
    flash('error', 'Access denied');
    header('Location: ' . BASE_URL . '/login');
    exit;
}

// ─── Parse admin route ─────────────────────────────────────
$uri = $_SERVER['REQUEST_URI'];
$basePath = parse_url(ADMIN_URL, PHP_URL_PATH) ?: '/admin';
$adminPath = substr(parse_url($uri, PHP_URL_PATH), strlen($basePath));
$adminPath = '/' . trim($adminPath, '/');
if ($adminPath === '/') $adminPath = '/';

$method = $_SERVER['REQUEST_METHOD'];

// ─── Models ────────────────────────────────────────────────
$propertyModel = new Property();
$userModel = new User();
$settingModel = new Setting();

// ─── Admin Route Dispatch ──────────────────────────────────
switch (true) {
    // Dashboard
    case $adminPath === '/' && $method === 'GET':
        $stats = $propertyModel->adminGetStats();
        $pending = $propertyModel->adminGetAll(['status' => 'pending'], 1);
        $pageTitle = 'Dashboard';
        
        ob_start();
        require __DIR__ . '/views/dashboard.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/views/layout.php';
        break;
    
    // Listings Management
    case $adminPath === '/listings' && $method === 'GET':
        $filters = ['status' => $_GET['status'] ?? '', 'search' => $_GET['search'] ?? ''];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $result = $propertyModel->adminGetAll($filters, $page);
        $listings = $result['data'];
        $pagination = $result['pagination'];
        $pageTitle = 'Listings';
        
        ob_start();
        require __DIR__ . '/views/listings.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/views/layout.php';
        break;

    // Edit listing form
    case preg_match('/^\/listings\/(\d+)\/edit$/', $adminPath, $m) && $method === 'GET':
        $property = $propertyModel->getById((int)$m[1]);
        if (!$property) {
            flash('error', 'Listing not found');
            header('Location: ' . ADMIN_URL . '/listings');
            exit;
        }

        $pageTitle = 'Edit Listing';

        ob_start();
        require __DIR__ . '/views/listing-edit.php';
        $content = ob_get_clean();

        require __DIR__ . '/views/layout.php';
        break;

    // Update listing
    case preg_match('/^\/listings\/(\d+)\/edit$/', $adminPath, $m) && $method === 'POST':
        verify_csrf();
        $id = (int) $m[1];
        $property = $propertyModel->getById($id);
        if (!$property) {
            flash('error', 'Listing not found');
            header('Location: ' . ADMIN_URL . '/listings');
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            flash('error', 'Title is required');
            header('Location: ' . ADMIN_URL . '/listings/' . $id . '/edit');
            exit;
        }

        $featuredUntil = !empty($_POST['is_featured'])
            ? date('Y-m-d H:i:s', strtotime('+30 days'))
            : null;

        $toNullableInt = static function($value): ?int {
            if ($value === '' || $value === null) return null;
            return (int)$value;
        };
        $toNullableFloat = static function($value): ?float {
            if ($value === '' || $value === null) return null;
            return (float)$value;
        };
        $status = $_POST['status'] ?? $property['status'];
        if (!in_array($status, PROPERTY_STATUSES, true)) {
            $status = $property['status'];
        }
        $type = $_POST['type'] ?? $property['type'];
        if (!in_array($type, PROPERTY_TYPES, true)) {
            $type = $property['type'];
        }
        $dealType = $_POST['deal_type'] ?? $property['deal_type'];
        if (!in_array($dealType, DEAL_TYPES, true)) {
            $dealType = $property['deal_type'];
        }
        $currency = $_POST['currency'] ?? $property['currency'];
        if (!in_array($currency, CURRENCIES, true)) {
            $currency = $property['currency'];
        }

        $data = [
            'status'       => $status,
            'type'         => $type,
            'deal_type'    => $dealType,
            'price'        => $toNullableFloat($_POST['price'] ?? $property['price']),
            'currency'     => $currency,
            'price_negotiable' => isset($_POST['price_negotiable']) ? 1 : 0,
            'area_m2'      => $toNullableFloat($_POST['area_m2'] ?? null),
            'rooms'        => $toNullableInt($_POST['rooms'] ?? null),
            'bedrooms'     => $toNullableInt($_POST['bedrooms'] ?? null),
            'bathrooms'    => $toNullableInt($_POST['bathrooms'] ?? null),
            'floors_total' => $toNullableInt($_POST['floors_total'] ?? null),
            'floor_number' => $toNullableInt($_POST['floor_number'] ?? null),
            'has_pool'     => isset($_POST['has_pool']) ? 1 : 0,
            'has_garage'   => isset($_POST['has_garage']) ? 1 : 0,
            'has_balcony'  => isset($_POST['has_balcony']) ? 1 : 0,
            'has_garden'   => isset($_POST['has_garden']) ? 1 : 0,
            'has_furniture'=> isset($_POST['has_furniture']) ? 1 : 0,
            'sea_distance_m' => $toNullableInt($_POST['sea_distance_m'] ?? null),
            'address'      => $_POST['address'] ?? '',
            'district'     => $_POST['district'] ?? '',
            'lat'          => $toNullableFloat($_POST['lat'] ?? null),
            'lng'          => $toNullableFloat($_POST['lng'] ?? null),
            'contact_name' => $_POST['contact_name'] ?? '',
            'contact_phone'=> $_POST['contact_phone'] ?? '',
            'contact_whatsapp' => $_POST['contact_whatsapp'] ?? '',
            'contact_telegram' => $_POST['contact_telegram'] ?? '',
            'contact_email' => $_POST['contact_email'] ?? ($property['contact_email'] ?? ''),
            'admin_note'   => trim($_POST['admin_note'] ?? ''),
            'is_featured'  => isset($_POST['is_featured']) ? 1 : 0,
            'featured_until' => $featuredUntil,
            'translations' => [
                'ka' => [
                    'title' => $title,
                    'description' => trim($_POST['description'] ?? ''),
                ],
            ],
        ];

        try {
            // Handle deleted images first
            if (!empty($_POST['delete_images'])) {
                foreach ((array)$_POST['delete_images'] as $imgId) {
                    $propertyModel->deleteImage((int)$imgId);
                }
            }

            // Handle new image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $files = Image::restructureFiles($_FILES['images']);
                $data['new_images'] = Image::uploadMultiple($files);
            }

            $propertyModel->update($id, $data);
            flash('success', 'Listing updated');
            header('Location: ' . ADMIN_URL . '/listings/' . $id . '/edit');
            exit;
        } catch (Exception $e) {
            error_log('Admin listing update failed for ID ' . $id . ': ' . $e->getMessage());
            $message = 'Failed to update listing';
            if (defined('APP_DEBUG') && APP_DEBUG) {
                $message .= ': ' . $e->getMessage();
            }
            flash('error', $message);
            header('Location: ' . ADMIN_URL . '/listings/' . $id . '/edit');
            exit;
        }
    
    // Approve listing
    case preg_match('/^\/listings\/(\d+)\/approve$/', $adminPath, $m) && $method === 'POST':
        verify_csrf();
        $propertyModel->update($m[1], ['status' => 'active']);
        flash('success', 'Listing approved!');
        header('Location: ' . ADMIN_URL . '/listings');
        exit;
    
    // Reject listing
    case preg_match('/^\/listings\/(\d+)\/reject$/', $adminPath, $m) && $method === 'POST':
        verify_csrf();
        $note = $_POST['admin_note'] ?? '';
        $propertyModel->update($m[1], ['status' => 'rejected', 'admin_note' => $note]);
        flash('success', 'Listing rejected');
        header('Location: ' . ADMIN_URL . '/listings');
        exit;
    
    // Feature listing
    case preg_match('/^\/listings\/(\d+)\/feature$/', $adminPath, $m) && $method === 'POST':
        verify_csrf();
        $days = (int) ($_POST['days'] ?? 30);
        $propertyModel->update($m[1], [
            'is_featured'    => 1,
            'featured_until' => date('Y-m-d H:i:s', strtotime("+{$days} days")),
        ]);
        flash('success', "Featured for {$days} days");
        header('Location: ' . ADMIN_URL . '/listings');
        exit;
    
    // Delete listing
    case preg_match('/^\/listings\/(\d+)\/delete$/', $adminPath, $m) && $method === 'POST':
        verify_csrf();
        $propertyModel->delete($m[1]);
        flash('success', 'Listing deleted');
        header('Location: ' . ADMIN_URL . '/listings');
        exit;
    
    // Users Management
    case $adminPath === '/users' && $method === 'GET':
        $users = $userModel->getAll();
        $pageTitle = 'Users';
        
        ob_start();
        require __DIR__ . '/views/users.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/views/layout.php';
        break;
    
    // Toggle user active
    case preg_match('/^\/users\/(\d+)\/toggle$/', $adminPath, $m) && $method === 'POST':
        verify_csrf();
        $userModel->toggleActive($m[1]);
        flash('success', 'User status updated');
        header('Location: ' . ADMIN_URL . '/users');
        exit;
    
    // Settings
    case $adminPath === '/settings' && $method === 'GET':
        $settings = $settingModel->getAll();
        $pageTitle = 'Settings';
        
        ob_start();
        require __DIR__ . '/views/settings.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/views/layout.php';
        break;
    
    case $adminPath === '/settings' && $method === 'POST':
        verify_csrf();
        unset($_POST['_token']);
        $settingModel->updateBulk($_POST);
        flash('success', 'Settings saved');
        header('Location: ' . ADMIN_URL . '/settings');
        exit;
    
    // Kobuleti Info CMS
    case $adminPath === '/info' && $method === 'GET':
        $db = Database::getInstance();
        $sections = $db->query("SELECT * FROM kobuleti_info ORDER BY id ASC")->fetchAll();
        $pageTitle = 'Kobuleti Info';
        
        ob_start();
        require __DIR__ . '/views/info.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/views/layout.php';
        break;
    
    case $adminPath === '/info' && $method === 'POST':
        verify_csrf();
        $db = Database::getInstance();
        
        if ($_POST['action'] === 'create') {
            $db->prepare("INSERT INTO kobuleti_info (lang, title, content) VALUES (:lang, :title, :content)")
               ->execute([
                   ':lang' => $_POST['lang'] ?? 'ka',
                   ':title' => $_POST['title'] ?? '',
                   ':content' => $_POST['content'] ?? '',
               ]);
        } elseif ($_POST['action'] === 'update' && !empty($_POST['id'])) {
            $db->prepare("UPDATE kobuleti_info SET title = :title, content = :content WHERE id = :id")
               ->execute([
                   ':title' => $_POST['title'] ?? '',
                   ':content' => $_POST['content'] ?? '',
                   ':id' => $_POST['id'],
               ]);
        } elseif ($_POST['action'] === 'delete' && !empty($_POST['id'])) {
            $db->prepare("DELETE FROM kobuleti_info WHERE id = :id")->execute([':id' => $_POST['id']]);
        }
        
        flash('success', 'Content saved');
        header('Location: ' . ADMIN_URL . '/info');
        exit;
    
    default:
        http_response_code(404);
        echo '<h1>404 — Admin page not found</h1>';
        break;
}

ob_end_flush();

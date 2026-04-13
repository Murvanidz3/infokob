<?php
/**
 * User Controller
 * Dashboard, Listings CRUD, Profile
 */

class UserController {
    private Property $propertyModel;
    private User $userModel;
    
    public function __construct() {
        $this->propertyModel = new Property();
        $this->userModel = new User();
    }
    
    public function dashboard() {
        Auth::requireAuth();
        $userId = Auth::id();
        
        $stats = $this->propertyModel->getUserStats($userId);
        $listings = $this->propertyModel->getByUser($userId);
        
        SEO::set(__('dashboard_title') . ' | ' . SITE_NAME);
        
        ob_start();
        require VIEW_PATH . '/user/dashboard.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/user.php';
    }
    
    public function listings() {
        Auth::requireAuth();
        $this->dashboard(); // Same view
    }
    
    public function createForm() {
        Auth::requireAuth();
        
        SEO::set(__('create_title') . ' | ' . SITE_NAME);
        $scripts = ['upload-preview.js'];
        
        ob_start();
        require VIEW_PATH . '/user/create.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/user.php';
    }
    
    public function create() {
        Auth::requireAuth();
        verify_csrf();
        
        $userId = Auth::id();
        $user = Auth::user();
        
        // Build data
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($title)) {
            flash('error', __('error_required'));
            storeOldInput();
            redirect(BASE_URL . '/my/listings/create');
        }
        
        $slug = $this->propertyModel->generateSlug($title);
        
        $data = [
            'user_id'      => $userId,
            'slug'         => $slug,
            'type'         => $_POST['type'] ?? 'apartment',
            'deal_type'    => $_POST['deal_type'] ?? 'sale',
            'price'        => $_POST['price'] ?? 0,
            'currency'     => $_POST['currency'] ?? 'USD',
            'price_negotiable' => isset($_POST['price_negotiable']) ? 1 : 0,
            'area_m2'      => $_POST['area_m2'] ?? null,
            'rooms'        => $_POST['rooms'] ?? null,
            'bedrooms'     => $_POST['bedrooms'] ?? null,
            'bathrooms'    => $_POST['bathrooms'] ?? null,
            'floors_total' => $_POST['floors_total'] ?? null,
            'floor_number' => $_POST['floor_number'] ?? null,
            'has_pool'     => isset($_POST['has_pool']) ? 1 : 0,
            'has_garage'   => isset($_POST['has_garage']) ? 1 : 0,
            'has_balcony'  => isset($_POST['has_balcony']) ? 1 : 0,
            'has_garden'   => isset($_POST['has_garden']) ? 1 : 0,
            'has_furniture'=> isset($_POST['has_furniture']) ? 1 : 0,
            'sea_distance_m' => $_POST['sea_distance_m'] ?? null,
            'address'      => $_POST['address'] ?? '',
            'district'     => $_POST['district'] ?? '',
            'lat'          => $_POST['lat'] ?? null,
            'lng'          => $_POST['lng'] ?? null,
            'contact_name' => $_POST['contact_name'] ?? $user['name'],
            'contact_phone'=> $_POST['contact_phone'] ?? $user['phone'],
            'contact_whatsapp' => $_POST['contact_whatsapp'] ?? '',
            'contact_telegram' => $_POST['contact_telegram'] ?? '',
            'contact_email'=> $_POST['contact_email'] ?? $user['email'],
            'translations' => [
                'ka' => ['title' => $title, 'description' => $description],
            ],
            'images'       => [],
        ];
        
        // Handle file uploads
        if (!empty($_FILES['images']['name'][0])) {
            $files = Image::restructureFiles($_FILES['images']);
            $data['images'] = Image::uploadMultiple($files);
        }
        
        try {
            $this->propertyModel->create($data);
            flash('success', __('listing_submitted'));
            redirect(BASE_URL . '/my/dashboard');
        } catch (Exception $e) {
            flash('error', __('error_generic'));
            storeOldInput();
            redirect(BASE_URL . '/my/listings/create');
        }
    }
    
    public function editForm($id) {
        Auth::requireAuth();
        $property = $this->propertyModel->getById($id);
        
        if (!$property || !Auth::canEdit($property['user_id'])) {
            show404();
            return;
        }
        
        SEO::set(__('edit_title') . ' | ' . SITE_NAME);
        $scripts = ['upload-preview.js'];
        
        ob_start();
        require VIEW_PATH . '/user/edit.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/user.php';
    }
    
    public function update($id) {
        Auth::requireAuth();
        verify_csrf();
        
        $property = $this->propertyModel->getById($id);
        
        if (!$property || !Auth::canEdit($property['user_id'])) {
            show404();
            return;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        $data = [
            'type'         => $_POST['type'] ?? $property['type'],
            'deal_type'    => $_POST['deal_type'] ?? $property['deal_type'],
            'price'        => $_POST['price'] ?? $property['price'],
            'currency'     => $_POST['currency'] ?? $property['currency'],
            'price_negotiable' => isset($_POST['price_negotiable']) ? 1 : 0,
            'area_m2'      => $_POST['area_m2'] ?? null,
            'rooms'        => $_POST['rooms'] ?? null,
            'bedrooms'     => $_POST['bedrooms'] ?? null,
            'bathrooms'    => $_POST['bathrooms'] ?? null,
            'floors_total' => $_POST['floors_total'] ?? null,
            'floor_number' => $_POST['floor_number'] ?? null,
            'has_pool'     => isset($_POST['has_pool']) ? 1 : 0,
            'has_garage'   => isset($_POST['has_garage']) ? 1 : 0,
            'has_balcony'  => isset($_POST['has_balcony']) ? 1 : 0,
            'has_garden'   => isset($_POST['has_garden']) ? 1 : 0,
            'has_furniture'=> isset($_POST['has_furniture']) ? 1 : 0,
            'sea_distance_m' => $_POST['sea_distance_m'] ?? null,
            'address'      => $_POST['address'] ?? '',
            'district'     => $_POST['district'] ?? '',
            'lat'          => $_POST['lat'] ?? null,
            'lng'          => $_POST['lng'] ?? null,
            'contact_name' => $_POST['contact_name'] ?? '',
            'contact_phone'=> $_POST['contact_phone'] ?? '',
            'contact_whatsapp' => $_POST['contact_whatsapp'] ?? '',
            'contact_telegram' => $_POST['contact_telegram'] ?? '',
            'translations' => [
                'ka' => ['title' => $title, 'description' => $description],
            ],
        ];
        
        // Handle new image uploads
        if (!empty($_FILES['images']['name'][0])) {
            $files = Image::restructureFiles($_FILES['images']);
            $data['new_images'] = Image::uploadMultiple($files);
        }
        
        // Handle deleted images
        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imgId) {
                $this->propertyModel->deleteImage((int)$imgId);
            }
        }
        
        try {
            $this->propertyModel->update($id, $data);
            flash('success', __('listing_updated'));
            redirect(BASE_URL . '/my/dashboard');
        } catch (Exception $e) {
            flash('error', __('error_generic'));
            redirect(BASE_URL . '/my/listings/' . $id . '/edit');
        }
    }
    
    public function delete($id) {
        Auth::requireAuth();
        verify_csrf();
        
        $property = $this->propertyModel->getById($id);
        
        if (!$property || !Auth::canEdit($property['user_id'])) {
            show404();
            return;
        }
        
        $this->propertyModel->delete($id);
        flash('success', __('listing_deleted'));
        redirect(BASE_URL . '/my/dashboard');
    }
    
    public function profileForm() {
        Auth::requireAuth();
        $user = $this->userModel->findById(Auth::id());
        
        SEO::set(__('nav_profile') . ' | ' . SITE_NAME);
        
        ob_start();
        require VIEW_PATH . '/user/profile.php';
        $content = ob_get_clean();
        
        require VIEW_PATH . '/layouts/user.php';
    }
    
    public function updateProfile() {
        Auth::requireAuth();
        verify_csrf();
        
        $userId = Auth::id();
        $data = [
            'name'               => trim($_POST['name'] ?? ''),
            'phone'              => trim($_POST['phone'] ?? ''),
            'whatsapp_number'    => trim($_POST['whatsapp_number'] ?? ''),
            'telegram_username'  => trim($_POST['telegram_username'] ?? ''),
        ];
        
        // Handle password change
        if (!empty($_POST['new_password'])) {
            if ($_POST['new_password'] !== $_POST['new_password_confirm']) {
                flash('error', __('error_password_match'));
                redirect(BASE_URL . '/my/profile');
            }
            if (strlen($_POST['new_password']) < 6) {
                flash('error', __('error_password_short'));
                redirect(BASE_URL . '/my/profile');
            }
            $data['password'] = $_POST['new_password'];
        }
        
        $this->userModel->update($userId, $data);
        
        // Refresh session
        Auth::refreshSession([
            'name'     => $data['name'],
            'phone'    => $data['phone'],
            'whatsapp_number' => $data['whatsapp_number'],
            'telegram_username' => $data['telegram_username'],
        ]);
        
        flash('success', __('profile_updated'));
        redirect(BASE_URL . '/my/profile');
    }
}

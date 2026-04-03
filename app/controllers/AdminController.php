<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/models/Property.php';
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Setting.php';

class AdminController
{
    private const SETTINGS_KEYS = [
        'site_name_ka',
        'site_name_ru',
        'site_name_en',
        'contact_phone',
        'contact_email',
        'google_maps_key',
        'facebook_url',
        'instagram_url',
        'featured_price_gel',
        'featured_duration_days',
    ];

    public function dashboard(): void
    {
        Auth::requireAdmin();
        Property::deactivateExpiredFeatured();
        $byStatus = Property::adminCountByStatus();
        $userCount = User::adminCountRegular();
        $meta = ['title' => Helpers::__('admin_dashboard_title') . ' — Admin', 'description' => ''];
        View::render('admin/dashboard', [
            'meta' => $meta,
            'byStatus' => $byStatus,
            'userCount' => $userCount,
        ], 'admin');
    }

    public function propertyIndex(): void
    {
        Auth::requireAdmin();
        Property::deactivateExpiredFeatured();
        $status = (string) ($_GET['status'] ?? 'pending');
        if (!in_array($status, ['all', 'pending', 'active', 'rejected', 'sold', 'archived'], true)) {
            $status = 'pending';
        }
        $q = trim((string) ($_GET['q'] ?? ''));
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 25;
        $filters = ['status' => $status, 'q' => $q];
        $total = Property::adminCountFiltered($filters);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $rows = Property::adminListFiltered($filters, $page, $perPage);
        $meta = ['title' => Helpers::__('admin_properties_title') . ' — Admin', 'description' => ''];
        View::render('admin/properties', [
            'meta' => $meta,
            'rows' => $rows,
            'filters' => $filters,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ], 'admin');
    }

    public function propertyShow(array $params = []): void
    {
        Auth::requireAdmin();
        $id = (int) ($params['id'] ?? 0);
        $prop = Property::getByIdForAdmin($id);
        if ($prop === null) {
            http_response_code(404);
            $meta = ['title' => '404', 'description' => ''];
            View::render('errors/404', ['meta' => $meta], 'admin');
            return;
        }
        $images = Property::getImages($id);
        $translations = Property::getTranslationsMap($id);
        $meta = ['title' => Helpers::__('admin_property_title') . ' #' . $id . ' — Admin', 'description' => ''];
        View::render('admin/property', [
            'meta' => $meta,
            'property' => $prop,
            'images' => $images,
            'translations' => $translations,
        ], 'admin');
    }

    public function propertyApprove(array $params = []): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/properties');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/properties');
        }
        $id = (int) ($params['id'] ?? 0);
        $prop = Property::getByIdForAdmin($id);
        if ($prop === null) {
            Helpers::setFlash('error', Helpers::__('admin_error_not_found'));
            Helpers::redirect(BASE_URL . '/properties');
        }
        $st = (string) ($prop['status'] ?? '');
        if (!in_array($st, ['pending', 'rejected'], true)) {
            Helpers::setFlash('error', Helpers::__('admin_error_approve_status'));
            Helpers::redirect(BASE_URL . '/properties/' . $id);
        }
        if (Property::adminSetStatus($id, 'active', null)) {
            Helpers::setFlash('success', Helpers::__('admin_listing_approved'));
        } else {
            Helpers::setFlash('error', Helpers::__('admin_error_generic'));
        }
        Helpers::redirect(BASE_URL . '/properties/' . $id);
    }

    public function propertyReject(array $params = []): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/properties');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/properties');
        }
        $id = (int) ($params['id'] ?? 0);
        $note = trim((string) ($_POST['admin_note'] ?? ''));
        if (mb_strlen($note) < 3) {
            Helpers::setFlash('error', Helpers::__('admin_error_reject_note'));
            Helpers::redirect(BASE_URL . '/properties/' . $id);
        }
        $prop = Property::getByIdForAdmin($id);
        if ($prop === null) {
            Helpers::setFlash('error', Helpers::__('admin_error_not_found'));
            Helpers::redirect(BASE_URL . '/properties');
        }
        if (($prop['status'] ?? '') !== 'pending') {
            Helpers::setFlash('error', Helpers::__('admin_error_reject_status'));
            Helpers::redirect(BASE_URL . '/properties/' . $id);
        }
        if (Property::adminSetStatus($id, 'rejected', $note)) {
            Helpers::setFlash('success', Helpers::__('admin_listing_rejected'));
        } else {
            Helpers::setFlash('error', Helpers::__('admin_error_generic'));
        }
        Helpers::redirect(BASE_URL . '/properties/' . $id);
    }

    public function propertyToggleFeature(array $params = []): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/properties');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/properties');
        }
        $id = (int) ($params['id'] ?? 0);
        if (Property::getByIdForAdmin($id) === null) {
            Helpers::setFlash('error', Helpers::__('admin_error_not_found'));
            Helpers::redirect(BASE_URL . '/properties');
        }
        if (Property::adminToggleFeatured($id)) {
            Helpers::setFlash('success', Helpers::__('admin_feature_updated'));
        } else {
            Helpers::setFlash('error', Helpers::__('admin_error_feature'));
        }
        Helpers::redirect(BASE_URL . '/properties/' . $id);
    }

    public function users(): void
    {
        Auth::requireAdmin();
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 40;
        $total = User::adminTotalUsers();
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $rows = User::adminList($page, $perPage);
        $meta = ['title' => Helpers::__('admin_users_title') . ' — Admin', 'description' => ''];
        View::render('admin/users', [
            'meta' => $meta,
            'rows' => $rows,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
        ], 'admin');
    }

    public function userToggleActive(array $params = []): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/users');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/users');
        }
        $id = (int) ($params['id'] ?? 0);
        $active = isset($_POST['active']) && (string) $_POST['active'] === '1';
        if (User::adminSetUserActive($id, $active)) {
            Helpers::setFlash('success', Helpers::__('admin_user_updated'));
        } else {
            Helpers::setFlash('error', Helpers::__('admin_error_generic'));
        }
        Helpers::redirect(BASE_URL . '/users');
    }

    public function settingsForm(): void
    {
        Auth::requireAdmin();
        $values = [];
        foreach (self::SETTINGS_KEYS as $key) {
            $values[$key] = Setting::get($key, '');
        }
        $meta = ['title' => Helpers::__('admin_settings_title') . ' — Admin', 'description' => ''];
        View::render('admin/settings', [
            'meta' => $meta,
            'values' => $values,
        ], 'admin');
    }

    public function settingsSave(): void
    {
        Auth::requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/settings');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/settings');
        }
        foreach (self::SETTINGS_KEYS as $key) {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }
            $v = trim((string) $_POST[$key]);
            if ($key === 'featured_duration_days' || $key === 'featured_price_gel') {
                if ($v !== '' && !ctype_digit($v)) {
                    continue;
                }
            }
            Setting::set($key, $v);
        }
        Setting::resetCache();
        Helpers::setFlash('success', Helpers::__('admin_settings_saved'));
        Helpers::redirect(BASE_URL . '/settings');
    }
}

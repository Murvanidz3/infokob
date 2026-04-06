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
        $featuredExpired = Property::countFeaturedExpiredNotCleared();
        Property::deactivateExpiredFeatured();
        $featuredActive = Property::countFeaturedActiveValid();
        $byStatus = Property::adminCountByStatus();
        $userCount = User::adminCountRegular();
        $meta = ['title' => Helpers::__('admin_dashboard_title') . ' — Admin', 'description' => ''];
        View::render('admin/dashboard', [
            'meta' => $meta,
            'byStatus' => $byStatus,
            'userCount' => $userCount,
            'featuredActive' => $featuredActive,
            'featuredExpiredCleared' => $featuredExpired,
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

    public function propertyEditForm(array $params = []): void
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
        $tr = Property::getTranslationsMap($id);
        $images = Property::getImages($id);
        $meta = ['title' => Helpers::__('user_edit_title') . ' #' . $id . ' — Admin', 'description' => ''];
        View::render('admin/property-edit', [
            'meta' => $meta,
            'property' => $prop,
            'translations' => $tr,
            'images' => $images,
        ], 'admin');
    }

    public function propertyUpdate(array $params = []): void
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

        $title = trim((string) ($_POST['title'] ?? ''));
        $desc = trim((string) ($_POST['description'] ?? ''));
        if ($title === '' || mb_strlen($desc) < 50) {
            Helpers::setFlash('error', Helpers::__('user_error_validation'));
            Helpers::redirect(BASE_URL . '/properties/' . $id . '/edit');
        }

        $data = self::buildPayloadFromPost();
        if ($data['contact_phone'] === null || $data['contact_phone'] === '') {
            Helpers::setFlash('error', Helpers::__('user_error_phone'));
            Helpers::redirect(BASE_URL . '/properties/' . $id . '/edit');
        }

        $slug = Property::generateUniqueSlug($title);
        $tr = self::tripleTranslation($title, $desc);
        $newFiles = Helpers::restructureUploadedFiles('images');
        $deleteIds = [];
        if (!empty($_POST['delete_images']) && is_array($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $imgId) {
                $deleteIds[] = (int) $imgId;
            }
        }
        $mainImageId = isset($_POST['main_image_id']) && $_POST['main_image_id'] !== ''
            ? (int) $_POST['main_image_id']
            : null;
        $imageOrder = [];
        if (!empty($_POST['image_order']) && is_array($_POST['image_order'])) {
            foreach ($_POST['image_order'] as $imgId) {
                $imageOrder[] = (int) $imgId;
            }
        }

        $before = count(Property::getImages($id));
        if ($before - count($deleteIds) + count($newFiles) < 1) {
            Helpers::setFlash('error', Helpers::__('user_error_images'));
            Helpers::redirect(BASE_URL . '/properties/' . $id . '/edit');
        }

        try {
            Property::updateForUser(
                $id,
                (int) ($prop['user_id'] ?? 0),
                $slug,
                $data,
                $tr,
                $newFiles,
                $deleteIds,
                $mainImageId,
                $imageOrder
            );
        } catch (Throwable $e) {
            Helpers::setFlash('error', Helpers::__('user_error_generic'));
            Helpers::redirect(BASE_URL . '/properties/' . $id . '/edit');
        }

        Helpers::setFlash('success', Helpers::__('user_listing_updated'));
        Helpers::redirect(BASE_URL . '/properties/' . $id);
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

    public function propertyFeaturedSave(array $params = []): void
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
        $on = isset($_POST['is_featured']) && (string) $_POST['is_featured'] === '1';
        $untilRaw = trim((string) ($_POST['featured_until'] ?? ''));
        $until = $untilRaw !== '' ? $untilRaw : null;
        if (Property::adminSaveFeatured($id, $on, $until)) {
            Helpers::setFlash('success', Helpers::__('admin_feature_updated'));
        } else {
            Helpers::setFlash('error', $on && $until !== null
                ? Helpers::__('admin_featured_invalid_date')
                : Helpers::__('admin_error_feature'));
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

    /**
     * @return array<string, array{title:string,description:string}>
     */
    private static function tripleTranslation(string $title, string $desc): array
    {
        $b = ['title' => $title, 'description' => $desc];
        return ['ka' => $b, 'ru' => $b, 'en' => $b];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildPayloadFromPost(): array
    {
        $type = (string) ($_POST['type'] ?? 'apartment');
        $allowedT = ['apartment', 'house', 'cottage', 'land', 'commercial', 'hotel_room'];
        if (!in_array($type, $allowedT, true)) {
            $type = 'apartment';
        }
        $deal = (string) ($_POST['deal_type'] ?? 'sale');
        if (!in_array($deal, ['sale', 'rent', 'daily_rent'], true)) {
            $deal = 'sale';
        }
        $currency = (string) ($_POST['currency'] ?? 'USD');
        if (!in_array($currency, ['USD', 'GEL', 'EUR'], true)) {
            $currency = 'USD';
        }
        $price = isset($_POST['price']) && $_POST['price'] !== '' ? (float) $_POST['price'] : null;

        return [
            'type' => $type,
            'deal_type' => $deal,
            'price' => $price,
            'currency' => $currency,
            'price_negotiable' => !empty($_POST['price_negotiable']) ? 1 : 0,
            'area_m2' => self::nullableFloat($_POST['area_m2'] ?? null),
            'rooms' => self::nullableInt($_POST['rooms'] ?? null),
            'bedrooms' => self::nullableInt($_POST['bedrooms'] ?? null),
            'bathrooms' => self::nullableInt($_POST['bathrooms'] ?? null),
            'floors_total' => self::nullableInt($_POST['floors_total'] ?? null),
            'floor_number' => self::nullableInt($_POST['floor_number'] ?? null),
            'has_pool' => !empty($_POST['has_pool']) ? 1 : 0,
            'has_garage' => !empty($_POST['has_garage']) ? 1 : 0,
            'has_balcony' => !empty($_POST['has_balcony']) ? 1 : 0,
            'has_garden' => !empty($_POST['has_garden']) ? 1 : 0,
            'sea_distance_m' => self::nullableInt($_POST['sea_distance_m'] ?? null),
            'address' => trim((string) ($_POST['address'] ?? '')) ?: null,
            'district' => trim((string) ($_POST['district'] ?? '')) ?: null,
            'lat' => self::nullableFloat($_POST['lat'] ?? null),
            'lng' => self::nullableFloat($_POST['lng'] ?? null),
            'contact_name' => trim((string) ($_POST['contact_name'] ?? '')) ?: null,
            'contact_phone' => trim((string) ($_POST['contact_phone'] ?? '')) ?: null,
            'contact_whatsapp' => trim((string) ($_POST['contact_whatsapp'] ?? '')) ?: null,
            'contact_email' => trim((string) ($_POST['contact_email'] ?? '')) ?: null,
            'contact_telegram' => trim((string) ($_POST['contact_telegram'] ?? '')) ?: null,
        ];
    }

    private static function nullableInt(mixed $v): ?int
    {
        if ($v === null || $v === '') {
            return null;
        }
        return (int) $v;
    }

    private static function nullableFloat(mixed $v): ?float
    {
        if ($v === null || $v === '') {
            return null;
        }
        return (float) $v;
    }
}

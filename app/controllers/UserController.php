<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/models/User.php';
require_once dirname(__DIR__, 2) . '/app/models/Property.php';
require_once dirname(__DIR__, 2) . '/app/models/Setting.php';

class UserController
{
    public function dashboard(array $params = []): void
    {
        Auth::requireAuth();
        $uid = (int) Auth::userId();
        $lang = Language::get();
        $stats = Property::getDashboardStats($uid);
        $recent = array_slice(Property::listForUser($uid, $lang), 0, 8);
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_my_dashboard') . ' — InfoKobuleti';
        View::render('user/dashboard', [
            'meta' => $meta,
            'stats' => $stats,
            'recent' => $recent,
        ], 'user');
    }

    public function listings(array $params = []): void
    {
        Auth::requireAuth();
        $uid = (int) Auth::userId();
        $lang = Language::get();
        $rows = Property::listForUser($uid, $lang);
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('user_nav_listings') . ' — InfoKobuleti';
        View::render('user/listings', [
            'meta' => $meta,
            'listings' => $rows,
            'featuredPriceGel' => Setting::get('featured_price_gel', '25'),
            'featuredDurationDays' => Setting::get('featured_duration_days', '30'),
            'contactPhone' => Setting::get('contact_phone', ''),
        ], 'user');
    }

    public function createForm(array $params = []): void
    {
        Auth::requireAuth();
        $user = User::findById((int) Auth::userId());
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_add_listing') . ' — InfoKobuleti';
        View::render('user/create', [
            'meta' => $meta,
            'user' => $user,
            'property' => null,
            'translations' => [],
            'images' => [],
            'edit' => false,
        ], 'user');
    }

    public function create(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/my/listings/create');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/my/listings/create');
        }
        $uid = (int) Auth::userId();
        $title = trim((string) ($_POST['title'] ?? ''));
        $desc = trim((string) ($_POST['description'] ?? ''));
        if ($title === '' || mb_strlen($desc) < 50) {
            Helpers::setFlash('error', Helpers::__('user_error_validation'));
            Helpers::redirect(BASE_URL . '/my/listings/create');
        }
        $files = Helpers::restructureUploadedFiles('images');
        if (count($files) < 1) {
            Helpers::setFlash('error', Helpers::__('user_error_images'));
            Helpers::redirect(BASE_URL . '/my/listings/create');
        }
        $data = self::buildPayloadFromPost();
        if ($data['contact_phone'] === null || $data['contact_phone'] === '') {
            Helpers::setFlash('error', Helpers::__('user_error_phone'));
            Helpers::redirect(BASE_URL . '/my/listings/create');
        }
        $slug = Property::generateUniqueSlug($title);
        $tr = self::tripleTranslation($title, $desc);
        try {
            Property::createForUser($uid, $slug, $data, $tr, $files);
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'no_images') {
                Helpers::setFlash('error', Helpers::__('user_error_images'));
            } else {
                Helpers::setFlash('error', Helpers::__('user_error_generic'));
            }
            Helpers::redirect(BASE_URL . '/my/listings/create');
        }
        Helpers::setFlash('success', Helpers::__('listing_submitted'));
        Helpers::redirect(BASE_URL . '/my/listings');
    }

    public function editForm(array $params = []): void
    {
        Auth::requireAuth();
        $id = (int) ($params['id'] ?? 0);
        $uid = (int) Auth::userId();
        $prop = Property::getOwnedById($id, $uid);
        if ($prop === null) {
            http_response_code(404);
            View::render('errors/404', ['meta' => SEO::defaultMeta()], 'main');
            return;
        }
        $tr = Property::getTranslationsMap($id);
        $images = Property::getImages($id);
        $user = User::findById($uid);
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('user_edit_title') . ' — InfoKobuleti';
        View::render('user/create', [
            'meta' => $meta,
            'user' => $user,
            'property' => $prop,
            'translations' => $tr,
            'images' => $images,
            'edit' => true,
        ], 'user');
    }

    public function update(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        $id = (int) ($params['id'] ?? 0);
        $uid = (int) Auth::userId();
        $prop = Property::getOwnedById($id, $uid);
        if ($prop === null) {
            http_response_code(404);
            exit;
        }
        $title = trim((string) ($_POST['title'] ?? ''));
        $desc = trim((string) ($_POST['description'] ?? ''));
        if ($title === '' || mb_strlen($desc) < 50) {
            Helpers::setFlash('error', Helpers::__('user_error_validation'));
            Helpers::redirect(BASE_URL . '/my/listings/' . $id . '/edit');
        }
        $data = self::buildPayloadFromPost();
        if ($data['contact_phone'] === null || $data['contact_phone'] === '') {
            Helpers::setFlash('error', Helpers::__('user_error_phone'));
            Helpers::redirect(BASE_URL . '/my/listings/' . $id . '/edit');
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
        $before = count(Property::getImages($id));
        if ($before - count($deleteIds) + count($newFiles) < 1) {
            Helpers::setFlash('error', Helpers::__('user_error_images'));
            Helpers::redirect(BASE_URL . '/my/listings/' . $id . '/edit');
        }
        try {
            Property::updateForUser($id, $uid, $slug, $data, $tr, $newFiles, $deleteIds);
        } catch (Throwable $e) {
            Helpers::setFlash('error', Helpers::__('user_error_generic'));
            Helpers::redirect(BASE_URL . '/my/listings/' . $id . '/edit');
        }
        Helpers::setFlash('success', Helpers::__('user_listing_updated'));
        Helpers::redirect(BASE_URL . '/my/listings');
    }

    public function delete(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        $id = (int) ($params['id'] ?? 0);
        $uid = (int) Auth::userId();
        if (Property::deleteForUser($id, $uid)) {
            Helpers::setFlash('success', Helpers::__('user_listing_deleted'));
        } else {
            Helpers::setFlash('error', Helpers::__('user_error_generic'));
        }
        Helpers::redirect(BASE_URL . '/my/listings');
    }

    public function markSold(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        $id = (int) ($params['id'] ?? 0);
        $uid = (int) Auth::userId();
        if (Property::setStatusForUser($id, $uid, 'sold')) {
            Helpers::setFlash('success', Helpers::__('user_marked_sold'));
        }
        Helpers::redirect(BASE_URL . '/my/listings');
    }

    public function archive(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/my/listings');
        }
        $id = (int) ($params['id'] ?? 0);
        $uid = (int) Auth::userId();
        if (Property::setStatusForUser($id, $uid, 'archived')) {
            Helpers::setFlash('success', Helpers::__('user_marked_archived'));
        }
        Helpers::redirect(BASE_URL . '/my/listings');
    }

    public function profileForm(array $params = []): void
    {
        Auth::requireAuth();
        $user = User::findById((int) Auth::userId());
        if ($user === null) {
            Auth::logout();
            Helpers::redirect(BASE_URL . '/login');
        }
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('profile_title') . ' — InfoKobuleti';
        View::render('user/profile', ['meta' => $meta, 'user' => $user], 'user');
    }

    public function updateProfile(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/my/profile');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/my/profile');
        }
        $uid = (int) Auth::userId();
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $wa = trim((string) ($_POST['whatsapp_number'] ?? ''));
        $tg = trim((string) ($_POST['telegram_username'] ?? ''));
        $newPass = (string) ($_POST['new_password'] ?? '');
        $newPass2 = (string) ($_POST['new_password_confirm'] ?? '');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Helpers::setFlash('error', Helpers::__('user_error_validation'));
            Helpers::redirect(BASE_URL . '/my/profile');
        }
        $other = User::findByEmail($email);
        if ($other !== null && (int) $other['id'] !== $uid) {
            Helpers::setFlash('error', Helpers::__('auth_error_email_taken'));
            Helpers::redirect(BASE_URL . '/my/profile');
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'whatsapp_number' => $wa,
            'telegram_username' => $tg,
        ];
        if ($newPass !== '') {
            if (strlen($newPass) < 8 || !hash_equals($newPass, $newPass2)) {
                Helpers::setFlash('error', Helpers::__('auth_error_password_match'));
                Helpers::redirect(BASE_URL . '/my/profile');
            }
            $data['password'] = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        }
        User::updateProfile($uid, $data);
        $fresh = User::findById($uid);
        if ($fresh !== null) {
            Auth::loginWithUser($fresh);
        }
        Helpers::setFlash('success', Helpers::__('profile_saved'));
        Helpers::redirect(BASE_URL . '/my/profile');
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

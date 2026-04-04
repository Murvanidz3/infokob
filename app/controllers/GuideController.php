<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__) . '/helpers/GuideLocale.php';
require_once dirname(__DIR__) . '/models/GuideUserContent.php';

class GuideController extends BaseController
{
    /** @return array<string, mixed> */
    private static function dataset(): array
    {
        $path = BASE_PATH . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'guide' . DIRECTORY_SEPARATOR . 'pages.php';
        if (!is_readable($path)) {
            return [];
        }
        $data = require $path;
        return is_array($data) ? $data : [];
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    private static function localizeCards(array $items, string $lang): array
    {
        $out = [];
        foreach ($items as $it) {
            $row = [
                'image' => $it['image'] ?? '',
                'title' => GuideLocale::t($it['title'] ?? '', $lang),
                'lines' => GuideLocale::lines($it['lines'] ?? [], $lang),
            ];
            $out[] = $row;
        }
        return $out;
    }

    /**
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    private static function localizeBankBlocks(array $items, string $lang): array
    {
        $out = [];
        foreach ($items as $it) {
            $out[] = [
                'title' => GuideLocale::t($it['title'] ?? '', $lang),
                'lines' => GuideLocale::lines($it['lines'] ?? [], $lang),
            ];
        }
        return $out;
    }

    public function hotels(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_hotels_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_hotels_meta');
        $items = self::localizeCards(self::dataset()['hotels'] ?? [], $lang);
        View::render('pages/guide-cards', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_hotels_h1'),
            'leadKey' => 'guide_hotels_lead',
            'items' => $items,
        ]);
    }

    public function restaurants(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_restaurants_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_restaurants_meta');
        $items = self::localizeCards(self::dataset()['restaurants'] ?? [], $lang);
        View::render('pages/guide-cards', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_restaurants_h1'),
            'leadKey' => 'guide_restaurants_lead',
            'items' => $items,
        ]);
    }

    public function sights(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_sights_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_sights_meta');
        $items = self::localizeCards(self::dataset()['sights'] ?? [], $lang);
        View::render('pages/guide-cards', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_sights_h1'),
            'leadKey' => 'guide_sights_lead',
            'items' => $items,
        ]);
    }

    public function events(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_events_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_events_meta');
        $items = self::localizeCards(self::dataset()['events'] ?? [], $lang);
        View::render('pages/guide-cards', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_events_h1'),
            'leadKey' => 'guide_events_lead',
            'items' => $items,
        ]);
    }

    public function transport(array $params = []): void
    {
        $lang = Language::get();
        $data = self::dataset();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_transport_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_transport_meta');

        $marsh = [];
        foreach ($data['transport_marshrutka'] ?? [] as $block) {
            $marsh[] = [
                'route' => GuideLocale::t($block['route'] ?? '', $lang),
                'times' => $block['rows'][0] ?? [],
                'note' => GuideLocale::t($block['note'] ?? '', $lang),
            ];
        }

        $trainIntro = GuideLocale::t($data['transport_train']['intro'] ?? '', $lang);
        $trainRows = [];
        foreach ($data['transport_train']['rows'] ?? [] as $r) {
            $trainRows[] = [
                'dep' => $r['dep'] ?? '',
                'arr' => $r['arr'] ?? '',
                'train' => $r['train'] ?? '',
                'note' => GuideLocale::t($r['note'] ?? '', $lang),
            ];
        }

        View::render('pages/guide-transport', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_transport_h1'),
            'leadKey' => 'guide_transport_lead',
            'marsh' => $marsh,
            'trainIntro' => $trainIntro,
            'trainRows' => $trainRows,
        ]);
    }

    public function banks(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_banks_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_banks_meta');
        $blocks = self::localizeBankBlocks(self::dataset()['banks'] ?? [], $lang);
        View::render('pages/guide-banks', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_banks_h1'),
            'leadKey' => 'guide_banks_lead',
            'blocks' => $blocks,
        ]);
    }

    public function beauty(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_beauty_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_beauty_meta');
        $items = self::localizeCards(self::dataset()['beauty'] ?? [], $lang);
        View::render('pages/guide-cards', [
            'meta' => $meta,
            'pageH1' => Helpers::__('guide_beauty_h1'),
            'leadKey' => 'guide_beauty_lead',
            'items' => $items,
        ]);
    }

    public function classifieds(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_classifieds_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_classifieds_meta');

        $user = GuideUserContent::readList('classifieds');
        $demo = self::dataset()['seekers_demo'] ?? [];
        $merged = array_merge($user, $demo);
        usort($merged, static function (array $a, array $b): int {
            $ta = strtotime((string) ($a['created_at'] ?? '1970-01-01'));
            $tb = strtotime((string) ($b['created_at'] ?? '1970-01-01'));
            return $tb <=> $ta;
        });

        $rows = [];
        foreach ($merged as $row) {
            $role = $row['role'] ?? '';
            $body = $row['body'] ?? '';
            $rows[] = [
                'role' => is_array($role) ? GuideLocale::t($role, $lang) : (string) $role,
                'body' => is_array($body) ? GuideLocale::t($body, $lang) : (string) $body,
                'phone' => (string) ($row['phone'] ?? ''),
                'name' => (string) ($row['name'] ?? ''),
                'created_at' => (string) ($row['created_at'] ?? ''),
                'from_site' => isset($row['id']) && is_numeric($row['id']),
            ];
        }

        View::render('pages/guide-classifieds', [
            'meta' => $meta,
            'rows' => $rows,
        ]);
    }

    public function classifiedsStore(array $params = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/classifieds');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }
        if (Auth::rateLimitHit('guide_classifieds')) {
            Helpers::setFlash('error', Helpers::__('guide_rate_limit'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }

        $role = trim((string) ($_POST['role'] ?? ''));
        $body = trim((string) ($_POST['body'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));

        if ($name === '' || mb_strlen($name) < 2) {
            Helpers::setFlash('error', Helpers::__('guide_classifieds_error_name'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }
        if ($role === '' || mb_strlen($role) < 3) {
            Helpers::setFlash('error', Helpers::__('guide_classifieds_error_role'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }
        if (mb_strlen($body) < 20) {
            Helpers::setFlash('error', Helpers::__('guide_classifieds_error_body'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }
        if ($phone === '' || mb_strlen($phone) < 8) {
            Helpers::setFlash('error', Helpers::__('guide_classifieds_error_phone'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }

        $row = [
            'role' => $role,
            'body' => $body,
            'phone' => $phone,
            'name' => $name,
            'created_at' => date('c'),
        ];
        if (Auth::isLoggedIn()) {
            $row['user_id'] = Auth::userId();
        }
        $ok = GuideUserContent::append('classifieds', $row);
        if (!$ok) {
            Helpers::setFlash('error', Helpers::__('guide_save_error'));
            Helpers::redirect(BASE_URL . '/classifieds');
        }
        Helpers::setFlash('success', Helpers::__('guide_classifieds_success'));
        Helpers::redirect(BASE_URL . '/classifieds');
    }

    public function vacancies(array $params = []): void
    {
        $lang = Language::get();
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('guide_vacancies_title') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('guide_vacancies_meta');

        $user = GuideUserContent::readList('vacancies');
        $demo = self::dataset()['vacancies_demo'] ?? [];
        $merged = array_merge($user, $demo);
        usort($merged, static function (array $a, array $b): int {
            $ta = strtotime((string) ($a['created_at'] ?? '1970-01-01'));
            $tb = strtotime((string) ($b['created_at'] ?? '1970-01-01'));
            return $tb <=> $ta;
        });

        $rows = [];
        foreach ($merged as $row) {
            $title = $row['title'] ?? '';
            $body = $row['body'] ?? '';
            $rows[] = [
                'company' => (string) ($row['company'] ?? ''),
                'title' => is_array($title) ? GuideLocale::t($title, $lang) : (string) $title,
                'body' => is_array($body) ? GuideLocale::t($body, $lang) : (string) $body,
                'phone' => (string) ($row['phone'] ?? ''),
                'created_at' => (string) ($row['created_at'] ?? ''),
                'from_site' => isset($row['id']) && is_numeric($row['id']),
            ];
        }

        View::render('pages/guide-vacancies', [
            'meta' => $meta,
            'rows' => $rows,
            'isAuth' => Auth::isLoggedIn(),
        ]);
    }

    public function vacanciesStore(array $params = []): void
    {
        Auth::requireAuth();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/vacancies');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }
        if (Auth::rateLimitHit('guide_vacancies')) {
            Helpers::setFlash('error', Helpers::__('guide_rate_limit'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }

        $company = trim((string) ($_POST['company'] ?? ''));
        $title = trim((string) ($_POST['title'] ?? ''));
        $body = trim((string) ($_POST['body'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));

        if ($company === '' || mb_strlen($company) < 2) {
            Helpers::setFlash('error', Helpers::__('guide_vacancies_error_company'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }
        if ($title === '' || mb_strlen($title) < 3) {
            Helpers::setFlash('error', Helpers::__('guide_vacancies_error_title'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }
        if (mb_strlen($body) < 40) {
            Helpers::setFlash('error', Helpers::__('guide_vacancies_error_body'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }
        if ($phone === '' || mb_strlen($phone) < 8) {
            Helpers::setFlash('error', Helpers::__('guide_vacancies_error_phone'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }

        $ok = GuideUserContent::append('vacancies', [
            'user_id' => Auth::userId(),
            'company' => $company,
            'title' => $title,
            'body' => $body,
            'phone' => $phone,
            'created_at' => date('c'),
        ]);
        if (!$ok) {
            Helpers::setFlash('error', Helpers::__('guide_save_error'));
            Helpers::redirect(BASE_URL . '/vacancies');
        }
        Helpers::setFlash('success', Helpers::__('guide_vacancies_success'));
        Helpers::redirect(BASE_URL . '/vacancies');
    }
}

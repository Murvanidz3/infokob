<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once dirname(__DIR__, 2) . '/app/models/KobuletiInfo.php';
require_once dirname(__DIR__, 2) . '/app/models/Setting.php';

class PageController extends BaseController
{
    public function kobuleti(array $params = []): void
    {
        $lang = Language::get();
        $sections = KobuletiInfo::getByLang($lang);
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_kobuleti') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('kob_page_meta');

        View::render('pages/about-kobuleti', [
            'meta' => $meta,
            'sections' => $sections,
        ]);
    }

    public function contact(array $params = []): void
    {
        $meta = SEO::defaultMeta();
        $meta['title'] = Helpers::__('nav_contact') . ' — InfoKobuleti';
        $meta['description'] = Helpers::__('contact_meta');

        View::render('pages/contact', [
            'meta' => $meta,
        ]);
    }

    public function sendContact(array $params = []): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Helpers::redirect(BASE_URL . '/contact');
        }
        if (!Helpers::verifyCsrf($_POST['csrf'] ?? null)) {
            Helpers::setFlash('error', Helpers::__('error_csrf'));
            Helpers::redirect(BASE_URL . '/contact');
        }
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($message) < 10) {
            Helpers::setFlash('error', Helpers::__('contact_error_validation'));
            Helpers::redirect(BASE_URL . '/contact');
        }
        $to = Setting::get('contact_email', 'info@infokobuleti.com');
        $subject = '[InfoKobuleti] Contact: ' . $name;
        $body = "Name: $name\nEmail: $email\n\n" . $message;
        $headers = [
            'From: noreply@infokobuleti.com',
            'Reply-To: ' . $email,
            'Content-Type: text/plain; charset=UTF-8',
        ];
        @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
        Helpers::setFlash('success', Helpers::__('contact_success'));
        Helpers::redirect(BASE_URL . '/contact');
    }
}

<?php
declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

final class Mailer
{
    /**
     * Envía un correo HTML vía SMTP (PHPMailer).
     * Si falla, guarda en storage/mail.log y storage/mail_debug.log.
     */
    public static function send(string $to, string $subject, string $html): void
    {
        // 1) Cargar config desde la constante CONFIG definida en public/index.php.
        //    Si por alguna razón no existe (CLI/test), hacer fallback a los archivos.
        $cfg = [];
        if (\defined('CONFIG')) {
            $root = \constant('CONFIG');
            $cfg  = is_array($root['mail'] ?? null) ? $root['mail'] : [];
        } else {
            $basePath = \defined('BASE_PATH') ? \constant('BASE_PATH') : dirname(__DIR__, 2);
            $base  = is_file($basePath.'/app/config/config.php') ? include $basePath.'/app/config/config.php' : [];
            $local = is_file($basePath.'/app/config/config.local.php') ? include $basePath.'/app/config/config.local.php' : [];
            $merged = array_replace_recursive($base, $local);
            $cfg = $merged['mail'] ?? [];
        }

        $fromEmail = $cfg['from_email'] ?? 'no-reply@bibliopoas.local';
        $fromName  = $cfg['from_name']  ?? 'BiblioPoás';

        // Helpers de logging
        $ensureStorage = function(): string {
            $dir = __DIR__ . '/../../storage';
            if (!is_dir($dir)) @mkdir($dir, 0777, true);
            return $dir;
        };
        $fallback = function (string $reason) use ($to, $subject, $html, $ensureStorage): void {
            $dir = $ensureStorage();
            @file_put_contents(
                $dir . '/mail.log',
                "----\n".date('Y-m-d H:i:s')."\nFAIL: $reason\nTO: $to\nSUBJECT: $subject\n$html\n\n",
                FILE_APPEND
            );
        };

        try {
            if (($cfg['driver'] ?? 'smtp') !== 'smtp') {
                $fallback('driver not smtp');
                return;
            }

            $mail = new PHPMailer(true);

            // === DEBUG hacia archivo ===
            $mail->SMTPDebug  = 2; // 0=off, 2=verbose
            $mail->Debugoutput = function($str, $level) use ($ensureStorage) {
                $dir = $ensureStorage();
                @file_put_contents($dir.'/mail_debug.log', date('H:i:s')." [$level] $str\n", FILE_APPEND);
            };

            // === SMTP base ===
            $mail->isSMTP();
            $mail->Host       = $cfg['host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['username'] ?? '';
            $mail->Password   = $cfg['password'] ?? '';
            $mail->Port       = (int)($cfg['port'] ?? 587);

            // === Cifrado ===
            $enc = strtolower((string)($cfg['encryption'] ?? 'tls'));
            if ($enc === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // 465
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 587
            }

            // Opcional: útil para ambientes locales con TLS estricto
            if (!empty($cfg['allow_self_signed'])) {
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ];
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body    = $html;
            $mail->AltBody = self::toAltText($html);

            $mail->send();
        } catch (MailException $e) {
            $fallback('PHPMailer: ' . $e->getMessage());
        } catch (\Throwable $e) {
            $fallback('Generic: ' . $e->getMessage());
        }
    }

    public static function buildTempPasswordEmail(string $nombre, string $tempPass): string
    {
        return '
  <div style="font-family:Inter,Arial,Helvetica,sans-serif;max-width:560px;margin:0 auto;padding:24px;background:#ffffff;border:1px solid #eee;border-radius:12px">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px">
      <div style="width:36px;height:36px;border-radius:50%;background:#ec6d13"></div>
      <strong style="font-size:18px;color:#333">BiblioPoás</strong>
    </div>

    <h2 style="margin:0 0 8px 0;color:#333">Solicitud para restablecer tu contraseña</h2>
    <p style="margin:0 0 16px 0;color:#555">Hola '.htmlspecialchars($nombre).', recibimos una solicitud para restablecer tu contraseña.</p>

    <div style="margin:20px 0;padding:24px;border-radius:12px;background:#fcfaf8;border:1px dashed #ec6d13;text-align:center">
      <div style="color:#9a6c4c;margin-bottom:6px">Tu contraseña temporal es:</div>
      <div style="font-size:32px;font-weight:800;letter-spacing:2px;color:#ec6d13">'.$tempPass.'</div>
      <div style="margin-top:8px;color:#888;font-size:12px">Válida por un único acceso. Deberás cambiarla al iniciar sesión.</div>
    </div>

    <p style="margin:0 0 16px 0;color:#555">Ingresa con esa contraseña temporal y te llevaremos a la pantalla para definir una nueva contraseña.</p>

    <a href="'.self::appUrl('/login').'" style="display:inline-block;margin-top:6px;padding:12px 18px;background:#ec6d13;color:#fff;text-decoration:none;border-radius:10px;font-weight:700">
      Ir a iniciar sesión
    </a>

    <p style="margin:24px 0 0 0;color:#999;font-size:12px">Si no solicitaste este cambio, puedes ignorar este correo.</p>
  </div>';
    }

    private static function toAltText(string $html): string
    {
        $text = strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $html));
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    private static function appUrl(string $path): string
    {
        $host = ($_SERVER['HTTP_HOST'] ?? 'localhost:8000');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        return $scheme . '://' . $host . $path;
    }
}

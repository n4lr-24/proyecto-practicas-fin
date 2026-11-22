<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Código de verificación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8fafc; padding: 24px; color: #1e293b;">
    <div style="max-width: 440px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px;">
        <h2 style="margin-top: 0; color: #0f172a;">Código de verificación</h2>
        <p>Hola <strong>{{ $usuario->name }}</strong>, usa este código para iniciar sesión:</p>

        <div style="text-align: center; margin: 20px 0; padding: 14px; background: #f1f5f9; border-radius: 8px; font-size: 28px; font-weight: bold; letter-spacing: 6px; color: #0f172a;">
            {{ $codigo }}
        </div>

        <p style="font-size: 13px; color: #475569; margin-bottom: 0;">
            Válido por <strong>5 minutos</strong>.
        </p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invitation EasyColoc</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px;">

    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px;">

        <h2 style="color: #1f2937; margin-bottom: 20px;">
            Invitation à rejoindre une colocation
        </h2>

        <p style="color: #374151; line-height: 1.6;">
            Bonjour,
        </p>

        <p style="color: #374151; line-height: 1.6;">
            Vous avez été invité à rejoindre une colocation sur <strong>EasyColoc</strong>.
        </p>

        <p style="color: #374151; line-height: 1.6;">
            EasyColoc vous permet de gérer facilement les dépenses partagées,
            suivre les soldes entre membres et éviter les malentendus financiers.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('invitations.show', $invitation->token) }}"
               style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Rejoindre la colocation
            </a>
        </div>

        <p style="color: #6b7280; font-size: 14px;">
            Si vous ne souhaitez pas rejoindre cette colocation, vous pouvez ignorer cet email.
        </p>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">

        <p style="color: #9ca3af; font-size: 12px;">
            Cet email a été envoyé automatiquement par EasyColoc.<br>
            Merci de ne pas y répondre directement.
        </p>

    </div>

</body>
</html>
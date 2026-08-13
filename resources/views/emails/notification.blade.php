<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 24px;">
        <h2 style="color: #333;">{{ $notification->title }}</h2>
        <p style="color: #555; font-size: 15px; line-height: 1.5;">
            {{ $notification->body }}
        </p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="color: #999; font-size: 12px;">
            Planora — Smart Study Planner
        </p>
    </div>
</body>
</html>
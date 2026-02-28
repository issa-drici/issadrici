<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation confirmée</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .success-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }
        .success-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #1f2937;
            margin-bottom: 16px;
        }
        p {
            color: #6b7280;
            margin-bottom: 8px;
        }
        .date-info {
            background: #f3f4f6;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✅</div>
        <h1>Réservation confirmée !</h1>
        <p>Votre réservation a été enregistrée avec succès.</p>
        
        <div class="date-info">
            <p style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">Date et heure du call :</p>
            <p style="font-size: 18px; color: #667eea;">{{ $booking->date_choisie->format('d/m/Y à H:i') }}</p>
        </div>
        
        <p style="margin-top: 20px;">Vous recevrez un email de confirmation avec les détails du call.</p>
    </div>
</body>
</html>

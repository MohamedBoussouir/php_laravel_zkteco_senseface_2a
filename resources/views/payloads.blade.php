<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payloads Logs</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; }
        .card { background: white; border-radius: 8px; padding: 15px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .badge { background: #3b82f6; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

    <h2>مراقبة طلبات جهاز البصمة (Payloads)</h2>

    @foreach($payloads as $payload)
        <div class="card">
            <div class="header">
                <strong>الجهاز: {{ $payload->device_sn ?? 'غير معروف' }}</strong>
                <span class="badge">{{ $payload->endpoint }}</span>
                <small>{{ $payload->created_at->diffForHumans() }}</small>
            </div>
            
            <!-- هنا نستخدم  لعرض المحتوى بنفس ستايل dd() -->
            <div>
                @dump($payload->content)
            </div>
        </div>
    @endforeach

    @if($payloads->isEmpty())
        <p>لا يوجد أي طلبات محفوظة حتى الآن.</p>
    @endif

</body>
</html>
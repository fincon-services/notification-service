<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Devices</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --success: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.3) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.3) 0, transparent 50%);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 5rem;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            padding: 0 1.5rem;
        }

        .nav-links {
            margin-bottom: 2rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.2s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--text-main);
            background: rgba(255, 255, 255, 0.15);
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
        }

        .header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 2.25rem;
            font-weight: 700;
            background: linear-gradient(to right, #60a5fa, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            margin-top: 0.5rem;
            color: var(--text-muted);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--glass-border);
        }

        th {
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }

        .badge-platform {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .token-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-family: monospace;
            color: #94a3b8;
            font-size: 0.85rem;
        }
        
        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 3rem 0;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="nav-links">
            <a href="{{ route('projects.index') }}">Projects</a>
            <a href="{{ route('devices.index') }}" class="active">Devices</a>
        </div>

        <div class="header">
            <h1>Registered Devices</h1>
            <p>View all devices registered for push notifications</p>
        </div>

        <div class="glass-panel" style="overflow-x: auto;">
            @if($devices->count() > 0)
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Project</th>
                            <th>User ID</th>
                            <th>Platform</th>
                            <th>Token (FCM)</th>
                            <th>Registered At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                            <tr>
                                <td>{{ $device->id }}</td>
                                <td>
                                    <span style="font-weight: 500;">{{ $device->project->name ?? 'Unknown' }}</span>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">ID: {{ $device->project_id }}</div>
                                </td>
                                <td>{{ $device->user_id }}</td>
                                <td>
                                    <span class="badge-platform">{{ $device->platform }}</span>
                                </td>
                                <td class="token-cell" title="{{ $device->token }}">{{ $device->token }}</td>
                                <td style="font-size: 0.85rem; color: var(--text-muted);">
                                    {{ $device->updated_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    No devices registered yet.
                </div>
            @endif
        </div>

    </div>

</body>
</html>

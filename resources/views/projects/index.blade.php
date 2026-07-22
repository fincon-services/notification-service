<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
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
            max-width: 800px;
            padding: 0 1.5rem;
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

        .form-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .input-text {
            flex: 1;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-text:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: var(--success);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .projects-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .project-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: rgba(0, 0, 0, 0.15);
            border: 1px solid var(--glass-border);
            border-radius: 0.75rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }

        .project-item:hover {
            background: rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.01);
        }

        .project-name {
            font-weight: 500;
            font-size: 1.1rem;
        }

        .project-id-badge {
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-family: monospace;
            border: 1px solid var(--glass-border);
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            padding: 2rem 0;
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
    </style>
</head>
<body>

    <div class="container">
        
        <div class="nav-links">
            <a href="{{ route('projects.index') }}" class="active">Projects</a>
            <a href="{{ route('devices.index') }}">Devices</a>
        </div>

        <div class="header">
            <h1>Notification Projects</h1>
            <p>Create and manage projects to use with the Notification API</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="glass-panel">
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="text" name="name" class="input-text" placeholder="Enter new project name" required>
                    <button type="submit" class="btn">Create Project</button>
                </div>
                @error('name')
                    <div style="color: #ef4444; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</div>
                @enderror
            </form>
        </div>

        <div class="glass-panel">
            <h2 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Your Projects</h2>
            
            @if($projects->count() > 0)
                <ul class="projects-list">
                    @foreach($projects as $project)
                        <li class="project-item">
                            <div class="project-name">{{ $project->name }}</div>
                            <div class="project-id-badge">ID: {{ $project->id }}</div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="empty-state">
                    No projects found. Create one to get started!
                </div>
            @endif
        </div>

    </div>

</body>
</html>

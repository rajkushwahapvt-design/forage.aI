<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draft Preview - {{ $site->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl border border-slate-200 p-8 text-center shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl mx-auto mb-4">
            <i class="fa-solid fa-lock"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 mb-2">{{ $site->title }} is in Draft</h1>
        <p class="text-slate-600 text-sm mb-6">
            This website is currently being customized by its business owner and has not been published publicly yet.
        </p>
        <div class="space-y-3">
            <a href="{{ route('builder.editor', $site->id) }}" class="block w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                Open Site Editor to Publish
            </a>
            <a href="{{ route('builder.index') }}" class="block text-xs text-slate-500 hover:underline">
                Create a New Website
            </a>
        </div>
    </div>
</body>
</html>

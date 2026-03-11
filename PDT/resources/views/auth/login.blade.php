<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <title>Document</title>
</head>
<body>
<div class="flex items-center justify-center h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl mb-4">Login</h2>
           <form action="{{ url('/login') }}" method="POST">
    @csrf
    <div class="mb-4">
        <input type="email" name="email" placeholder="Email" class="border p-2 w-full" required>
    </div>
    <div class="mb-4">
        <input type="password" name="password" placeholder="Password" class="border p-2 w-full" required>
    </div>
    <button type="submit" class="bg-blue-500 text-white p-2 rounded w-full">Login</button>
</form>

        </div>
    </div>
   
</body>

</html>
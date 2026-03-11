@extends('layouts.app')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Public API Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-5xl mx-auto bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">Users from Public API</h1>

        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-200 text-gray-700 uppercase text-sm">
                    <th class="py-3 px-4 border-b">ID</th>
                    <th class="py-3 px-4 border-b">Name</th>
                    <th class="py-3 px-4 border-b">Email</th>
                    <th class="py-3 px-4 border-b">Phone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="py-2 px-4 border-b">{{ $user['id'] }}</td>
                    <td class="py-2 px-4 border-b font-medium">{{ $user['name'] }}</td>
                    <td class="py-2 px-4 border-b text-blue-600">{{ $user['email'] }}</td>
                    <td class="py-2 px-4 border-b">{{ $user['phone'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>

@endsection
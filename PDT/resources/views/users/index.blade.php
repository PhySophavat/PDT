@extends('layouts.app')

@section('title', 'Users')
@section('page_title', 'Public API Users')
@section('page_subtitle', 'បញ្ជីអ្នកប្រើប្រាស់ដែលត្រូវបានទាញយកពី API ខាងក្រៅ។')

@section('content')
<div class="space-y-6">
    <section class="info-panel">
        <h2 class="info-panel__title">Users from Public API</h2>

        <div class="overflow-x-auto mt-4">
            <table class="min-w-full rounded-lg overflow-hidden">
                <thead>
                    <tr class="uppercase text-sm">
                        <th class="py-3 px-4 border-b text-left">ID</th>
                        <th class="py-3 px-4 border-b text-left">Name</th>
                        <th class="py-3 px-4 border-b text-left">Email</th>
                        <th class="py-3 px-4 border-b text-left">Phone</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="py-3 px-4 border-b">{{ $user['id'] }}</td>
                            <td class="py-3 px-4 border-b font-medium">{{ $user['name'] }}</td>
                            <td class="py-3 px-4 border-b">{{ $user['email'] }}</td>
                            <td class="py-3 px-4 border-b">{{ $user['phone'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

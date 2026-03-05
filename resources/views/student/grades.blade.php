@extends('layouts.app')
@section('title', 'Grade Details')

@section('content')
    <div class="bg-white p-8 rounded shadow w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">Grade Details</h2>

        <table class="w-full text-sm border-collapse">
            <tbody>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600 w-1/3">Record ID</td>
                    <td class="py-2">{{ $grade->id }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Student ID</td>
                    <td class="py-2">{{ $grade->user_id }}</td>
                </tr>
                 <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Student Name</td>
                    <td class="py-2">{{ $grade->student->name }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Subject</td>
                    <td class="py-2">{{ $grade->subject }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-medium text-gray-600">Score</td>
                    <td class="py-2">{{ $grade->score }}</td>
                </tr>
                <tr>
                    <td class="py-2 font-medium text-gray-600">Semester</td>
                    <td class="py-2">{{ $grade->semester ?? 'N/A' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="mt-6 flex justify-between">
            <a href="/student/grades/{{ $grade->id - 1 }}"
               class="text-blue-500 hover:underline">&larr; Previous</a>
            <a href="/dashboard" class="text-gray-500 hover:underline">Dashboard</a>
            <a href="/student/grades/{{ $grade->id + 1 }}"
               class="text-blue-500 hover:underline">Next &rarr;</a>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">{{ $product->name }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6">
        <p><strong>ID:</strong> {{ $product->id }}</p>
        <p><strong>Name:</strong> {{ $product->name }}</p>
        <p><strong>Description:</strong> {{ $product->description }}</p>
        <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
        <p><strong>Created At:</strong> {{ $product->created_at->format('Y-m-d H:i:s') }}</p>
        <p><strong>Updated At:</strong> {{ $product->updated_at->format('Y-m-d H:i:s') }}</p>

        <div class="mt-6">
            <a href="{{ route('products.edit', $product) }}" class="bg-blue-500 text-white px-4 py-2 rounded mr-2">Edit</a>
            <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Back to List</a>
        </div>
    </div>
</div>
@endsection
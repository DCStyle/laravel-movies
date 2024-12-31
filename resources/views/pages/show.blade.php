@extends('layouts.app')

@section('seo')
    {!! seo($page->getDynamicSEOData()) !!}
@endsection

@section('content')
    <article class="max-w-4xl mx-auto sm:px-4 sm:py-8">
        <!-- Article Header -->
        <header class="mb-8 text-center">
            <h1 class="text-4xl font-bold mb-4">{{ $page->title }}</h1>
        </header>

        <!-- Article Content -->
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
    </article>
@endsection
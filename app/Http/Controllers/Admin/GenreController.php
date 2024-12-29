<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GenreController extends Controller
{
    public function index(Request $request)
    {
        $query = Genre::withCount('movies');

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $genres = $query->latest()->paginate(50)->withQueryString();

        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        $genre = new Genre();

        return view('admin.genres.add_edit', compact('genre'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string'
        ]);

        Genre::create($validated);

        return redirect()
            ->route('admin.genres.index')
            ->with('success', 'Tạo thể loại thành công');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.add_edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:genres,name,' . $genre->id,
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string'
        ]);

        $genre->update($validated);

        return redirect()
            ->route('admin.genres.index')
            ->with('success', 'Cập nhật thể loại thành công');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();

        return redirect()
            ->route('admin.genres.index')
            ->with('success', 'Xóa thể loại thành công');
    }
}
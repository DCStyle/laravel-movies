<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::latest()->paginate(10);
        return view('management.pages.index', compact('pages'));
    }

    public function create()
    {
        $page = new Page();

        return view('management.pages.add_edit', compact('page'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'is_active' => 'boolean'
        ]);

        $validated['slug'] = Str::slug($request->title);

        $page = Page::create($validated);

        $this->attachContentImages($page, $validated['content']);

        return redirect()->route('management.pages.index')
            ->with('success', 'Tạo trang thành công');
    }

    public function edit(Page $page)
    {
        return view('management.pages.add_edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable',
            'is_active' => 'boolean'
        ]);

        if ($request->title !== $page->title) {
            $validated['slug'] = Str::slug($request->title);
        }

        $page->update($validated);

        $this->attachContentImages($page, $validated['content']);

        return redirect()->route('management.pages.index')
            ->with('success', 'Cập nhật trang thành công');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        return redirect()->route('management.pages.index')
            ->with('success', 'Xóa trang thành công');
    }

    private function attachContentImages($page, $content)
    {
        preg_match_all('/<img[^>]+src="([^">]+)"/', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $src) {
                $path = str_replace(asset('storage/'), '', $src);
                Image::where('path', $path)
                    ->whereNull('imageable_type')
                    ->update([
                        'imageable_type' => Page::class,
                        'imageable_id' => $page->id
                    ]);
            }
        }
    }
}
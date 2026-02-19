<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\HomeCarouselItem;
use App\Models\HomeContentBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeContentController extends Controller
{
    public function index()
    {
        $carouselItems = HomeCarouselItem::orderBy('sort_order')->get();
        $contentBlocks = HomeContentBlock::orderBy('sort_order')->get();
        return view('admin.home-content.index', compact('carouselItems', 'contentBlocks'));
    }

    // Carousel
    public function createCarousel()
    {
        return view('admin.home-content.carousel-form', ['item' => null]);
    }

    public function storeCarousel(Request $request)
    {
        $data = $request->validate([
            'caption' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link_url' => 'nullable|url|max:500',
            'sort_order' => 'integer',
            'active' => 'boolean',
            'image' => 'required|image|max:5120',
        ]);

        $path = $this->storeUpload($request->file('image'), 'carousel');
        $data['image_path'] = $path;
        $data['active'] = $request->boolean('active', true);
        unset($data['image']);

        HomeCarouselItem::create($data);
        AuditLog::log('carousel_item_created');
        return redirect()->route('admin.home-content.index')->with('success', 'Carousel item added.');
    }

    public function editCarousel(HomeCarouselItem $carouselItem)
    {
        return view('admin.home-content.carousel-form', ['item' => $carouselItem]);
    }

    public function updateCarousel(Request $request, HomeCarouselItem $carouselItem)
    {
        $data = $request->validate([
            'caption' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'link_url' => 'nullable|url|max:500',
            'sort_order' => 'integer',
            'active' => 'boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $this->storeUpload($request->file('image'), 'carousel');
            $data['image_path'] = $path;
        }
        $data['active'] = $request->boolean('active', true);
        unset($data['image']);

        $carouselItem->update($data);
        return redirect()->route('admin.home-content.index')->with('success', 'Carousel item updated.');
    }

    public function destroyCarousel(HomeCarouselItem $carouselItem)
    {
        $carouselItem->delete();
        return redirect()->route('admin.home-content.index')->with('success', 'Carousel item deleted.');
    }

    // Content Blocks
    public function createBlock()
    {
        return view('admin.home-content.block-form', ['block' => null]);
    }

    public function storeBlock(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body_html' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        HomeContentBlock::create($data);
        AuditLog::log('content_block_created');
        return redirect()->route('admin.home-content.index')->with('success', 'Content block added.');
    }

    public function editBlock(HomeContentBlock $contentBlock)
    {
        return view('admin.home-content.block-form', ['block' => $contentBlock]);
    }

    public function updateBlock(Request $request, HomeContentBlock $contentBlock)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body_html' => 'required|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'integer',
            'active' => 'boolean',
        ]);
        $data['active'] = $request->boolean('active', true);

        $contentBlock->update($data);
        return redirect()->route('admin.home-content.index')->with('success', 'Content block updated.');
    }

    public function destroyBlock(HomeContentBlock $contentBlock)
    {
        $contentBlock->delete();
        return redirect()->route('admin.home-content.index')->with('success', 'Content block deleted.');
    }

    private function storeUpload($file, string $folder): string
    {
        $tenant = app('current_tenant');
        $name = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = "uploads/{$tenant->slug}/{$folder}";
        $file->move(storage_path("app/public/{$path}"), $name);
        return "{$path}/{$name}";
    }
}

<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Event;
use App\Models\HomeCarouselItem;
use App\Models\HomeContentBlock;

class HomeController extends Controller
{
    public function index()
    {
        $tenant = app('current_tenant');
        $carouselItems = HomeCarouselItem::where('active', true)->orderBy('sort_order')->get();
        $contentBlocks = HomeContentBlock::where('active', true)->orderBy('sort_order')->get();
        $latestPosts = BlogPost::published()->orderByDesc('published_at')->limit(3)->get();
        $upcomingEvent = Event::published()->upcoming()->orderBy('start_at')->first();

        return view('public.home', compact('tenant', 'carouselItems', 'contentBlocks', 'latestPosts', 'upcomingEvent'));
    }
}

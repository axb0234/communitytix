<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::with('author')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(15)->withQueryString();
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.form', ['post' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body_html' => 'required|string',
            'status' => 'required|in:draft,published',
            'featured_image_file' => 'nullable|image|max:5120',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['author_id'] = auth()->id();

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        if ($request->hasFile('featured_image_file')) {
            $data['featured_image'] = $this->storeUpload($request->file('featured_image_file'), 'blog');
        }
        unset($data['featured_image_file']);

        // Ensure unique slug
        $baseSlug = $data['slug'];
        $counter = 1;
        while (BlogPost::withoutGlobalScopes()->where('tenant_id', app('current_tenant')->id)->where('slug', $data['slug'])->exists()) {
            $data['slug'] = $baseSlug . '-' . $counter++;
        }

        $post = BlogPost::create($data);
        AuditLog::log('blog_post_created', 'BlogPost', $post->id);

        return redirect()->route('admin.blog.index')->with('success', 'Post created.');
    }

    public function edit(BlogPost $post)
    {
        return view('admin.blog.form', compact('post'));
    }

    public function update(Request $request, BlogPost $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body_html' => 'required|string',
            'status' => 'required|in:draft,published',
            'featured_image_file' => 'nullable|image|max:5120',
        ]);

        if ($data['status'] === 'published' && !$post->published_at) {
            $data['published_at'] = now();
            AuditLog::log('blog_post_published', 'BlogPost', $post->id);
        }

        if ($request->hasFile('featured_image_file')) {
            $data['featured_image'] = $this->storeUpload($request->file('featured_image_file'), 'blog');
        }
        unset($data['featured_image_file']);

        $post->update($data);
        return redirect()->route('admin.blog.index')->with('success', 'Post updated.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.index')->with('success', 'Post deleted.');
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

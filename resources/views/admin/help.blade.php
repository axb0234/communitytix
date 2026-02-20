@extends('admin.layout')
@section('page-title', 'Help & Guide')

@push('styles')
<style>
    .help-content { max-width: 900px; }
    .help-content h2 {
        font-size: 1.6rem; font-weight: 700; color: #2d3748;
        border-bottom: 3px solid #18bc9c; padding-bottom: 0.5rem; margin: 2.5rem 0 1rem;
    }
    .help-content h3 {
        font-size: 1.2rem; font-weight: 600; color: #4a5568; margin: 1.5rem 0 0.75rem;
    }
    .help-content h4 {
        font-size: 1.05rem; font-weight: 600; color: #718096; margin: 1.2rem 0 0.5rem;
    }
    .help-content p, .help-content li { color: #4a5568; line-height: 1.7; }
    .help-content img.screenshot {
        max-width: 100%; border: 1px solid #e2e8f0; border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin: 1rem 0 1.5rem; cursor: pointer;
    }
    .help-content .tip-box {
        background: #ebf8ff; border-left: 4px solid #4299e1; padding: 1rem 1.25rem;
        border-radius: 0 6px 6px 0; margin: 1rem 0;
    }
    .help-content .tip-box strong { color: #2b6cb0; }
    .help-content .warning-box {
        background: #fffbeb; border-left: 4px solid #ecc94b; padding: 1rem 1.25rem;
        border-radius: 0 6px 6px 0; margin: 1rem 0;
    }
    .help-content .warning-box strong { color: #975a16; }
    .help-content .step-badge {
        display: inline-block; background: #18bc9c; color: #fff; width: 28px; height: 28px;
        border-radius: 50%; text-align: center; line-height: 28px; font-weight: 700;
        font-size: 0.85rem; margin-right: 0.5rem;
    }
    .help-content code {
        background: #edf2f7; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; color: #e53e3e;
    }
    .help-toc {
        background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 1.5rem 2rem;
        margin-bottom: 2rem;
    }
    .help-toc h5 { font-weight: 700; color: #2d3748; margin-bottom: 0.75rem; }
    .help-toc ol { padding-left: 1.25rem; margin: 0; }
    .help-toc li { margin-bottom: 0.35rem; }
    .help-toc a { color: #4299e1; text-decoration: none; font-weight: 500; }
    .help-toc a:hover { text-decoration: underline; }
    .help-content .field-table { font-size: 0.9rem; }
    .help-content .field-table th { background: #f7fafc; font-weight: 600; }
    .help-content .field-table td, .help-content .field-table th { padding: 0.5rem 0.75rem; }

    /* Lightbox */
    .help-lightbox {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.85); z-index: 9999; justify-content: center; align-items: center;
        cursor: pointer;
    }
    .help-lightbox.active { display: flex; }
    .help-lightbox img { max-width: 95%; max-height: 95%; border-radius: 8px; }
</style>
@endpush

@section('content')
<div class="help-content">
    <div class="d-flex align-items-center mb-3">
        <h1 class="h3 mb-0"><i class="fas fa-life-ring text-primary me-2"></i>Admin Help Guide</h1>
    </div>
    <p class="text-muted mb-4">
        Welcome to the CommunityTix administration guide. This page covers everything you need to manage your
        community organisation's website, events, tickets, and payments.
    </p>

    {{-- Table of Contents --}}
    <div class="help-toc">
        <h5><i class="fas fa-list me-2"></i>Contents</h5>
        <ol>
            <li><a href="#dashboard">Dashboard Overview</a></li>
            <li><a href="#home-page">Managing the Home Page</a></li>
            <li><a href="#blog">Blog Posts</a></li>
            <li><a href="#events">Events, Tickets & PWYC</a></li>
            <li><a href="#orders">Online Orders</a></li>
            <li><a href="#rsvps">RSVPs (Free Events)</a></li>
            <li><a href="#cash">Cash Collections</a></li>
            <li><a href="#pos">Card at Door (POS)</a></li>
            <li><a href="#members">Member Management</a></li>
            <li><a href="#settings">Settings & PayPal Integration</a></li>
            <li><a href="#tips">General Tips</a></li>
        </ol>
    </div>

    {{-- ============================================================ --}}
    {{-- 1. DASHBOARD --}}
    {{-- ============================================================ --}}
    <h2 id="dashboard"><i class="fas fa-tachometer-alt me-2"></i>1. Dashboard Overview</h2>

    <p>The Dashboard is your home screen when you log in. It provides an at-a-glance summary of your organisation's activity.</p>

    <img src="/help-images/dashboard.png" alt="Dashboard" class="screenshot" onclick="openLightbox(this)">

    <h3>What you'll see</h3>
    <ul>
        <li><strong>Stats cards</strong> &mdash; Total members, pending approvals, upcoming events, blog posts, orders, and total revenue.</li>
        <li><strong>Recent Orders</strong> &mdash; The latest ticket orders with status indicators (Completed, Pending, Failed).</li>
        <li><strong>Upcoming Events</strong> &mdash; Your next events at a glance with dates and type (Free/Ticketed).</li>
    </ul>

    <div class="tip-box">
        <strong><i class="fas fa-lightbulb me-1"></i> Tip:</strong> Use the sidebar on the left to navigate to any section. On mobile, tap the
        <i class="fas fa-bars"></i> hamburger icon to show the sidebar.
    </div>

    {{-- ============================================================ --}}
    {{-- 2. HOME PAGE --}}
    {{-- ============================================================ --}}
    <h2 id="home-page"><i class="fas fa-home me-2"></i>2. Managing the Home Page</h2>

    <p>The Home Page section lets you control what visitors see when they first arrive at your website. It has two main components: <strong>Carousel Items</strong> (the image slideshow) and <strong>Content Blocks</strong> (the info cards below the carousel).</p>

    <img src="/help-images/home-content.png" alt="Home Page Content" class="screenshot" onclick="openLightbox(this)">

    <h3>Carousel Items (Image Slideshow)</h3>
    <p>The carousel is the large banner at the top of your home page. Each item consists of:</p>

    <table class="table table-bordered field-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><strong>Image</strong></td><td>The banner image (recommended: 1200x500 pixels or wider). Accepted formats: JPG, PNG.</td></tr>
            <tr><td><strong>Caption</strong></td><td>The main heading text displayed over the image.</td></tr>
            <tr><td><strong>Subtitle</strong></td><td>A secondary line of text displayed below the caption.</td></tr>
            <tr><td><strong>Link URL</strong></td><td>Optional: where the visitor goes if they click. Leave blank for no link.</td></tr>
            <tr><td><strong>Sort Order</strong></td><td>Controls the display order. Lower numbers appear first (0, 1, 2...).</td></tr>
            <tr><td><strong>Active</strong></td><td>Uncheck to hide a carousel item without deleting it.</td></tr>
        </tbody>
    </table>

    <img src="/help-images/carousel-edit.png" alt="Edit Carousel Item" class="screenshot" onclick="openLightbox(this)">

    <h4>To add a carousel item:</h4>
    <ol>
        <li>Click <strong>+ Add</strong> next to "Carousel Items".</li>
        <li>Upload an image, enter a caption and subtitle.</li>
        <li>Set the sort order (0 = first).</li>
        <li>Click <strong>Save</strong>.</li>
    </ol>

    <h4>To edit or delete:</h4>
    <ul>
        <li>Click the <i class="fas fa-edit text-primary"></i> edit icon to modify an existing item.</li>
        <li>Click the <i class="fas fa-trash text-danger"></i> delete icon to remove it (you will be asked to confirm).</li>
    </ul>

    <h3>Content Blocks (Info Cards)</h3>
    <p>Content blocks appear below the carousel as information cards with icons. They're great for highlighting key messages like "About Us", "Upcoming Events", and "Get Involved".</p>

    <table class="table table-bordered field-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><strong>Title</strong></td><td>The heading of the card (e.g., "Our Community").</td></tr>
            <tr><td><strong>Body</strong></td><td>The descriptive text. Supports rich text formatting.</td></tr>
            <tr><td><strong>Icon</strong></td><td>A Font Awesome icon class (e.g., <code>fas fa-heart</code>, <code>fas fa-calendar-alt</code>). Browse icons at <a href="https://fontawesome.com/icons" target="_blank">fontawesome.com/icons</a>.</td></tr>
            <tr><td><strong>Sort Order</strong></td><td>Display order (0, 1, 2...).</td></tr>
            <tr><td><strong>Active</strong></td><td>Toggle visibility without deleting.</td></tr>
        </tbody>
    </table>

    {{-- ============================================================ --}}
    {{-- 3. BLOG --}}
    {{-- ============================================================ --}}
    <h2 id="blog"><i class="fas fa-blog me-2"></i>3. Blog Posts</h2>

    <p>Use blog posts to share news, updates, event recaps, and community stories. Posts appear on your public website's Blog page.</p>

    <img src="/help-images/blog-list.png" alt="Blog Posts List" class="screenshot" onclick="openLightbox(this)">

    <h3>Creating a Blog Post</h3>
    <ol>
        <li>Go to <strong>Blog Posts</strong> in the sidebar.</li>
        <li>Click <strong>+ New Post</strong>.</li>
        <li>Fill in the form fields:</li>
    </ol>

    <table class="table table-bordered field-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><strong>Title *</strong></td><td>The headline of your blog post.</td></tr>
            <tr><td><strong>Excerpt</strong></td><td>A short summary shown on the blog listing page. Keep it to 1-2 sentences.</td></tr>
            <tr><td><strong>Content *</strong></td><td>The full blog post body. Use the rich text editor to format text, add headings, lists, links, and images.</td></tr>
            <tr><td><strong>Featured Image</strong></td><td>An image shown at the top of the post and on the listing page. Recommended: 800x400 pixels.</td></tr>
            <tr><td><strong>Status</strong></td><td><strong>Draft</strong> = not visible to the public. <strong>Published</strong> = live on the website.</td></tr>
        </tbody>
    </table>

    <img src="/help-images/blog-edit.png" alt="Edit Blog Post" class="screenshot" onclick="openLightbox(this)">

    <h3>Using the Rich Text Editor</h3>
    <p>The editor toolbar lets you:</p>
    <ul>
        <li><strong>Style</strong> &mdash; Apply heading styles (H2, H3, H4, etc.) or paragraph formatting.</li>
        <li><strong>Bold / Italic / Underline</strong> &mdash; Standard text formatting.</li>
        <li><strong>Lists</strong> &mdash; Create bullet points or numbered lists.</li>
        <li><strong>Links</strong> &mdash; Highlight text and click the link icon to add a hyperlink.</li>
        <li><strong>Images</strong> &mdash; Insert images by URL (use externally hosted images).</li>
        <li><strong>Tables</strong> &mdash; Add tabular data.</li>
        <li><strong>Code View</strong> &mdash; Switch to raw HTML editing for advanced users.</li>
    </ul>

    <div class="tip-box">
        <strong><i class="fas fa-lightbulb me-1"></i> Tip:</strong> The editor supports content in any language including
        Bengali, Hindi, Chinese, Japanese, Korean, and other non-Latin scripts. Simply type or paste your content in any language.
    </div>

    <h3>Filtering & Searching Posts</h3>
    <p>Use the search box to find posts by title, and the status dropdown to filter by Draft or Published.</p>

    {{-- ============================================================ --}}
    {{-- 4. EVENTS --}}
    {{-- ============================================================ --}}
    <h2 id="events"><i class="fas fa-calendar-alt me-2"></i>4. Events, Tickets & PWYC</h2>

    <p>Events are the heart of CommunityTix. You can create <strong>Free (RSVP-only)</strong> or <strong>Ticketed (paid)</strong> events.</p>

    <img src="/help-images/events-list.png" alt="Events List" class="screenshot" onclick="openLightbox(this)">

    <h3>Events List</h3>
    <p>The events list shows all your events sorted by date (newest first) with detailed columns:</p>
    <ul>
        <li><strong>Guests</strong> &mdash; RSVP count, Ticketed count, and Total.</li>
        <li><strong>Tickets Sold</strong> &mdash; Broken down by Online, Cash, and Card at Door.</li>
        <li><strong>Revenue</strong> &mdash; Income from each channel and the total.</li>
    </ul>
    <p>Use the filters at the top to search by name, filter by status (Draft/Published), or by type (Free/Ticketed).
        Click <strong><i class="fas fa-file-excel"></i> Export</strong> to download the data as a CSV file for use in Excel or Google Sheets.</p>

    <h3>Creating an Event</h3>
    <ol>
        <li>Click <strong>+ New Event</strong>.</li>
        <li>Fill in the event details.</li>
        <li>Set Status to <strong>Published</strong> when ready to go live (or keep as Draft to prepare it first).</li>
        <li>Click <strong>Save</strong>.</li>
        <li>After saving, you can add <strong>Ticket Types</strong> and <strong>Event Images</strong>.</li>
    </ol>

    <img src="/help-images/event-edit.png" alt="Edit Event" class="screenshot" onclick="openLightbox(this)">

    <h3>Event Fields</h3>
    <table class="table table-bordered field-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><strong>Title *</strong></td><td>The name of your event.</td></tr>
            <tr><td><strong>Event Type *</strong></td><td><strong>Free RSVP</strong> = guests register for free. <strong>Ticketed</strong> = guests buy tickets online (requires PayPal setup).</td></tr>
            <tr><td><strong>Start Date/Time *</strong></td><td>When the event begins.</td></tr>
            <tr><td><strong>End Date/Time</strong></td><td>When the event ends (optional).</td></tr>
            <tr><td><strong>Status *</strong></td><td><strong>Draft</strong> = hidden from public. <strong>Published</strong> = visible on the website.</td></tr>
            <tr><td><strong>Location</strong></td><td>Venue name (e.g., "Community Hall").</td></tr>
            <tr><td><strong>Address</strong></td><td>Full address for the venue.</td></tr>
            <tr><td><strong>Short Description</strong></td><td>A brief summary shown on event listing cards. Keep it to 1-2 sentences.</td></tr>
            <tr><td><strong>RSVP Capacity</strong></td><td>Maximum number of RSVPs allowed for free events. Leave blank for unlimited.</td></tr>
            <tr><td><strong>Flyer / Poster</strong></td><td>Upload a promotional image or PDF flyer for the event.</td></tr>
            <tr><td><strong>Full Description</strong></td><td>Detailed event description with rich text formatting. Include schedules, activities, food info, etc.</td></tr>
        </tbody>
    </table>

    <h3 id="ticket-types">Ticket Types (for Ticketed Events)</h3>
    <p>After creating a ticketed event, scroll down to the <strong>Ticket Types</strong> section to define your pricing tiers.</p>

    <table class="table table-bordered field-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><strong>Name</strong></td><td>The ticket name (e.g., "Adult", "Child", "Family (2+2)").</td></tr>
            <tr><td><strong>Price</strong></td><td>Ticket price in your organisation's currency.</td></tr>
            <tr><td><strong>Capacity</strong></td><td>Maximum tickets available. Leave blank for unlimited.</td></tr>
            <tr><td><strong>Sort Order</strong></td><td>Display order on the public event page.</td></tr>
        </tbody>
    </table>

    <div class="tip-box">
        <strong><i class="fas fa-lightbulb me-1"></i> Tip:</strong> You can deactivate a ticket type rather than deleting it.
        Click the edit icon on a ticket type to toggle its Active status.
    </div>

    <h3 id="pwyc">Pay What You Can (PWYC)</h3>
    <p>PWYC allows attendees to choose how much they pay, making events accessible to everyone regardless of budget.
    This is available for <strong>Ticketed</strong> events only.</p>

    <h4>To enable PWYC:</h4>
    <ol>
        <li>When editing a Ticketed event, find the <strong>Pay What You Can (PWYC)</strong> section.</li>
        <li>Check <strong>Enable Pay What You Can</strong>.</li>
        <li>Optionally enter up to 3 <strong>Suggested Amounts</strong> (e.g., 5.00, 10.00, 20.00). These appear as quick-select buttons for the attendee.</li>
        <li>If you leave suggested amounts blank, attendees can enter any amount they wish.</li>
    </ol>

    <div class="tip-box">
        <strong><i class="fas fa-lightbulb me-1"></i> Tip:</strong> PWYC works alongside regular ticket types. Attendees can buy
        fixed-price tickets AND add a PWYC contribution, or just pay what they can on its own.
    </div>

    <h3>Event Images</h3>
    <p>Upload photos to create a gallery on the event page. These appear below the event description.</p>
    <ul>
        <li>Scroll to the <strong>Event Images</strong> section at the bottom of the event edit page.</li>
        <li>Choose a file and optionally add a caption.</li>
        <li>Click <strong>Upload</strong>.</li>
        <li>Images can be deleted with the trash icon.</li>
    </ul>

    {{-- ============================================================ --}}
    {{-- 5. ORDERS --}}
    {{-- ============================================================ --}}
    <h2 id="orders"><i class="fas fa-shopping-cart me-2"></i>5. Online Orders</h2>

    <p>The Orders page shows all online ticket purchases made through PayPal. Each order is automatically created when
    a visitor begins checkout and updated when payment is completed.</p>

    <img src="/help-images/orders.png" alt="Orders" class="screenshot" onclick="openLightbox(this)">

    <h3>Order Statuses</h3>
    <table class="table table-bordered field-table">
        <thead><tr><th>Status</th><th>Meaning</th></tr></thead>
        <tbody>
            <tr><td><span class="badge bg-success">COMPLETED</span></td><td>Payment received successfully. The customer has been emailed a confirmation.</td></tr>
            <tr><td><span class="badge bg-warning text-dark">PENDING</span></td><td>Order created but payment not yet completed. The customer may have abandoned checkout.</td></tr>
            <tr><td><span class="badge bg-danger">FAILED</span></td><td>PayPal reported a payment failure.</td></tr>
            <tr><td><span class="badge bg-info">REFUNDED</span></td><td>The order has been manually marked as refunded by an admin.</td></tr>
        </tbody>
    </table>

    <h3>Filters</h3>
    <ul>
        <li><strong>Search</strong> &mdash; Search by purchaser name, email, or order number.</li>
        <li><strong>Status</strong> &mdash; Filter by order status.</li>
        <li><strong>Event</strong> &mdash; Filter orders for a specific event (dropdown shows event name with date).</li>
        <li><strong>Date Range</strong> &mdash; Enter From/To dates to narrow results.</li>
    </ul>

    <p>Click <strong><i class="fas fa-file-excel"></i> Export</strong> to download all filtered orders as a CSV file.</p>

    <h3>Viewing Order Details</h3>
    <p>Click an order number to view full details including: purchaser information, event name, line items with quantities and prices,
    total amount, PayPal transaction ID, and timestamps.</p>

    <h3>Marking a Refund</h3>
    <p>If you refund a customer through PayPal directly, you can mark the order as refunded in CommunityTix by
    clicking the <strong>Mark Refunded</strong> button on the order detail page. This is for record-keeping only &mdash;
    actual refunds must be processed through PayPal.</p>

    {{-- ============================================================ --}}
    {{-- 6. RSVPs --}}
    {{-- ============================================================ --}}
    <h2 id="rsvps"><i class="fas fa-clipboard-check me-2"></i>6. RSVPs (Free Events)</h2>

    <p>RSVPs are collected when visitors register for <strong>Free</strong> events. The RSVPs page shows all registrations with guest counts.</p>

    <img src="/help-images/rsvps.png" alt="RSVPs" class="screenshot" onclick="openLightbox(this)">

    <h3>Key Features</h3>
    <ul>
        <li><strong>Guest Count Banner</strong> &mdash; At the top, you'll see the total number of guests across all RSVPs (matching your current filters). This tells you how many people to expect.</li>
        <li><strong>Search & Filter</strong> &mdash; Search by name or email, filter by event, or use date range filters.</li>
        <li><strong>Export</strong> &mdash; Download the filtered RSVP list as a CSV file.</li>
    </ul>

    <div class="tip-box">
        <strong><i class="fas fa-lightbulb me-1"></i> Tip:</strong> The "Guests" column shows how many people
        each RSVP covers. One RSVP might bring 1, 2, or more guests. Use the total at the top for capacity planning.
    </div>

    {{-- ============================================================ --}}
    {{-- 7. CASH --}}
    {{-- ============================================================ --}}
    <h2 id="cash"><i class="fas fa-money-bill-wave me-2"></i>7. Cash Collections</h2>

    <p>Use this page to record cash payments collected at the door on the day of an event. This is for situations where
    attendees pay in cash rather than buying tickets online.</p>

    <img src="/help-images/cash.png" alt="Cash Collections" class="screenshot" onclick="openLightbox(this)">

    <h3>Recording a Cash Payment</h3>
    <ol>
        <li>Scroll down to the <strong>Record Cash Collection</strong> form.</li>
        <li>Select the <strong>Event</strong> from the dropdown.</li>
        <li>Enter the <strong>Amount</strong> collected.</li>
        <li>Optionally add <strong>Notes</strong> (e.g., "2 adults paid at door", "change given from 20").</li>
        <li>Click <strong><i class="fas fa-money-bill-wave"></i> Record</strong>.</li>
    </ol>

    <p>Each record is timestamped and attributed to the logged-in admin who recorded it.</p>

    <h3>Viewing & Exporting</h3>
    <ul>
        <li>The <strong>Total Banner</strong> shows the sum of all cash collected (for current filters).</li>
        <li>Use the event dropdown and date filters to narrow results.</li>
        <li>Click <strong>Export</strong> for a CSV download.</li>
    </ul>

    <div class="warning-box">
        <strong><i class="fas fa-exclamation-triangle me-1"></i> Important:</strong> Cash collections are manual records.
        Ensure each collection is entered promptly and accurately. You should reconcile cash records against physical cash at the end of each event.
    </div>

    {{-- ============================================================ --}}
    {{-- 8. POS --}}
    {{-- ============================================================ --}}
    <h2 id="pos"><i class="fas fa-credit-card me-2"></i>8. Card at Door (POS)</h2>

    <p>Use this page to record card payments taken at the door using a card reader or mobile POS device (e.g., SumUp, iZettle, Square).
    These are separate from online PayPal transactions.</p>

    <img src="/help-images/pos.png" alt="Card at Door (POS)" class="screenshot" onclick="openLightbox(this)">

    <h3>Recording a Card Payment</h3>
    <ol>
        <li>Scroll down to the <strong>Record Card Payment</strong> form.</li>
        <li>Select the <strong>Event</strong>.</li>
        <li>Enter the <strong>Amount</strong>.</li>
        <li>Enter the <strong>Reference / Receipt #</strong> from your card reader (optional but recommended for reconciliation).</li>
        <li>Add any <strong>Notes</strong> if needed.</li>
        <li>Click <strong><i class="fas fa-credit-card"></i> Record (Pending Reconciliation)</strong>.</li>
    </ol>

    <h3>Status</h3>
    <p>Card payments are recorded with a status of <strong>PENDING_RECONCILIATION</strong>. This reminds you to verify
    that the amount actually settled into your bank account from your POS provider.</p>

    <div class="tip-box">
        <strong><i class="fas fa-lightbulb me-1"></i> Tip:</strong> Keep the receipt reference from your card reader.
        This makes it easy to match CommunityTix records with your bank statements.
    </div>

    {{-- ============================================================ --}}
    {{-- 9. MEMBERS --}}
    {{-- ============================================================ --}}
    <h2 id="members"><i class="fas fa-users me-2"></i>9. Member Management</h2>

    <p>The Members page lets you manage your community membership. People can sign up via the "Join Us" page on your
    public website, and you manage their status from here.</p>

    <img src="/help-images/members.png" alt="Members" class="screenshot" onclick="openLightbox(this)">

    <h3>Member Types</h3>
    <table class="table table-bordered field-table">
        <thead><tr><th>Type</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><span class="badge bg-secondary">GUEST</span></td><td>Initial sign-up type. Can RSVP and buy tickets.</td></tr>
            <tr><td><span class="badge bg-primary">ORDINARY</span></td><td>A full member of your organisation. Promoted from Guest by an admin.</td></tr>
            <tr><td><span class="badge bg-danger">GOVERNING</span></td><td>An admin/committee member with full access to the admin panel.</td></tr>
        </tbody>
    </table>

    <h3>Member Statuses</h3>
    <table class="table table-bordered field-table">
        <thead><tr><th>Status</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><span class="badge bg-success">ACTIVE</span></td><td>Member is active and can use the site normally.</td></tr>
            <tr><td><span class="badge bg-warning text-dark">PENDING_APPROVAL</span></td><td>New sign-up waiting to be approved by an admin.</td></tr>
            <tr><td><span class="badge bg-dark">SUSPENDED</span></td><td>Member has been suspended. Cannot access member features.</td></tr>
        </tbody>
    </table>

    <h3>Managing a Member</h3>
    <p>Click a member's name to view their details. From the detail page you can:</p>
    <ul>
        <li><strong>Approve</strong> &mdash; Approve a pending member (changes status to Active).</li>
        <li><strong>Promote to Ordinary</strong> &mdash; Upgrade a Guest to a full Ordinary member.</li>
        <li><strong>Promote to Governing</strong> &mdash; Give a member admin access (use sparingly!).</li>
        <li><strong>Suspend</strong> &mdash; Temporarily block a member.</li>
        <li><strong>Activate</strong> &mdash; Re-activate a suspended member.</li>
        <li><strong>Edit</strong> &mdash; Update their name, email, phone, and membership type.</li>
    </ul>

    <div class="warning-box">
        <strong><i class="fas fa-exclamation-triangle me-1"></i> Important:</strong> Only promote members to
        <strong>GOVERNING</strong> if they need admin access. Governing members can modify all content, events, settings,
        and other members. Keep this group small and trusted.
    </div>

    {{-- ============================================================ --}}
    {{-- 10. SETTINGS --}}
    {{-- ============================================================ --}}
    <h2 id="settings"><i class="fas fa-cogs me-2"></i>10. Settings & PayPal Integration</h2>

    <p>Settings is where you configure your organisation's core information and payment integration.</p>

    <img src="/help-images/settings.png" alt="Settings" class="screenshot" onclick="openLightbox(this)">

    <h3>Organisation Settings</h3>
    <table class="table table-bordered field-table">
        <thead><tr><th>Field</th><th>Description</th></tr></thead>
        <tbody>
            <tr><td><strong>Organisation Name *</strong></td><td>Your organisation's name. Appears in the header, footer, emails, and browser tab.</td></tr>
            <tr><td><strong>Tagline</strong></td><td>A short motto or description shown on the home page hero section.</td></tr>
            <tr><td><strong>Currency *</strong></td><td>The currency for all ticket prices and payments (GBP, USD, EUR, INR, CAD, AUD).</td></tr>
            <tr><td><strong>Timezone *</strong></td><td>Your local timezone (e.g., <code>Europe/London</code>, <code>America/New_York</code>).</td></tr>
            <tr><td><strong>Contact Email</strong></td><td>Shown in order confirmation emails. Customers can reach you at this address.</td></tr>
            <tr><td><strong>Logo</strong></td><td>Your organisation's logo. Appears in the navbar. Recommended: transparent PNG, 200x60 pixels.</td></tr>
        </tbody>
    </table>

    <h3 id="paypal">PayPal Integration</h3>
    <p>PayPal is required for accepting online ticket payments. If you only run free (RSVP) events, you do not need to set up PayPal.</p>

    <h4><span class="step-badge">1</span>Choose Mode: Sandbox or Live</h4>
    <ul>
        <li><strong>Sandbox</strong> &mdash; Use this for testing. No real money is charged. Recommended when setting up for the first time.</li>
        <li><strong>Live</strong> &mdash; Use this when you're ready to accept real payments. Requires a verified PayPal Business account.</li>
    </ul>

    <h4><span class="step-badge">2</span>Create a PayPal REST App</h4>
    <ol>
        <li>Go to the <a href="https://developer.paypal.com/dashboard/applications" target="_blank"><strong>PayPal Developer Dashboard</strong></a> and log in with your PayPal account.</li>
        <li>Navigate to <strong>Apps & Credentials</strong>.</li>
        <li>Select the <strong>Sandbox</strong> or <strong>Live</strong> tab (must match your CommunityTix Mode).</li>
        <li>Click <strong>Create App</strong>.</li>
        <li>Give it a name (e.g., "CommunityTix").</li>
        <li>After creation, copy the <strong>Client ID</strong> and <strong>Secret</strong>.</li>
    </ol>

    <h4><span class="step-badge">3</span>Enter Credentials in CommunityTix</h4>
    <ol>
        <li>In the <strong>PayPal Settings</strong> section of your Settings page:</li>
        <li>Set <strong>Mode</strong> to match the PayPal dashboard tab you used.</li>
        <li>Paste the <strong>Client ID</strong>.</li>
        <li>Paste the <strong>Client Secret</strong>.</li>
        <li>Click <strong>Save PayPal Settings</strong>.</li>
    </ol>

    <h4><span class="step-badge">4</span>Create a Webhook in PayPal</h4>
    <p>Webhooks allow PayPal to notify CommunityTix when a payment is completed, so orders can be automatically updated.</p>
    <ol>
        <li>In the PayPal Developer Dashboard, go to your app's settings.</li>
        <li>Scroll to <strong>Webhooks</strong> and click <strong>Add Webhook</strong>.</li>
        <li>For the <strong>Webhook URL</strong>, copy the URL shown on your CommunityTix settings page
        (it looks like: <code>https://yourdomain.communitytix.org/webhook/paypal/your-slug</code>).</li>
        <li>Select these events to subscribe to:
            <ul>
                <li><code>PAYMENT.CAPTURE.COMPLETED</code> (required)</li>
                <li><code>PAYMENT.CAPTURE.DENIED</code> (recommended)</li>
                <li><code>PAYMENT.CAPTURE.PENDING</code> (recommended)</li>
                <li><code>CHECKOUT.ORDER.APPROVED</code> (recommended)</li>
            </ul>
        </li>
        <li>Save the webhook.</li>
        <li>Copy the <strong>Webhook ID</strong> that PayPal displays after creation.</li>
        <li>Paste the Webhook ID into the <strong>Webhook ID</strong> field in CommunityTix and save.</li>
    </ol>

    <h4><span class="step-badge">5</span>Test the Integration</h4>
    <ol>
        <li>Create a <strong>Ticketed Event</strong> and publish it.</li>
        <li>Visit the event page on your public site.</li>
        <li>Select a ticket and proceed through checkout.</li>
        <li>Complete the payment in PayPal (use sandbox test accounts if in Sandbox mode).</li>
        <li>Verify the order shows as <strong>COMPLETED</strong> in your Orders page.</li>
        <li>Check that a confirmation email was sent to the buyer.</li>
    </ol>

    <h4>Going Live Checklist</h4>
    <p>Before switching from Sandbox to Live:</p>
    <ul>
        <li>Ensure your PayPal Business account is verified.</li>
        <li>Create a <strong>Live</strong> REST app in PayPal (do not reuse Sandbox credentials).</li>
        <li>Create a <strong>Live</strong> webhook pointing to the same webhook URL.</li>
        <li>Update CommunityTix: Mode = Live, paste Live Client ID, Secret, and Webhook ID.</li>
        <li>Test with a small real transaction to confirm everything works.</li>
    </ul>

    <h4>Troubleshooting PayPal</h4>
    <table class="table table-bordered field-table">
        <thead><tr><th>Problem</th><th>Solution</th></tr></thead>
        <tbody>
            <tr>
                <td><strong>"Invalid client" or 401 error</strong></td>
                <td>Client ID or Secret is wrong. Check you copied from the correct PayPal tab (Sandbox vs Live) that matches your CommunityTix Mode.</td>
            </tr>
            <tr>
                <td><strong>Orders created but stay PENDING</strong></td>
                <td>Webhook not set up or wrong Webhook ID. Verify the webhook URL is correct and the required events are selected.</td>
            </tr>
            <tr>
                <td><strong>Webhook signature verification failed</strong></td>
                <td>The Webhook ID in CommunityTix doesn't match PayPal. Re-copy the Webhook ID from PayPal and save.</td>
            </tr>
            <tr>
                <td><strong>Sandbox works but Live fails</strong></td>
                <td>You need separate Live credentials (Client ID, Secret, Webhook ID). Do not reuse Sandbox values for Live.</td>
            </tr>
        </tbody>
    </table>

    <div class="warning-box">
        <strong><i class="fas fa-shield-alt me-1"></i> Security:</strong> Treat your PayPal Client Secret like a password.
        Never share it via email, WhatsApp, or messaging. If you suspect it has been compromised, regenerate the secret in PayPal
        and update CommunityTix immediately.
    </div>

    {{-- ============================================================ --}}
    {{-- 11. GENERAL TIPS --}}
    {{-- ============================================================ --}}
    <h2 id="tips"><i class="fas fa-star me-2"></i>11. General Tips</h2>

    <h3>Workflow: Creating a New Event (Step by Step)</h3>
    <ol>
        <li><strong>Create the event</strong> &mdash; Go to Events, click "+ New Event". Fill in title, date, type, location, and description. Save as <strong>Draft</strong> initially.</li>
        <li><strong>Add ticket types</strong> (ticketed events) &mdash; After saving, scroll down and add your pricing tiers (Adult, Child, Family, etc.).</li>
        <li><strong>Enable PWYC</strong> (optional) &mdash; If you want flexible pricing, check "Enable Pay What You Can" and set suggested amounts.</li>
        <li><strong>Upload images</strong> &mdash; Add event photos and a flyer/poster to make the event page attractive.</li>
        <li><strong>Write a blog post</strong> (optional) &mdash; Announce the event on your blog with a link to the event page.</li>
        <li><strong>Publish</strong> &mdash; When everything looks good, change the event status to Published. It will immediately appear on your public website.</li>
        <li><strong>Share the link</strong> &mdash; Click "View Site" in the sidebar and navigate to your event to copy the URL. Share it via social media, email, or WhatsApp.</li>
    </ol>

    <h3>On the Day of an Event</h3>
    <ul>
        <li>Check the <strong>RSVPs</strong> page for free events or <strong>Orders</strong> page for ticketed events to see who's expected.</li>
        <li>Use the <strong>Cash Collections</strong> page to record any cash payments at the door.</li>
        <li>Use the <strong>Card at Door</strong> page to record card payments taken with a mobile card reader.</li>
        <li>After the event, review all your figures on the <strong>Events</strong> list page for a complete financial picture.</li>
    </ul>

    <h3>Exporting Data</h3>
    <p>Every major data page (Events, Orders, RSVPs, Cash, POS) has an <strong>Export</strong> button that downloads the
    current filtered view as a CSV file. You can open CSV files in Microsoft Excel, Google Sheets, or LibreOffice Calc.</p>

    <h3>Multi-language Content</h3>
    <p>CommunityTix supports content in any language. The editor and all text fields work with:</p>
    <ul>
        <li>Latin scripts (English, French, Spanish, etc.)</li>
        <li>Bengali, Hindi/Devanagari</li>
        <li>Chinese (Simplified), Japanese, Korean</li>
        <li>Arabic, Thai, and other Unicode scripts</li>
    </ul>
    <p>Simply type or paste your content in any language and it will display correctly on the website.</p>

    <h3>Need More Help?</h3>
    <p>If you encounter any issues or need further assistance, please contact your platform administrator or email
    <a href="mailto:{{ $tenant->contact_email ?? 'support@communitytix.org' }}">{{ $tenant->contact_email ?? 'support@communitytix.org' }}</a>.</p>

    <div class="mb-5"></div>
</div>

{{-- Lightbox --}}
<div class="help-lightbox" id="helpLightbox" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="Screenshot">
</div>

@endsection

@push('scripts')
<script>
function openLightbox(img) {
    document.getElementById('lightboxImg').src = img.src;
    document.getElementById('helpLightbox').classList.add('active');
}
function closeLightbox() {
    document.getElementById('helpLightbox').classList.remove('active');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush

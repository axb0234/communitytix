<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\EventImage;
use App\Models\HomeCarouselItem;
use App\Models\HomeContentBlock;
use App\Models\Member;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Rsvp;
use App\Models\Tenant;
use App\Models\TicketType;
use App\Services\TenantPurgeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SeedDemoContent extends Command
{
    protected $signature = 'demo:seed {--purge-first}';
    protected $description = 'Seed the demo tenant with sample content including 20 events';

    private Tenant $tenant;
    private string $uploadBase;

    private array $venues = [
        ['name' => 'Riverside Community Hall', 'address' => '42 Riverside Walk, Bristol BS1 6LR'],
        ['name' => 'St George\'s Bristol', 'address' => 'Great George Street, Bristol BS1 5RR'],
        ['name' => 'The Watershed', 'address' => '1 Canons Road, Bristol BS1 5TX'],
        ['name' => 'Arnolfini Arts Centre', 'address' => '16 Narrow Quay, Bristol BS1 4QA'],
        ['name' => 'Bristol Beacon', 'address' => 'Explore Lane, Bristol BS1 5TJ'],
    ];

    private array $fakeNames = [
        ['Sarah', 'Thompson'], ['James', 'Wilson'], ['Priya', 'Patel'],
        ['Michael', 'O\'Brien'], ['Aisha', 'Rahman'], ['David', 'Chen'],
        ['Emma', 'Garcia'], ['Kwame', 'Asante'], ['Lucy', 'Morgan'],
        ['Omar', 'Hassan'], ['Sophie', 'Taylor'], ['Raj', 'Sharma'],
        ['Hannah', 'Williams'], ['Marcus', 'Brown'], ['Fatima', 'Ali'],
        ['Tom', 'Davies'], ['Yuki', 'Tanaka'], ['Grace', 'Okafor'],
        ['Daniel', 'Martinez'], ['Amara', 'Diallo'],
    ];

    public function handle(): int
    {
        $this->tenant = Tenant::where('slug', 'demo')->first();
        if (!$this->tenant) {
            $this->error('Demo tenant not found. Run php artisan db:seed first.');
            return 1;
        }

        // Bind tenant to container
        app()->instance('current_tenant', $this->tenant);

        $this->uploadBase = storage_path("app/public/uploads/demo");

        if ($this->option('purge-first')) {
            $this->info('Purging existing demo data...');
            $purgeService = new TenantPurgeService();
            $stats = $purgeService->purge($this->tenant, keepGoverningUser: true);
            $this->info('Purged ' . array_sum($stats) . ' records.');
        }

        $this->seedLogo();
        $this->seedCarousel();
        $this->seedContentBlocks();
        $this->seedBlogPosts();
        $this->seedMembers();
        $this->seedEvents();

        $this->info('Demo content seeded successfully!');
        return 0;
    }

    private function seedLogo(): void
    {
        $this->info('Seeding logo...');
        $dir = "{$this->uploadBase}/logo";
        File::ensureDirectoryExists($dir);

        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60" viewBox="0 0 200 60">
  <rect width="200" height="60" rx="8" fill="#2d3748"/>
  <text x="100" y="38" font-family="Arial, sans-serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">RCC</text>
</svg>
SVG;

        $filename = 'logo.svg';
        File::put("{$dir}/{$filename}", $svg);

        $this->tenant->update(['logo_path' => "uploads/demo/logo/{$filename}"]);
    }

    private function seedCarousel(): void
    {
        $this->info('Seeding carousel...');
        $items = [
            ['caption' => 'Welcome to Riverside', 'subtitle' => 'A place for everyone in our community', 'seed' => 100],
            ['caption' => 'Exciting Events All Year', 'subtitle' => 'From festivals to workshops, there\'s always something on', 'seed' => 200],
            ['caption' => 'Join Our Community', 'subtitle' => 'Become a member and help shape our neighbourhood', 'seed' => 300],
        ];

        foreach ($items as $i => $item) {
            $imagePath = $this->downloadImage("carousel", "carousel-{$i}", $item['seed'], 1200, 400);

            HomeCarouselItem::withoutGlobalScopes()->create([
                'tenant_id' => $this->tenant->id,
                'image_path' => $imagePath,
                'caption' => $item['caption'],
                'subtitle' => $item['subtitle'],
                'link_url' => $i === 1 ? '/events' : ($i === 2 ? '/join' : null),
                'sort_order' => $i,
                'active' => true,
            ]);
        }
    }

    private function seedContentBlocks(): void
    {
        $this->info('Seeding content blocks...');
        $blocks = [
            [
                'title' => 'Our Community',
                'body_html' => '<p>Riverside Community Centre has been at the heart of our neighbourhood for over 30 years. We provide a welcoming space for people of all ages and backgrounds to come together, learn, and celebrate.</p>',
                'icon' => 'fas fa-heart',
            ],
            [
                'title' => 'Upcoming Events',
                'body_html' => '<p>From music festivals and food nights to workshops and family fun days, we host a diverse range of events throughout the year. Check our events page to find something you\'ll love.</p>',
                'icon' => 'fas fa-calendar-alt',
            ],
            [
                'title' => 'Get Involved',
                'body_html' => '<p>Whether you want to volunteer, join a class, or help organise events, there are many ways to get involved. Our community thrives because of people like you.</p>',
                'icon' => 'fas fa-hands-helping',
            ],
        ];

        foreach ($blocks as $i => $block) {
            HomeContentBlock::withoutGlobalScopes()->create([
                'tenant_id' => $this->tenant->id,
                'title' => $block['title'],
                'body_html' => $block['body_html'],
                'icon' => $block['icon'],
                'sort_order' => $i,
                'active' => true,
            ]);
        }
    }

    private function seedBlogPosts(): void
    {
        $this->info('Seeding blog posts...');

        $governingMember = Member::withoutGlobalScopes()
            ->where('tenant_id', $this->tenant->id)
            ->where('member_type', 'GOVERNING')
            ->first();
        $authorId = $governingMember?->user_id;

        $posts = [
            [
                'title' => 'Welcome to Our New Website',
                'slug' => 'welcome-to-our-new-website',
                'excerpt' => 'We are thrilled to launch our brand new community website, making it easier than ever to stay connected.',
                'body_html' => '<h3>A New Chapter for Riverside</h3><p>After months of planning and development, we\'re excited to unveil our new website! This platform will make it easier for you to find events, book tickets, sign up as a member, and stay up to date with everything happening at Riverside Community Centre.</p><h4>What You Can Do</h4><ul><li>Browse and book events online</li><li>RSVP to free community gatherings</li><li>Read our blog for the latest news</li><li>Sign up as a community member</li></ul><p>We hope you enjoy using the site. If you have any feedback, please don\'t hesitate to get in touch!</p>',
                'published_at' => '2026-01-05 10:00:00',
                'seed' => 400,
            ],
            [
                'title' => 'Volunteer Spotlight: Meet Our Amazing Team',
                'slug' => 'volunteer-spotlight-meet-our-amazing-team',
                'excerpt' => 'Our volunteers are the backbone of everything we do. Meet some of the incredible people who make it all happen.',
                'body_html' => '<h3>The Heart of Our Community</h3><p>Behind every successful event, every well-maintained facility, and every warm welcome, there\'s a team of dedicated volunteers. This month, we want to shine a spotlight on some of the incredible people who give their time and energy to make Riverside Community Centre a special place.</p><h4>Why Volunteer?</h4><p>Volunteering at Riverside isn\'t just about giving back — it\'s about building friendships, learning new skills, and being part of something meaningful. Whether it\'s setting up chairs for an event, serving food at a community dinner, or helping with admin, every contribution matters.</p><p>If you\'re interested in volunteering, drop by the centre or send us a message. We\'d love to have you on board!</p>',
                'published_at' => '2026-01-20 14:00:00',
                'seed' => 500,
            ],
            [
                'title' => 'Summer Festival 2026: Save the Date!',
                'slug' => 'summer-festival-2026-save-the-date',
                'excerpt' => 'Mark your calendars! The Riverside Summer Festival returns on July 18th with live music, food, and family fun.',
                'body_html' => '<h3>Our Biggest Event of the Year</h3><p>The Riverside Summer Festival is back and bigger than ever! Join us on <strong>Saturday 18th July 2026</strong> for a day packed with entertainment, delicious food, and activities for the whole family.</p><h4>What to Expect</h4><ul><li>Live music from local bands and performers</li><li>Street food stalls featuring cuisines from around the world</li><li>Children\'s activities and bouncy castle</li><li>Craft market with local artisans</li><li>Community art exhibition</li></ul><h4>Tickets</h4><p>Early bird tickets will be available soon. Keep an eye on our events page for more details. We also offer Pay What You Can pricing to ensure everyone can enjoy the festival!</p>',
                'published_at' => '2026-02-10 09:00:00',
                'seed' => 600,
            ],
            [
                'title' => 'Introducing Pay What You Can Pricing',
                'slug' => 'introducing-pay-what-you-can-pricing',
                'excerpt' => 'We believe everyone should have access to community events. That\'s why we\'re introducing Pay What You Can pricing.',
                'body_html' => '<h3>Community Events for Everyone</h3><p>At Riverside Community Centre, we believe that financial barriers should never prevent anyone from participating in community life. That\'s why we\'re excited to introduce our <strong>Pay What You Can (PWYC)</strong> pricing option for select events.</p><h4>How It Works</h4><p>For events marked with PWYC, you\'ll see suggested donation amounts when booking. You can choose one of our suggested amounts or enter any amount that works for your budget. Every contribution helps us keep our doors open and our events running.</p><h4>Why PWYC?</h4><ul><li>Makes events accessible to everyone regardless of income</li><li>Builds trust between the community and our centre</li><li>Allows those who can afford more to support those who can\'t</li><li>Keeps our community inclusive and welcoming</li></ul><p>Look for the PWYC badge on our events page. We hope this makes it easier for everyone to join in!</p>',
                'published_at' => '2026-02-15 11:00:00',
                'seed' => 700,
            ],
        ];

        foreach ($posts as $post) {
            $imagePath = $this->downloadImage("blog", $post['slug'], $post['seed'], 800, 400);

            BlogPost::withoutGlobalScopes()->create([
                'tenant_id' => $this->tenant->id,
                'author_id' => $authorId,
                'title' => $post['title'],
                'slug' => $post['slug'],
                'featured_image' => $imagePath,
                'excerpt' => $post['excerpt'],
                'body_html' => $post['body_html'],
                'status' => 'published',
                'published_at' => $post['published_at'],
            ]);
        }
    }

    private function seedMembers(): void
    {
        $this->info('Seeding members...');
        $types = ['ORDINARY', 'GUEST'];
        $statuses = ['ACTIVE', 'ACTIVE', 'ACTIVE', 'PENDING_APPROVAL'];

        foreach ($this->fakeNames as $i => [$first, $last]) {
            Member::withoutGlobalScopes()->create([
                'tenant_id' => $this->tenant->id,
                'member_type' => $types[$i % count($types)],
                'status' => $statuses[$i % count($statuses)],
                'first_name' => $first,
                'last_name' => $last,
                'email' => Str::lower($first) . '.' . Str::lower(str_replace("'", '', $last)) . '@example.com',
                'phone' => '07' . str_pad((string) ($i * 111 + 700000000), 9, '0', STR_PAD_LEFT),
            ]);
        }
    }

    private function seedEvents(): void
    {
        $this->info('Seeding 20 events...');

        $events = [
            [
                'title' => 'New Year Community Gathering',
                'date' => '2026-01-17 14:00',
                'end' => '2026-01-17 17:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'Start the new year right with your neighbours! Free refreshments, live acoustic music, and a warm welcome for all.',
                'body' => '<h3>Ring in the New Year Together</h3><p>Join us for a relaxed afternoon of community spirit as we kick off 2026 together. Whether you\'re a long-time resident or new to the area, this gathering is for you.</p><h4>What\'s On</h4><ul><li>Free tea, coffee, and homemade cakes</li><li>Live acoustic music from local musicians</li><li>Children\'s craft corner</li><li>Community noticeboard for 2026 plans</li></ul><p>No booking required — just turn up and enjoy!</p>',
                'capacity' => 80,
                'tickets' => [],
            ],
            [
                'title' => 'Valentine\'s Charity Concert',
                'date' => '2026-02-14 19:30',
                'end' => '2026-02-14 22:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 4,
                'short' => 'An evening of love songs and classical music to raise funds for local charities. Includes a glass of prosecco.',
                'body' => '<h3>An Evening of Romance and Giving</h3><p>Celebrate Valentine\'s Day with a beautiful evening of live music, performed by talented local musicians. All proceeds go to Bristol-based charities supporting families in need.</p><h4>Programme</h4><ul><li>Classical string quartet performing love songs through the ages</li><li>Solo vocal performances of jazz standards</li><li>Interval with prosecco and canapes</li></ul><h4>Details</h4><p>Doors open at 7pm. The concert begins at 7:30pm sharp. Smart casual dress code. Each ticket includes a complimentary glass of prosecco.</p><p>This is a fundraising event — all ticket revenue goes directly to our partner charities.</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult', 'price' => 15.00, 'capacity' => 120],
                    ['name' => 'Concession', 'price' => 10.00, 'capacity' => 40],
                ],
            ],
            [
                'title' => 'International Women\'s Day Workshop',
                'date' => '2026-03-08 10:00',
                'end' => '2026-03-08 16:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'A day of workshops, talks, and celebration honouring women in our community and beyond.',
                'body' => '<h3>Celebrating Women in Our Community</h3><p>Join us for a full day of empowering workshops, inspiring talks, and creative activities to mark International Women\'s Day 2026.</p><h4>Schedule</h4><ul><li><strong>10:00-11:30</strong> — Panel discussion: Women Leading Change in Bristol</li><li><strong>11:30-12:00</strong> — Coffee and networking</li><li><strong>12:00-13:00</strong> — Creative writing workshop</li><li><strong>13:00-14:00</strong> — Lunch (provided)</li><li><strong>14:00-15:30</strong> — Self-defence taster session</li><li><strong>15:30-16:00</strong> — Closing ceremony and group photo</li></ul><p>This event is open to all women and non-binary people. Childcare is available — please let us know when you RSVP.</p>',
                'capacity' => 60,
                'tickets' => [],
            ],
            [
                'title' => 'Spring Equinox Nature Walk',
                'date' => '2026-03-21 09:30',
                'end' => '2026-03-21 12:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'Celebrate the first day of spring with a guided nature walk along the riverbank. Suitable for all ages.',
                'body' => '<h3>Welcome Spring on the Riverside</h3><p>Celebrate the spring equinox with a gentle guided walk along the beautiful riverside paths. Our local nature guide will point out early spring wildlife, budding trees, and seasonal changes.</p><h4>Walk Details</h4><ul><li>Meet at Riverside Community Hall at 9:30am</li><li>Approximately 3km walk on flat paths</li><li>Suitable for pushchairs and wheelchairs</li><li>Hot drinks provided at the end</li></ul><h4>What to Bring</h4><p>Wear comfortable shoes and dress for the weather. Binoculars optional but recommended! Dogs welcome on leads.</p>',
                'capacity' => 30,
                'tickets' => [],
            ],
            [
                'title' => 'Easter Family Fun Day',
                'date' => '2026-04-11 11:00',
                'end' => '2026-04-11 16:00',
                'type' => 'TICKETED',
                'pwyw' => true,
                'pwyw_amounts' => [5.00, 10.00, 20.00],
                'venue' => 0,
                'short' => 'Egg hunts, face painting, bouncy castle, and craft workshops. A fun-filled day for the whole family!',
                'body' => '<h3>Easter Fun for All the Family</h3><p>Bring the whole family for a fantastic day of Easter-themed activities and entertainment. With something for every age group, this is the perfect way to celebrate the Easter holidays.</p><h4>Activities</h4><ul><li>Easter egg hunt around the grounds (age-grouped)</li><li>Face painting and glitter tattoos</li><li>Bouncy castle and soft play area</li><li>Easter bonnet decorating workshop</li><li>Cake decorating station</li><li>Live entertainment from local children\'s performers</li></ul><h4>Food & Drink</h4><p>Hot food stall with burgers, hot dogs, and veggie options. Cake sale in aid of the community centre. Tea, coffee, and soft drinks available.</p><p><strong>Pay What You Can:</strong> We want every family to enjoy Easter fun regardless of budget. Choose an amount that works for you!</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Family (2 adults + 2 children)', 'price' => 12.00, 'capacity' => 50],
                    ['name' => 'Adult', 'price' => 5.00, 'capacity' => 100],
                    ['name' => 'Child (under 12)', 'price' => 3.00, 'capacity' => 100],
                ],
            ],
            [
                'title' => 'Community Cooking Class: Indian Street Food',
                'date' => '2026-04-25 18:00',
                'end' => '2026-04-25 21:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'Learn to cook authentic Indian street food with our resident chef. All ingredients provided. Eat what you make!',
                'body' => '<h3>Master the Art of Indian Street Food</h3><p>Join our popular cooking class series with chef Priya, who will guide you through making three classic Indian street food dishes from scratch.</p><h4>On the Menu</h4><ul><li><strong>Pani Puri</strong> — crispy shells filled with spiced water and chutneys</li><li><strong>Aloo Tikki Chaat</strong> — spiced potato patties with yoghurt and tamarind</li><li><strong>Masala Dosa</strong> — crispy crepe with spiced potato filling</li></ul><h4>What\'s Included</h4><p>All ingredients and equipment provided. You\'ll take home recipe cards and enjoy eating everything you make! Suitable for beginners. Vegetarian menu — vegan options available on request.</p><p>Spaces are limited to 16 participants to ensure hands-on learning.</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'General Admission', 'price' => 25.00, 'capacity' => 16],
                ],
            ],
            [
                'title' => 'Local Artists Exhibition Opening',
                'date' => '2026-05-09 17:00',
                'end' => '2026-05-09 20:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 3,
                'short' => 'Opening night of our annual local artists exhibition. Meet the artists, enjoy wine and nibbles, and discover new talent.',
                'body' => '<h3>Discover Bristol\'s Creative Talent</h3><p>We\'re proud to host our annual exhibition showcasing the work of over 20 local artists. The opening night is your chance to meet the creators, enjoy complimentary wine and nibbles, and be the first to see this year\'s collection.</p><h4>Featured Work</h4><ul><li>Oil and watercolour paintings</li><li>Photography and digital art</li><li>Sculpture and ceramics</li><li>Mixed media and textile art</li></ul><h4>Exhibition Details</h4><p>The exhibition runs for two weeks after opening night. Free entry throughout. Many pieces are available for purchase, with a percentage supporting the community centre.</p>',
                'capacity' => 100,
                'tickets' => [],
            ],
            [
                'title' => 'Spring Music Festival',
                'date' => '2026-05-23 12:00',
                'end' => '2026-05-23 22:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 1,
                'short' => 'A full day of live music across two stages. Featuring local bands, solo artists, and special guests.',
                'body' => '<h3>A Day of Music and Celebration</h3><p>Our Spring Music Festival brings together some of the best musical talent in Bristol and beyond for a full day of live performances across two stages.</p><h4>Line-up Highlights</h4><ul><li><strong>Main Stage:</strong> The Harbour Lights, Southville Sound, DJ Phoenix</li><li><strong>Acoustic Stage:</strong> Singer-songwriters, folk acts, and spoken word</li><li><strong>12:00-14:00:</strong> Family-friendly sets</li><li><strong>14:00-18:00:</strong> Afternoon sessions</li><li><strong>18:00-22:00:</strong> Evening headliners</li></ul><h4>Food & Drink</h4><p>Street food vendors, craft beer bar, cocktails, and soft drinks. Bring a blanket and enjoy the music under the spring sky!</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Early Bird', 'price' => 12.00, 'capacity' => 50],
                    ['name' => 'Standard', 'price' => 18.00, 'capacity' => 150],
                    ['name' => 'Under 16', 'price' => 8.00, 'capacity' => 50],
                ],
            ],
            [
                'title' => 'Caribbean Food & Culture Night',
                'date' => '2026-06-13 18:00',
                'end' => '2026-06-13 23:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'An evening celebrating Caribbean culture with authentic food, live reggae, steel pan music, and dancing.',
                'body' => '<h3>A Taste of the Caribbean in Bristol</h3><p>Experience the vibrant flavours, rhythms, and warmth of Caribbean culture at this popular annual event. Featuring authentic food cooked by members of our Caribbean community, live music, and plenty of dancing!</p><h4>What\'s On</h4><ul><li><strong>18:00-19:00:</strong> Welcome drinks and steel pan music</li><li><strong>19:00-20:30:</strong> Caribbean buffet dinner</li><li><strong>20:30-23:00:</strong> Live reggae band and dancing</li></ul><h4>Menu</h4><p>The buffet includes jerk chicken, curry goat, rice and peas, fried plantain, festival dumplings, and rum punch. Vegetarian and vegan options available including callaloo and ackee.</p><p>This event sells out every year — book early to avoid disappointment!</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult (includes dinner)', 'price' => 22.00, 'capacity' => 80],
                    ['name' => 'Child (includes dinner)', 'price' => 10.00, 'capacity' => 30],
                ],
            ],
            [
                'title' => 'Midsummer Community Sports Day',
                'date' => '2026-06-27 10:00',
                'end' => '2026-06-27 16:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'A classic sports day with egg-and-spoon races, tug of war, rounders, and more. Fun for all ages!',
                'body' => '<h3>Get Active This Summer!</h3><p>Our annual Community Sports Day is back with classic games, friendly competition, and lots of laughs. Whether you\'re a serious athlete or just fancy a go at the sack race, everyone is welcome.</p><h4>Events</h4><ul><li>Egg and spoon race (kids and adults)</li><li>Sack race</li><li>Tug of war (team sign-up on the day)</li><li>Rounders tournament</li><li>100m dash (age categories)</li><li>Obstacle course</li></ul><h4>Facilities</h4><p>Free BBQ for all participants and spectators. First aid station on site. Bring sunscreen and water! Medals and trophies for winners in each category.</p>',
                'capacity' => 150,
                'tickets' => [],
            ],
            [
                'title' => 'Riverside Summer Festival 2026',
                'date' => '2026-07-18 11:00',
                'end' => '2026-07-18 22:00',
                'type' => 'TICKETED',
                'pwyw' => true,
                'pwyw_amounts' => [5.00, 10.00, 20.00],
                'venue' => 0,
                'short' => 'Our flagship annual festival! Live music, world food, craft market, kids\' zone, and community art. The highlight of the summer.',
                'body' => '<h3>The Biggest Community Event of the Year</h3><p>The Riverside Summer Festival is our flagship event, bringing together everything we love about our community in one incredible day. Now in its 12th year, the festival attracts over 500 visitors and showcases the very best of Bristol\'s diverse culture.</p><h4>What to Expect</h4><ul><li><strong>Main Stage:</strong> Live bands performing throughout the day</li><li><strong>World Food Village:</strong> 15+ food stalls representing cuisines from around the globe</li><li><strong>Craft Market:</strong> Handmade goods from local artisans</li><li><strong>Kids\' Zone:</strong> Bouncy castle, face painting, puppet shows, and craft workshops</li><li><strong>Community Art Wall:</strong> Add your mark to our collaborative mural</li><li><strong>Wellness Corner:</strong> Free yoga, meditation, and massage tasters</li></ul><h4>Schedule</h4><ul><li><strong>11:00:</strong> Gates open, family activities begin</li><li><strong>12:00-14:00:</strong> Afternoon music sets</li><li><strong>14:00-15:00:</strong> Community awards ceremony</li><li><strong>15:00-18:00:</strong> Main stage performances</li><li><strong>18:00-22:00:</strong> Evening headliners and DJ set</li></ul><p><strong>Pay What You Can:</strong> We believe community events should be accessible to all. Choose an amount that works for you!</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult', 'price' => 10.00, 'capacity' => 300],
                    ['name' => 'Child (5-15)', 'price' => 5.00, 'capacity' => 150],
                    ['name' => 'Family (2+2)', 'price' => 25.00, 'capacity' => 80],
                    ['name' => 'Under 5', 'price' => 0.00, 'capacity' => null],
                ],
            ],
            [
                'title' => 'Open Air Cinema Night',
                'date' => '2026-08-08 19:30',
                'end' => '2026-08-08 23:00',
                'type' => 'TICKETED',
                'pwyw' => true,
                'pwyw_amounts' => [3.00, 7.00, 15.00],
                'venue' => 0,
                'short' => 'Watch a classic film under the stars in our garden. Bring a blanket and enjoy cinema magic outdoors.',
                'body' => '<h3>Cinema Under the Stars</h3><p>Grab a blanket, some popcorn, and settle in for a magical evening of outdoor cinema in the community garden. We\'ll be screening a beloved family classic (film TBA — vote on our social media!).</p><h4>Details</h4><ul><li><strong>19:30:</strong> Gates open, grab your spot</li><li><strong>20:00:</strong> Pre-show entertainment and short film</li><li><strong>20:30:</strong> Main feature begins (sunset screening)</li></ul><h4>What\'s Available</h4><p>Popcorn, snacks, and drinks available to purchase. Hot chocolate and blankets available for a small donation. Bring your own camping chairs, bean bags, or blankets!</p><p>In case of rain, the screening will move indoors to the main hall. No refunds, but we\'ll make sure it\'s just as magical inside!</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult', 'price' => 8.00, 'capacity' => 100],
                    ['name' => 'Child', 'price' => 4.00, 'capacity' => 50],
                ],
            ],
            [
                'title' => 'Cultural Heritage Day',
                'date' => '2026-08-22 10:00',
                'end' => '2026-08-22 17:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'Celebrating the diverse cultural heritage of our community. Traditional dress, music, food, and storytelling.',
                'body' => '<h3>A Celebration of Our Diverse Community</h3><p>Cultural Heritage Day is a celebration of the many cultures that make up our vibrant community. Join us for a day of sharing, learning, and celebrating our differences and our shared humanity.</p><h4>Programme</h4><ul><li><strong>10:00-12:00:</strong> Cultural displays and exhibitions</li><li><strong>12:00-13:00:</strong> International food tasting</li><li><strong>13:00-14:30:</strong> Traditional music and dance performances</li><li><strong>14:30-16:00:</strong> Storytelling from around the world</li><li><strong>16:00-17:00:</strong> Community dialogue and closing ceremony</li></ul><p>We invite community members to share their heritage through food, dress, music, art, or stories. If you\'d like to participate, please contact us in advance.</p>',
                'capacity' => 200,
                'tickets' => [],
            ],
            [
                'title' => 'Harvest Supper & Barn Dance',
                'date' => '2026-09-12 18:30',
                'end' => '2026-09-12 23:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'A traditional harvest supper followed by a lively barn dance with a live ceilidh band. No experience needed!',
                'body' => '<h3>Celebrate the Harvest Season</h3><p>Our annual Harvest Supper brings the community together to celebrate the season with delicious locally-sourced food and a lively barn dance. The ceilidh band will call all the moves, so no dancing experience needed!</p><h4>Evening Schedule</h4><ul><li><strong>18:30:</strong> Doors open, welcome drink</li><li><strong>19:00-20:00:</strong> Three-course harvest supper</li><li><strong>20:00-23:00:</strong> Barn dance with live ceilidh band</li></ul><h4>Menu</h4><p>Seasonal soup, roast dinner with all the trimmings (veggie option available), and apple crumble with custard. All ingredients sourced from local farms and allotments where possible.</p><p>Bring your dancing shoes and an appetite!</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult (includes supper)', 'price' => 18.00, 'capacity' => 80],
                    ['name' => 'Child (includes supper)', 'price' => 8.00, 'capacity' => 30],
                ],
            ],
            [
                'title' => 'Coding for Kids Workshop',
                'date' => '2026-09-27 10:00',
                'end' => '2026-09-27 15:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 2,
                'short' => 'Introduction to coding for 8-14 year olds using Scratch. Build your own game in a day! Laptops provided.',
                'body' => '<h3>Build Your Own Game!</h3><p>This hands-on workshop introduces young people aged 8-14 to the world of coding using Scratch, a beginner-friendly programming language developed by MIT. By the end of the day, every participant will have built their own interactive game.</p><h4>Workshop Details</h4><ul><li><strong>10:00-10:30:</strong> Introduction to coding concepts</li><li><strong>10:30-12:00:</strong> Building your first Scratch project</li><li><strong>12:00-13:00:</strong> Lunch break (packed lunch required)</li><li><strong>13:00-14:30:</strong> Creating your own game</li><li><strong>14:30-15:00:</strong> Show and tell — demo your game!</li></ul><h4>Requirements</h4><p>No prior coding experience needed. All laptops and materials provided. Please bring a packed lunch. Parents are welcome to stay or drop off (DBS-checked supervisors on site).</p>',
                'capacity' => 20,
                'tickets' => [],
            ],
            [
                'title' => 'Autumn Craft Market',
                'date' => '2026-10-10 10:00',
                'end' => '2026-10-10 16:00',
                'type' => 'FREE',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'Browse handmade crafts, artisan food, and unique gifts from over 30 local stallholders. Free entry!',
                'body' => '<h3>Handmade, Local, Unique</h3><p>Our Autumn Craft Market brings together over 30 talented local makers and artisan food producers under one roof. Whether you\'re looking for unique gifts, home decorations, or just a lovely day out, you\'ll find it here.</p><h4>What You\'ll Find</h4><ul><li>Handmade jewellery and accessories</li><li>Pottery and ceramics</li><li>Candles and soaps</li><li>Artisan bread, preserves, and cakes</li><li>Woodwork and upcycled furniture</li><li>Knitwear and textiles</li></ul><h4>Additional Activities</h4><p>Live demonstrations throughout the day. Children\'s craft table. Cafe serving homemade food and drinks. Free entry — just come along and enjoy!</p>',
                'capacity' => 300,
                'tickets' => [],
            ],
            [
                'title' => 'Halloween Spooktacular',
                'date' => '2026-10-31 16:00',
                'end' => '2026-10-31 21:00',
                'type' => 'TICKETED',
                'pwyw' => true,
                'pwyw_amounts' => [5.00, 10.00, 15.00],
                'venue' => 0,
                'short' => 'A frighteningly good time! Haunted house, costume contest, pumpkin carving, and spooky disco.',
                'body' => '<h3>A Frighteningly Good Time!</h3><p>Our Halloween Spooktacular is the spookiest event of the year! Come in costume and enjoy an evening of family-friendly frights, creative activities, and dancing.</p><h4>Schedule</h4><ul><li><strong>16:00-18:00:</strong> Family-friendly activities (younger children)</li><li><strong>16:00:</strong> Pumpkin carving station</li><li><strong>17:00:</strong> Costume parade and contest</li><li><strong>18:00-19:00:</strong> Haunted house experience (ages 8+)</li><li><strong>19:00-21:00:</strong> Spooky disco with DJ</li></ul><h4>Costume Contest Categories</h4><ul><li>Scariest costume</li><li>Most creative</li><li>Best family/group costume</li><li>Cutest (under 5s)</li></ul><p>Prizes for winners in each category! Toffee apples and Halloween treats available.</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult', 'price' => 6.00, 'capacity' => 120],
                    ['name' => 'Child (3-15)', 'price' => 4.00, 'capacity' => 80],
                    ['name' => 'Family (2+3)', 'price' => 18.00, 'capacity' => 40],
                ],
            ],
            [
                'title' => 'World Food Festival',
                'date' => '2026-11-14 12:00',
                'end' => '2026-11-14 20:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'A culinary journey around the world with food stalls, cooking demos, and live music from every continent.',
                'body' => '<h3>Taste the World Without Leaving Bristol</h3><p>Our World Food Festival celebrates the incredible culinary diversity of our community. With food stalls representing cuisines from every continent, live cooking demonstrations, and music from around the globe, this is a feast for all the senses.</p><h4>Food Stalls</h4><ul><li>West African — jollof rice, suya, and puff puff</li><li>South Asian — biryani, samosas, and chai</li><li>Middle Eastern — falafel, hummus, and baklava</li><li>East Asian — dumplings, bao buns, and bubble tea</li><li>Latin American — tacos, empanadas, and churros</li><li>European — pierogis, crepes, and gelato</li></ul><h4>Entertainment</h4><p>Live music stage featuring world music. Cooking demonstrations from community chefs. Kids\' world map activity trail with stamps from each stall.</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult Entry', 'price' => 5.00, 'capacity' => 200],
                    ['name' => 'Child Entry', 'price' => 2.00, 'capacity' => 100],
                    ['name' => 'Tasting Passport (10 samples)', 'price' => 15.00, 'capacity' => 80],
                ],
            ],
            [
                'title' => 'Winter Wonderland Concert',
                'date' => '2026-12-05 19:00',
                'end' => '2026-12-05 21:30',
                'type' => 'TICKETED',
                'pwyw' => true,
                'pwyw_amounts' => [5.00, 15.00, 25.00],
                'venue' => 4,
                'short' => 'A festive evening of carols, choral music, and seasonal songs. Mulled wine and mince pies included.',
                'body' => '<h3>A Magical Evening of Festive Music</h3><p>Get into the Christmas spirit with our annual Winter Wonderland Concert. Featuring the Riverside Community Choir, local soloists, and a brass ensemble performing festive favourites old and new.</p><h4>Programme</h4><ul><li>Traditional carols with audience sing-along</li><li>Choral arrangements of seasonal classics</li><li>Brass ensemble performing winter melodies</li><li>Solo performances of contemporary Christmas songs</li><li>Candlelit finale</li></ul><h4>Included</h4><p>Every ticket includes a mulled wine (or hot chocolate) and a homemade mince pie. The venue will be beautifully decorated with festive lights and candles.</p><p><strong>Pay What You Can:</strong> Music and festive cheer should be for everyone. Choose what you can afford.</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult', 'price' => 12.00, 'capacity' => 200],
                    ['name' => 'Child', 'price' => 5.00, 'capacity' => 50],
                    ['name' => 'Family (2+2)', 'price' => 28.00, 'capacity' => 50],
                ],
            ],
            [
                'title' => 'Christmas Community Dinner',
                'date' => '2026-12-19 17:00',
                'end' => '2026-12-19 21:00',
                'type' => 'TICKETED',
                'pwyw' => false,
                'venue' => 0,
                'short' => 'A festive three-course dinner bringing the community together before Christmas. Crackers, carols, and good cheer!',
                'body' => '<h3>Celebrate Christmas Together</h3><p>Our annual Christmas Community Dinner is one of the most cherished events in our calendar. Join your neighbours for a wonderful evening of festive food, crackers, carols, and community spirit.</p><h4>Menu</h4><ul><li><strong>Starter:</strong> Roasted butternut squash soup with crusty bread</li><li><strong>Main:</strong> Traditional roast turkey with all the trimmings (vegetarian Wellington option)</li><li><strong>Dessert:</strong> Christmas pudding with brandy sauce or chocolate yule log</li></ul><h4>Entertainment</h4><ul><li>Christmas crackers and party hats at every place</li><li>Carol singing led by the community choir</li><li>Secret Santa table — bring a wrapped gift (under 5 pounds) to exchange!</li><li>Christmas quiz with prizes</li></ul><p>This event always sells out, so book early! A limited number of free places are available for those in financial hardship — please contact us in confidence.</p>',
                'capacity' => null,
                'tickets' => [
                    ['name' => 'Adult (3-course dinner)', 'price' => 20.00, 'capacity' => 80],
                    ['name' => 'Child (3-course dinner)', 'price' => 10.00, 'capacity' => 30],
                    ['name' => 'Concession', 'price' => 14.00, 'capacity' => 20],
                ],
            ],
        ];

        foreach ($events as $i => $eventData) {
            $venue = $this->venues[$eventData['venue']];
            $slug = Str::slug($eventData['title']);

            $imagePath = $this->downloadImage("events/flyers", $slug, 1000 + $i, 800, 500);

            $event = Event::withoutGlobalScopes()->create([
                'tenant_id' => $this->tenant->id,
                'title' => $eventData['title'],
                'slug' => $slug,
                'start_at' => $eventData['date'],
                'end_at' => $eventData['end'],
                'location' => $venue['name'],
                'location_address' => $venue['address'],
                'event_type' => $eventData['type'],
                'status' => 'published',
                'published_at' => now()->subDays(30 - $i),
                'flyer_path' => $imagePath,
                'body_html' => $eventData['body'],
                'short_description' => $eventData['short'],
                'rsvp_capacity' => $eventData['capacity'],
                'pwyw_enabled' => $eventData['pwyw'] ?? false,
                'pwyw_amount_1' => $eventData['pwyw_amounts'][0] ?? null,
                'pwyw_amount_2' => $eventData['pwyw_amounts'][1] ?? null,
                'pwyw_amount_3' => $eventData['pwyw_amounts'][2] ?? null,
            ]);

            // Create ticket types for ticketed events
            foreach ($eventData['tickets'] as $sortOrder => $ticket) {
                TicketType::withoutGlobalScopes()->create([
                    'tenant_id' => $this->tenant->id,
                    'event_id' => $event->id,
                    'name' => $ticket['name'],
                    'price' => $ticket['price'],
                    'capacity' => $ticket['capacity'],
                    'sort_order' => $sortOrder,
                    'active' => true,
                ]);
            }

            // Seed past event data (RSVPs for FREE, Orders for TICKETED)
            $eventDate = \Carbon\Carbon::parse($eventData['date']);
            if ($eventDate->isPast()) {
                $this->seedPastEventData($event, $eventData);
            }
        }
    }

    private function seedPastEventData(Event $event, array $eventData): void
    {
        $namePool = $this->fakeNames;
        shuffle($namePool);

        if ($event->isFree()) {
            // Seed RSVPs for past free events
            $count = rand(5, 15);
            for ($j = 0; $j < $count && $j < count($namePool); $j++) {
                [$first, $last] = $namePool[$j];
                Rsvp::withoutGlobalScopes()->create([
                    'tenant_id' => $this->tenant->id,
                    'event_id' => $event->id,
                    'name' => "{$first} {$last}",
                    'email' => Str::lower($first) . '.' . Str::lower(str_replace("'", '', $last)) . '@example.com',
                    'guests' => rand(1, 3),
                    'status' => 'CONFIRMED',
                ]);
            }
        } else {
            // Seed Orders for past ticketed events
            $ticketTypes = TicketType::withoutGlobalScopes()
                ->where('event_id', $event->id)
                ->get();

            if ($ticketTypes->isEmpty()) return;

            $orderCount = rand(5, 10);
            for ($j = 0; $j < $orderCount && $j < count($namePool); $j++) {
                [$first, $last] = $namePool[$j];
                $email = Str::lower($first) . '.' . Str::lower(str_replace("'", '', $last)) . '@example.com';

                $orderItems = [];
                $total = 0;

                // Pick 1-2 ticket types
                $selectedTypes = $ticketTypes->random(min(rand(1, 2), $ticketTypes->count()));
                foreach ($selectedTypes as $tt) {
                    $qty = rand(1, 3);
                    $lineTotal = $tt->price * $qty;
                    $total += $lineTotal;
                    $orderItems[] = [
                        'ticket_type_id' => $tt->id,
                        'qty' => $qty,
                        'unit_price' => $tt->price,
                    ];
                }

                // Add PWYC item for some orders on PWYC events
                if (($eventData['pwyw'] ?? false) && rand(0, 1)) {
                    $pwywAmount = $eventData['pwyw_amounts'][array_rand($eventData['pwyw_amounts'])];
                    $total += $pwywAmount;
                    $orderItems[] = [
                        'ticket_type_id' => null,
                        'qty' => 1,
                        'unit_price' => $pwywAmount,
                    ];
                }

                $paidAt = \Carbon\Carbon::parse($eventData['date'])->subDays(rand(1, 14));

                $order = Order::withoutGlobalScopes()->create([
                    'tenant_id' => $this->tenant->id,
                    'event_id' => $event->id,
                    'order_number' => Order::generateOrderNumber(),
                    'purchaser_name' => "{$first} {$last}",
                    'purchaser_email' => $email,
                    'status' => 'COMPLETED',
                    'total_amount' => $total,
                    'currency' => $this->tenant->currency,
                    'payment_method' => 'PAYPAL',
                    'provider_order_id' => 'DEMO-' . Str::upper(Str::random(12)),
                    'provider_capture_id' => 'DEMO-' . Str::upper(Str::random(12)),
                    'paid_at' => $paidAt,
                ]);

                foreach ($orderItems as $item) {
                    OrderItem::withoutGlobalScopes()->create([
                        'tenant_id' => $this->tenant->id,
                        'order_id' => $order->id,
                        'ticket_type_id' => $item['ticket_type_id'],
                        'qty' => $item['qty'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            }
        }
    }

    private function downloadImage(string $folder, string $name, int $seed, int $width, int $height): string
    {
        $dir = "{$this->uploadBase}/{$folder}";
        File::ensureDirectoryExists($dir);

        $filename = Str::slug($name) . '.jpg';
        $filepath = "{$dir}/{$filename}";

        // Try to download from picsum.photos
        try {
            $response = Http::timeout(10)->get("https://picsum.photos/seed/{$seed}/{$width}/{$height}");
            if ($response->successful()) {
                File::put($filepath, $response->body());
                return "uploads/demo/{$folder}/{$filename}";
            }
        } catch (\Exception $e) {
            // Fall through to SVG fallback
        }

        // SVG fallback
        $svgFilename = Str::slug($name) . '.svg';
        $svgPath = "{$dir}/{$svgFilename}";
        $hue = ($seed * 37) % 360;
        $label = Str::title(str_replace('-', ' ', $name));
        if (strlen($label) > 30) $label = substr($label, 0, 30) . '...';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect width="{$width}" height="{$height}" fill="hsl({$hue}, 40%, 50%)"/>
  <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="24" fill="white" text-anchor="middle" dy=".3em">{$label}</text>
</svg>
SVG;

        File::put($svgPath, $svg);
        return "uploads/demo/{$folder}/{$svgFilename}";
    }
}

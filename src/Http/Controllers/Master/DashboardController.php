<?php

namespace Porteiro\Http\Controllers\Master;

// use App\Models\Blog\Blog;
use App\Models\Negocios\Page;
use App\Models\Negocios\Subscription;
use App\Models\Photo;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Schema;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Tracking\Services\AnalyticsService;

class DashboardController extends Controller
{

    
    /**
     * Dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Home";

        $articles = 0; //Blog::count();
        $pages = 0; //Page::count();
        $members = 0; //UserMeta::count();
        // $subscriptions = Subscription::count();
        $photos = 0; //Photo::count();

        return view(
            'porteiro::master.dashboard.home',
            compact(
                'title',
                'articles',
                'pages',
                'photos',
                'members',
                // 'subscriptions'
            )
        );
    }

}

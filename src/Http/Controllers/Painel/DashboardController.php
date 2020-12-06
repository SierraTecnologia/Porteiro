<?php

namespace Porteiro\Http\Controllers\Painel;


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
            'porteiro::painel.dashboard.home',
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

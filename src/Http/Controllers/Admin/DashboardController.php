<?php

namespace Porteiro\Http\Controllers\Admin;

// use App\Models\Blog\Blog;
use App\Models\Negocios\Page;
use App\Models\Negocios\Subscription;
use App\Models\Photo;
use App\Models\UserMeta;
use Illuminate\Support\Facades\Schema;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Tracking\Services\AnalyticsService;
use Pedreiro\Components\BoxComponent;

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
        $boxComponents = [];

        
        if (class_exists(\Cms\Models\Blog\Blog::class)){

            $boxComponents[] = BoxComponent::create(
                // color
                'red',
                // number
                \Cms\Models\Blog\Blog::count(),
                // name
                trans('words.articles'),
                // icon
                'fa fa-user-plus',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/articles')
            );
        } else if (class_exists(\Siravel\Models\Blog\Blog::class)){
            $boxComponents[] = BoxComponent::create(
                // color
                'red',
                // number
                \Siravel\Models\Blog\Blog::count(),
                // name
                trans('words.articles'),
                // icon
                'fa fa-user-plus',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/articles')
            );
        }

        if (class_exists(\Cms\Models\Negocios\Page::class)){
            $boxComponents[] = BoxComponent::create(
                // color
                'yellow',
                // number
                \Cms\Models\Negocios\Page::count(),
                // name
                trans('words.pages'),
                // icon
                'fa fa-key',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/pages')
                
            );
        } else if (class_exists(\Siravel\Models\Negocios\Page::class)){
            $boxComponents[] = BoxComponent::create(
                // color
                'yellow',
                // number
                \Siravel\Models\Negocios\Page::count(),
                // name
                trans('words.pages'),
                // icon
                'fa fa-key',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/pages')
            );
        }

        if (class_exists(\Cms\Models\Negocios\Subscription::class)){
            $boxComponents[] = BoxComponent::create(
                // color
                'aqua',
                // number
                \Cms\Models\Negocios\Subscription::count(),
                // name
                trans('words.members'),
                // icon
                'ion ion-person-stalker',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/members')
            );
        } else if (class_exists(\Siravel\Models\Negocios\Subscription::class)){
            $boxComponents[] = BoxComponent::create(
                // color
                'yellow',
                // number
                \Siravel\Models\Negocios\Subscription::count(),
                // name
                trans('words.pages'),
                // icon
                'fa fa-key',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/pages'),
            );
        }

        if (class_exists(\Stalker\Models\Photo::class)){
            $boxComponents[] = BoxComponent::create(
                // color
                'aqua',
                // number
                \Stalker\Models\Photo::count(),
                // name
                trans('words.photos'),
                // icon
                'ion ion-person-stalker',
                // linkText
                trans('words.moreInfo'),
                // link
                url('admin/photos')
            );
        }

        return view(
            'porteiro::components.dashboard',
            compact(
                'boxComponents',
            )
        );
    }

}

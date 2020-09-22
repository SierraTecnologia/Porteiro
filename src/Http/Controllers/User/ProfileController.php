<?php

namespace Porteiro\Http\Controllers\User;

use Support\Http\Requests;
use Porteiro\Http\Controllers\User\Controller;
use App\Models\Activity;
use App\Models\Banner;
use App\Models\Link;
use App\Models\ActiveUser;
use App\Models\HotTopic;
use MediaManager\Models\Image;
use Illuminate\Http\Request;
use Auth;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        switch ($request->view) {
        case 'all':
            $activities = Activity::recent()->paginate(50);
            break;

        case 'mine':
            $activities = Auth::user()->activities();
            break;

        default:
            $activities = Auth::user()->subscribedActivityFeeds();
            break;
        }

        // $links  = Link::allFromCache();
        // $banners = Banner::allByPosition();
        // $active_users = ActiveUser::fetchAll();
        // $hot_topics = HotTopic::fetchAll();
        // $images = Image::fromActivities($activities);
        $links  = [];
        $banners = [];
        $active_users = [];
        $hot_topics = [];
        $images = [];

        return view('transmissor::users.activities.index', compact('activities', 'links', 'banners', 'active_users', 'hot_topics', 'images'));
    }

    public function show(Request $request)
    {
        return $this->index($request);
    }

}

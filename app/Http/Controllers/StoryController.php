<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\Setting;
use App\Models\StoryItem;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function viewstory($id)
    {
        $cStory = StoryItem::findOrFail($id);
        $currClicks = $cStory->clicks;
        $cStory->clicks = $currClicks + 1;
        $cStory->save();
        return redirect($cStory->link);
    }
    
    public function stories()
    {
        if(check_permissions('manage_stories')){
            $title = 'Menaxho Story-it';
            $stories = Story::orderBy('updated_at', 'DESC')->get();
            $limit = Setting::where('name', '=', 'storylimit')->first();
            if(!$limit){
                $limit = 2;
            } else {
                $limit = $limit->value;
            }
            return view('admin.stories.index', compact('title', 'stories', 'limit'));
        }
        abort(404);
    }

    public function limitStories()
    {
        if(check_permissions('manage_stories')){
            $limit = Setting::where('name', '=', 'storylimit')->first();
            $slimit = Setting::where('name', '=', 'storylimitItem')->first();
            if(!$limit){
                $limit = 2;
            } else {
                $limit = $limit->value;
            }
            if(!$slimit){
                $slimit = 5;
            } else {
                $slimit = $slimit->value;
            }
            return view('admin.stories.limit', compact('limit', 'slimit'));
        }
        abort(404);
    }

    public function limitStoriesUpdate(Request $request)
    {
        if(check_permissions('manage_stories')){
            $validatedDate = $request->validate([
                'limit' => 'required|numeric',
                'slimit' => 'required|numeric',
            ], [
                'limit.required' => 'Limiti është i detyrueshëm',
                'limit.numeric' => 'Limiti duhet të jetë në formatin numër',
                'slimit.required' => 'Limiti është i detyrueshëm',
                'slimit.numeric' => 'Limiti duhet të jetë në formatin numër',
            ]);
            Setting::upsert([
                ['name' => 'storylimit', 'value' => $request->limit]
            ], ['name'], ['value']);
            Setting::upsert([
                ['name' => 'storylimitItem', 'value' => $request->slimit]
            ], ['name'], ['value']);
            session()->put('success','Limiti u ruajt me sukses.');
            return redirect()->route('admin.stories.index');
        }
        abort(404);
    }

    public function addstories()
    {
        if(check_permissions('manage_stories')){
            return view('admin.stories.add');
        }
        abort(404);
    }

    public function addstorestories(Request $request)
    {
        if(check_permissions('manage_stories')){
            $validatedDate = $request->validate([
                'name' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
            ]);
            $stories = new Story();
            $stories->name = $request->name;
            $stories->vendor_id = 0;
            $stories->cactive = $request->storyStatus;
            $stories->save();
            session()->put('success','Story u shtua me sukses.');
            return redirect()->route('admin.stories.edit', $stories->id);
        }
        abort(404);
    }

    public function editstories($id)
    {
        if(check_permissions('manage_stories') && is_numeric($id)){
            $story = Story::where('id', '=', $id)->first();
            return view('admin.stories.edit', compact('story'));
        }
        abort(404);
    }

    public function storestories(Request $request, $id)
    {
        if(check_permissions('manage_stories') && is_numeric($id)){
            $stories = Story::findOrFail($id);
            $i = 0;
            if($request->story_id){
                foreach($request->story_id as $story){
                    $i++;
                    StoryItem::findOrFail($story)->update(['corder' => $i]);
                }
            } else {
                $stories->needaction = 0;
            }
            // $stories->name = $request->name;
            if($request->fview >= 1){
                $stories->fview = $request->fview;
            }
            if($request->fclick >= 1){
                $stories->fclick = $request->fclick;
            }
            // $stories->cactive = $request->storyStatus;
            $stories->cactive = 1;
            $stories->save();
            session()->put('success','Story u ndryshua me sukses.');
            return redirect()->route('admin.stories.index');
        }
        abort(404);
    }

    public function deletestories($id)
    {
        if(check_permissions('manage_stories') && check_permissions('delete_rights') && is_numeric($id)){
            $stories = Story::findOrFail($id);
            if($stories){
                $stories->delete();
                session()->put('success','Story u fshi me sukses.');
                return redirect()->route('admin.stories.index');
            }
        }
        abort(404);
    }

    public function storiesadd($id)
    {
        if(check_permissions('manage_stories') && is_numeric($id)){
            $stories = Story::where('id', '=', $id)->first();
            return view('admin.stories.story.add', compact('stories'));
        }
        abort(404);
    }

    public function storiesstore(Request $request, $id)
    {
        if(check_permissions('manage_stories')){
            $validatedDate = $request->validate([
                'storyType' => 'required',
                'name' => 'required',
                'link' => 'required',
                'duration' => 'required',
                'image' => 'required_unless:storyType,2',
                'video' => 'required_unless:storyType,1',
            ], [
                'storyType.required' => 'Lloji është i detyrueshëm',
                'name.required' => 'Emri i butonit është i detyrueshëm',
                'link.required' => 'Linku është i detyrueshëm',
                'duration.required' => 'Kohëzgjatja është e detyrueshme',
                'image.required_unless' => 'Foto është e detyrueshme',
                'video.required_unless' => 'Video është e detyrueshme',
            ]);
            $story = new StoryItem();
            $story->name = $request->name;
            $story->stories_id = $id;
            $story->link = $request->link;
            $story->length = $request->duration;
            if($request->storyType == 2){
                $story->type = 2;
                $story->image = $request->video;
            } else {
                $story->type = 1;
                $story->image = $request->image;
            }
            $todayDate = date('Y-m-d H:i:s');
            $story->start_story = $todayDate;
            $story->end_story = $todayDate;
            $story->save();
            session()->put('success','Foto/Video u shtua me sukses.');
            return redirect()->route('admin.stories.edit', $id);
        }
        abort(404);
    }

    public function storiesedit($id, $sid)
    {
        if(check_permissions('manage_stories') && is_numeric($id) && is_numeric($sid)){
            $stories = Story::where('id', '=', $id)->first();
            $story = StoryItem::findOrFail($sid);
            if($story->stories_id == $stories->id){
                return view('admin.stories.story.edit', compact('story', 'stories'));
            }
        }
        abort(404);
    }

    public function storiesupdate(Request $request, $id, $sid)
    {
        if(check_permissions('manage_stories') && is_numeric($id) && is_numeric($sid)){
            $validatedDate = $request->validate([
                'storyType' => 'required',
                'name' => 'required',
                'link' => 'required',
                'duration' => 'required',
                'image' => 'required_unless:storyType,2',
                'video' => 'required_unless:storyType,1',
                'start_date' => 'required',
                'expire_date' => 'required',
            ], [
                'storyType.required' => 'Lloji është i detyrueshëm',
                'name.required' => 'Emri i butonit është i detyrueshëm',
                'link.required' => 'Linku është i detyrueshëm',
                'duration.required' => 'Kohëzgjatja është e detyrueshme',
                'image.required_unless' => 'Foto është e detyrueshme',
                'video.required_unless' => 'Video është e detyrueshme',
                'start_date.required_unless' => 'Video është e detyrueshme',
                'expire_date.required_unless' => 'Video është e detyrueshme',
            ]);
            $stories = Story::findOrFail($id);
            $story = StoryItem::findOrFail($sid);
            if($story->stories_id == $stories->id){
                $changeStatus = false;
                $story->name = $request->name;
                $story->stories_id = $id;
                if($story->link != $request->link){
                    $story->link = $request->link;
                }
                $story->length = $request->duration;
                if($request->storyType == 2){
                    $story->type = 2;
                    // $story->image = NULL;
                    if($story->image != $request->video){
                        $story->image = $request->video;
                    }
                } else {
                    $story->type = 1;
                    if($story->image != $request->image){
                        $story->image = $request->image;
                    }
                }
                $story->cactive = $request->storyStatus;
                $story->start_story = $request->start_date;
                $story->end_story = $request->expire_date;
                $story->save();
                // dd($stories->items()->where('cactive', '=', 0)->where('start_story', '>', date('Y-m-1 H:i:s'))->count());
                if($stories->items()->where('cactive', '=', 0)->where('start_story', '>', date('Y-m-1 H:i:s'))->count() == 0){
                    $stories->needaction = 0;
                } else {
                    $stories->needaction = 1;
                }
                $stories->save();
                session()->put('success','Foto/Video u ndryshua me sukses.');
                return redirect()->route('admin.stories.edit', $id);
            }
        }
        abort(404);
    }

    public function storiesdelete($id, $sid)
    {
        if(check_permissions('manage_stories') && check_permissions('delete_rights') && is_numeric($id) && is_numeric($sid)){
            $stories = Story::findOrFail($id);
            $story = StoryItem::findOrFail($sid);
            if($story->stories_id == $stories->id){
                $story->delete();
                if($stories->items()->where('cactive', '=', 0)->count() == 0){
                    $stories->needaction = 0;
                    $stories->save();
                }
                session()->put('success','Foto/Video u fshi me sukses.');
                return redirect()->route('admin.stories.edit', $id);
            }
        }
    }

    public function vstories()
    {
        if(check_permissions('manage_stories') && vendor_status()){
            $title = 'Menaxho Storit';
            $stories = current_vendor()->stories()->orderBy('created_at', 'DESC')->get();
            if(current_vendor()->slimit && current_vendor()->slimit > 0){
                $limit = current_vendor()->slimit;
            } else {
                $limit = Setting::where('name', '=', 'storylimit')->first();
                if(!$limit){
                    $limit = 2;
                } else {
                    $limit = $limit->value;
                }
            }
            return view('admin.stories.vindex', compact('title', 'stories', 'limit'));
        }
        abort(404);
    }

    public function vstories1()
    {
        if(check_permissions('manage_stories') && vendor_status()){
            $title = 'Menaxho Storit';
            $story = current_vendor()->storie;
            $stories = [];
            if($story){
                $stories = $story->items;
            }
            if(current_vendor()->slimit && current_vendor()->slimit > 0){
                $limit = current_vendor()->slimit;
                $slimit = current_vendor()->silimit;
            } else {
                $limit = Setting::where('name', '=', 'storylimit')->first();
                $slimit = Setting::where('name', '=', 'storylimitItem')->first();
                if(!$limit){
                    $limit = 2;
                    $slimit = 2;
                } else {
                    $limit = $limit->value;
                    $slimit = $slimit->value;
                }
            }
            $activeStories = $story->items->where('end_story', '<', date('Y-m-d H:i:s'));
            return view('admin.stories.vendor.index', compact('title', 'story', 'stories', 'limit', 'slimit'));
        }
        abort(404);
    }

    public function vaddstories()
    {
        if(check_permissions('manage_stories') && vendor_status()){
            return view('admin.stories.vadd');
        }
        abort(404);
    }

    public function vaddstorestories(Request $request)
    {
        if(check_permissions('manage_stories') && vendor_status()){
            $validatedDate = $request->validate([
                'name' => 'required',
            ], [
                'name.required' => 'Emri është i detyrueshëm',
            ]);
            $stories = new Story();
            $stories->name = $request->name;
            $stories->vendor_id = current_vendor()->id;
            $stories->save();
            session()->put('success','Story u shtua me sukses.');
            return redirect()->route('vendor.stories.edit', $stories->id);
        }
        abort(404);
    }

    public function veditstories($id)
    {
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id)){
            $story = Story::where('id', '=', $id)->first();
            if(current_vendor()->silimit && current_vendor()->silimit > 0){
                $slimit = current_vendor()->silimit;
            } else {
                $slimit = Setting::where('name', '=', 'storylimitItem')->first();
                if(!$slimit){
                    $slimit = 5;
                } else {
                    $slimit = $slimit->value;
                }
            }
            return view('admin.stories.vedit', compact('story', 'slimit'));
        }
        abort(404);
    }

    public function vstorestories(Request $request, $id)
    {
        $validatedDate = $request->validate([
            'name' => 'required',
        ], [
            'name.required' => 'Emri është i detyrueshëm',
        ]);
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id)){
            $stories = Story::findOrFail($id);
            $i = 0;
            foreach($request->story_id as $story){
                $i++;
                StoryItem::findOrFail($story)->update(['corder' => $i]);
            }
            $stories->name = $request->name;
            $stories->save();
            session()->put('success','Story u ndryshua me sukses.');
            return redirect()->route('vendor.stories.index');
        }
        abort(404);
    }

    public function vdeletestories($id)
    {
        if(check_permissions('manage_stories') && vendor_status() && check_permissions('delete_rights') && is_numeric($id)){
            $stories = Story::findOrFail($id);
            if($stories && $stories->vendor_id == current_vendor()->id){
                $stories->delete();
                return redirect()->route('vendor.stories.index');
            }
        }
        abort(404);
    }

    public function vstoriesadd($id)
    {
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id)){
            $stories = Story::where('id', '=', $id)->first();
            return view('admin.stories.story.vadd', compact('stories'));
        }
        abort(404);
    }

    public function vaddstories1()
    {
        if(check_permissions('manage_stories') && vendor_status()){
            return view('admin.stories.vendor.add');
        }
        abort(404);
    }

    public function vstoriesstore1(Request $request)
    {
        if(check_permissions('manage_stories') && vendor_status()){
            $validatedDate = $request->validate([
                'storyType' => 'required',
                'name' => 'required',
                'link' => 'required',
                'duration' => 'required',
                'image' => 'required_unless:storyType,2',
                'video' => 'required_unless:storyType,1',
            ], [
                'storyType.required' => 'Lloji është i detyrueshëm',
                'name.required' => 'Emri i butonit është i detyrueshëm',
                'link.required' => 'Linku është i detyrueshëm',
                'duration.required' => 'Kohëzgjatja është e detyrueshme',
                'image.required_unless' => 'Foto është e detyrueshme',
                'video.required_unless' => 'Video është e detyrueshme',
            ]);
            $stories = Story::where('vendor_id', current_vendor()->id)->first();
            if(!$stories){
                $stories = new Story();
                $stories->vendor_id = current_vendor()->id;
                $stories->save();
            }
            $story = new StoryItem();
            $story->name = $request->name;
            $story->stories_id = $stories->id;
            $story->link = $request->link;
            $story->length = $request->duration;
            if($request->storyType == 2){
                $story->type = 2;
                $story->image = $request->video;
            } else {
                $story->type = 1;
                $story->image = $request->image;
            }
            $todayDate = date('Y-m-d H:i:s');
            $story->start_story = $todayDate;
            $story->end_story = $todayDate;
            $story->save();
            $stories->needaction = 1;
            $stories->save();
            session()->put('success','Foto/Video u shtua me sukses.');
            return redirect()->route('vendor.stories.index');
        }
        abort(404);
    }

    public function vstoriesstore(Request $request, $id)
    {
        if(check_permissions('manage_stories') && vendor_status()){
            $validatedDate = $request->validate([
                'storyType' => 'required',
                'name' => 'required',
                'link' => 'required',
                'duration' => 'required',
                'image' => 'required_unless:storyType,2',
                'video' => 'required_unless:storyType,1',
            ], [
                'storyType.required' => 'Lloji është i detyrueshëm',
                'name.required' => 'Emri i butonit është i detyrueshëm',
                'link.required' => 'Linku është i detyrueshëm',
                'duration.required' => 'Kohëzgjatja është e detyrueshme',
                'image.required_unless' => 'Foto është e detyrueshme',
                'video.required_unless' => 'Video është e detyrueshme',
            ]);
            $stories = Story::findOrFail($id);
            $story = new StoryItem();
            $story->name = $request->name;
            $story->stories_id = $id;
            $story->link = $request->link;
            $story->length = $request->duration;
            if($request->storyType == 2){
                $story->type = 2;
                $story->image = $request->video;
            } else {
                $story->type = 1;
                $story->image = $request->image;
            }
            $story->save();
            $stories->needaction = 1;
            $stories->save();
            session()->put('success','Foto/Video u shtua me sukses.');
            return redirect()->route('vendor.stories.edit', $id);
        }
        abort(404);
    }

    public function vstoriesedit($id, $sid)
    {
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id) && is_numeric($sid)){
            $stories = Story::where('id', '=', $id)->first();
            $story = StoryItem::findOrFail($sid);
            if($story->stories_id == $stories->id){
                return view('admin.stories.story.vedit', compact('story', 'stories'));
            }
        }
        abort(404);
    }

    public function vstoriesupdate(Request $request, $id, $sid)
    {
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id) && is_numeric($sid)){
            $validatedDate = $request->validate([
                'storyType' => 'required',
                'name' => 'required',
                'link' => 'required',
                'duration' => 'required',
                'image' => 'required_unless:storyType,2',
                'video' => 'required_unless:storyType,1',
            ], [
                'storyType.required' => 'Lloji është i detyrueshëm',
                'name.required' => 'Emri i butonit është i detyrueshëm',
                'link.required' => 'Linku është i detyrueshëm',
                'duration.required' => 'Kohëzgjatja është e detyrueshme',
                'image.required_unless' => 'Foto është e detyrueshme',
                'video.required_unless' => 'Video është e detyrueshme',
            ]);
            $stories = Story::findOrFail($id);
            $story = StoryItem::findOrFail($sid);
            if($story->stories_id == $stories->id){
                $changeStatus = false;
                $story->name = $request->name;
                $story->stories_id = $id;
                if($story->link != $request->link){
                    $story->link = $request->link;
                    $changeStatus = true;
                }
                $story->length = $request->duration;
                if($request->storyType == 2){
                    $story->type = 2;
                    // $story->image = NULL;
                    if($story->image != $request->video){
                        $story->image = $request->video;
                        $changeStatus = true;
                    }
                } else {
                    $story->type = 1;
                    if($story->image != $request->image){
                        $story->image = $request->image;
                        $changeStatus = true;
                    }
                }
                if($changeStatus && $story->cactive != 0){
                    $story->cactive = 2;
                    $stories->needaction = 1;
                    $stories->save();
                }
                $story->save();
                session()->put('success','Foto/Video u ndryshua me sukses.');
                return redirect()->route('vendor.stories.edit', $id);
            }
        }
        abort(404);
    }

    public function vstoriesedit1($id)
    {
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id)){
            // $stories = Story::where('id', '=', $id)->first();
            $story = StoryItem::findOrFail($id);
            $expiredStory = false;
            $refusedStory = false;
            if($story->cactive == 3) $refusedStory = true;
            if($story->end_story < date('Y-m-1 H:i:s')) $expiredStory = true;
            if($story->main->vendor_id == current_vendor()->id){
                return view('admin.stories.vendor.edit', compact('story', 'expiredStory', 'refusedStory'));
            }
        }
        abort(404);
    }

    public function vstoriesupdate1(Request $request, $id)
    {
        if(check_permissions('manage_stories') && vendor_status() && is_numeric($id)){
            $validatedDate = $request->validate([
                'storyType' => 'required',
                'name' => 'required',
                'link' => 'required',
                'duration' => 'required',
                'image' => 'required_unless:storyType,2',
                'video' => 'required_unless:storyType,1',
            ], [
                'storyType.required' => 'Lloji është i detyrueshëm',
                'name.required' => 'Emri i butonit është i detyrueshëm',
                'link.required' => 'Linku është i detyrueshëm',
                'duration.required' => 'Kohëzgjatja është e detyrueshme',
                'image.required_unless' => 'Foto është e detyrueshme',
                'video.required_unless' => 'Video është e detyrueshme',
            ]);
            // $stories = Story::findOrFail($id);
            $story = StoryItem::findOrFail($id);
            if($story->main->vendor_id == current_vendor()->id){
                $changeStatus = false;
                $story->name = $request->name;
                // $story->stories_id = $id;
                if($story->link != $request->link){
                    $story->link = $request->link;
                    $changeStatus = true;
                }
                $story->length = $request->duration;
                if($request->storyType == 2){
                    $story->type = 2;
                    // $story->image = NULL;
                    if($story->image != $request->video){
                        $story->image = $request->video;
                        $changeStatus = true;
                    }
                } else {
                    $story->type = 1;
                    if($story->image != $request->image){
                        $story->image = $request->image;
                        $changeStatus = true;
                    }
                }
                if($changeStatus && $story->cactive != 0){
                    $story->cactive = 2;
                    $story->main->needaction = 1;
                    $story->main->save();
                }
                $story->save();
                session()->put('success','Foto/Video u ndryshua me sukses.');
                return redirect()->route('vendor.stories.index');
            }
        }
        abort(404);
    }

    public function vstoriesdelete($id, $sid)
    {
        if(check_permissions('manage_stories') && vendor_status() && check_permissions('delete_rights') && is_numeric($id) && is_numeric($sid)){
            $stories = Story::findOrFail($id);
            $story = StoryItem::findOrFail($sid);
            if($story->stories_id == $stories->id){
                $story->delete();
                if($stories->items()->where('cactive', '=', 0)->count() == 0){
                    $stories->needaction = 0;
                    $stories->save();
                }
                session()->put('success','Foto/Video u fshi me sukses.');
                return redirect()->route('vendor.stories.edit', $id);
            }
        }
        abort(404);
    }

    public function vstoriesdelete1($id)
    {
        if(check_permissions('manage_stories') && vendor_status() && check_permissions('delete_rights') && is_numeric($id)){
            // $stories = Story::findOrFail($id);
            $story = StoryItem::findOrFail($id);
            if($story->main->vendor_id == current_vendor()->id){
                $story->delete();
                // if($story->main->items()->where('cactive', '=', 0)->count() == 0){
                //     $story->main->needaction = 0;
                //     $story->main->save();
                // }
                session()->put('success','Foto/Video u fshi me sukses.');
                return redirect()->route('vendor.stories.index');
            }
        }
        abort(404);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserRoleRequest;
use App\Models\VendorRole;
use Illuminate\Support\Facades\Mail;
use App\Mail\VendorUserMember;
use App\Mail\VendorUserMemberAccept;

class RoleController extends Controller
{
    public function manageroles()
    {
        if(check_permissions('manage_vendor')){
            $roles = Role::orderBy('type', 'desc')->get();
            return view('admin.roles.index', compact('roles'));
        }
        abort(404);
    }

    public function addroles()
    {
        if(check_permissions('manage_vendor')){
            return view('admin.roles.add');
        }
        abort(404);
    }

    public function storeroles(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'name' => 'required'
            ], [
                'name.required' => 'Emri është i detyrueshëm'
            ]);
            $role = new Role();
            $role->name = $request->name;
            $role->description = $request->description;
            if($request->action == 1){
                $role->type = 1;
                $role->manage_users = $request->manage_users;
                $role->manage_vendors = $request->manage_vendors;
                $role->manage_vendor = $request->manage_pages;
            } else {
                $role->type = 0;
                $role->manage_users = 0;
                $role->manage_vendors = 0;
                $role->manage_vendor = $request->manage_vendor;
            }
            $role->manage_chat = $request->manage_chat;
            $role->manage_stories = $request->manage_stories;
            $role->manage_ads = $request->manage_ads;
            //$role->manage_pages = $request->manage_pages;
            // $role->manage_notifications = $request->manage_notifications;
            $role->manage_offers = $request->manage_offers;
            $role->manage_supports = $request->manage_supports;
            $role->manage_products = $request->manage_products;
            $role->manage_orders = $request->manage_orders;
            $role->delete_rights = $request->delete_rights;
            $role->save();
            session()->put('success','Roli u shtua me sukses.');
            return redirect()->route('admin.roles.index');
        }
        abort(404);
    }

    public function editroles($id)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)) {
                $role = Role::findorfail($id);
                return view('admin.roles.edit', compact('role'));
            }
        }
        abort(404);
    }

    public function saveroles(Request $request, $id)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'name' => 'required'
            ], [
                'name.required' => 'Emri është i detyrueshëm'
            ]);
            $role = Role::findorfail($id);
            $role->name = $request->name;
            $role->description = $request->description;
            if($role->can_edit){
                if($request->action == 1){
                    $role->type = 1;
                    $role->manage_users = $request->manage_users;
                    $role->manage_vendors = $request->manage_vendors;
                    $role->manage_vendor = $request->manage_pages;
                } else {
                    $role->type = 0;
                    $role->manage_users = 0;
                    $role->manage_vendors = 0;
                    $role->manage_vendor = $request->manage_vendor;
                }
                $role->manage_chat = $request->manage_chat;
                $role->manage_stories = $request->manage_stories;
                $role->manage_ads = $request->manage_ads;
                // $role->manage_notifications = $request->manage_notifications;
                $role->manage_offers = $request->manage_offers;
                $role->manage_supports = $request->manage_supports;
                $role->manage_products = $request->manage_products;
                $role->manage_orders = $request->manage_orders;
                $role->delete_rights = $request->delete_rights;
            }
            session()->put('success','Roli u ndryshua me sukses.');
            $role->save();

            return redirect()->route('admin.roles.index');
        }
        abort(404);
    }

    public function deleterole($id)
    {
        if(check_permissions('manage_vendor') && check_permissions('delete_rights')){
            if(is_numeric($id)){
                $role = Role::findorfail($id);
                if($role){
                    if($role->can_edit){
                        $role->delete();
                        session()->put('success','Roli u fshi me sukses.');
                        return redirect()->route('admin.roles.index');
                    }
                    session()->put('success','Roli nuk u fshi');
                    return redirect()->route('admin.roles.index');
                }
            }
        }
        abort(404);
    }

    public function managestaff()
    {
        if(check_permissions('manage_vendor')){
            $roles = Role::where('type', '=', 1)->get();
            return view('admin.roles.staff.index', compact('roles'));
        }
        abort(404);
    }

    public function addstaff()
    {
        if(check_permissions('manage_vendor')){
            $roles = Role::where('type', '=', 1)->get();
            return view('admin.roles.staff.add', compact('roles'));
        }
        abort(404);
    }

    public function storestaff(Request $request)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'user_id' => 'required',
                'action' => 'required'
            ], [
                'user_id.required' => 'Përdoruesi është i detyrueshëm',
                'action.required' => 'Roli është i detyrueshëm'
            ]);
            $staff = new UserRole();
            $exisRole = UserRole::where('user_id', '=', $request->user_id)->first();
            if($exisRole){
                session()->put('success','Stafi ka aktualisht një rol ekzistuez.');
                return redirect()->route('admin.staff.index');
            } else {
                $staff->user_id = $request->user_id;
                $role = Role::where('id', '=', $request->action)->where('type', '=', 1)->first();
                if($role){
                    $staff->role_id = $request->action;
                    $staff->save();
                    session()->put('success','Stafi u shtua me sukses.');
                    return redirect()->route('admin.staff.index');
                }
            }
        }
        abort(404);
    }

    public function editstaff($id, $rid)
    {
        if(check_permissions('manage_vendor')){
            if(is_numeric($id)){
                $userRole = UserRole::where([['user_id', '=', $id],['role_id', $rid]])->first();
                if($userRole){
                    $roles = Role::where('type', '=', 1)->get();
                    return view('admin.roles.staff.edit', compact('userRole', 'roles'));
                }
            }
        }
        abort(404);
    }

    public function savestaff(Request $request, $id, $rid)
    {
        if(check_permissions('manage_vendor')){
            $validatedDate = $request->validate([
                'user_id' => 'required',
                'action' => 'required'
            ], [
                'user_id.required' => 'Përdoruesi është i detyrueshëm',
                'action.required' => 'Roli është i detyrueshëm'
            ]);
            UserRole::where([['user_id', '=', $id],['role_id', $rid]])->delete();
            $staff = new UserRole();
            $staff->user_id = $request->user_id;
            $staff->role_id = $request->action;
            $staff->save();
            session()->put('success','Stafi u ndryshua me sukses.');
            return redirect()->route('admin.staff.index');
        }
        abort(404);
    }

    public function deletestaff($id, $rid)
    {
        if(check_permissions('manage_vendor') && check_permissions('delete_rights')){
            if(is_numeric($id) && is_numeric($rid)){
                $userRole = UserRole::where([['user_id', '=', $id],['role_id', $rid]])->first();
                if($userRole){
                    if($userRole->role_id == 1){
                        $userRoleAdmin = UserRole::where('role_id', '=', 1)->get();
                        if(count($userRoleAdmin) > 1){
                            UserRole::where([['user_id', '=', $id],['role_id', $rid]])->delete();
                        } else {
                            session()->put('success','Duhet të paktën një administrator.');
                            return redirect()->route('admin.staff.index');
                        }
                    } else {
                        UserRole::where([['user_id', '=', $id],['role_id', $rid]])->delete();
                        session()->put('success','Story u fshi me sukses.');
                        return redirect()->route('admin.staff.index');
                    }
                }
                return redirect()->route('admin.staff.index');
            }
        }
        abort(404);
    }

    public function vmanagestaff()
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            $vRequests = current_vendor()->ownersRequest;
            $users = current_vendor()->owners;
            return view('admin.roles.staff.vindex', compact('vRequests', 'users'));
        }
        abort(404);
    }

    public function vaddstaff()
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            $roles = Role::where('type', '=', 0)->get();
            return view('admin.roles.staff.vadd', compact('roles'));
        }
        abort(404);
    }

    public function vstorestaff(Request $request)
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            $validatedDate = $request->validate([
                'user_email' => 'required',
                'action' => 'required'
            ], [
                'user_email.required' => 'Email i përdoruesit është i detyrueshëm',
                'action.required' => 'Roli është i detyrueshëm'
            ]);
            $checkRole = UserRole::where('user_id', '=', current_user()->id)->first();
            if($checkRole){
                session()->put('warning','Emaili '.$request->user_email.' ekziston si staf në dyqanin tuaj.');
                return redirect()->route('vendor.staff.index');
            }
            $roleType = Role::where('id', '=', $request->action)->first();
            if($roleType && $roleType->type == 0 && $roleType->id != 2){
                $checkUser = User::where('email', $request->user_email)->first();
                if($checkUser){
                    $userRole = new UserRole();
                    $userRole->user_id = $checkUser->id;
                    $userRole->role_id = $request->action;
                    $userRole->save();
                    $vendorRole = new VendorRole();
                    $vendorRole->user_id = $checkUser->id;
                    $vendorRole->vendor_id = current_vendor()->id;
                    $vendorRole->save();
                    Mail::to($checkUser->email)->send(new VendorUserMemberAccept($userRole, current_vendor()));
                    session()->put('success','Përdoruesi '.$checkUser->first_name.' u shtua me sukes si staf.');
                } else {
                    $staff = new UserRoleRequest();
                    $staff->user_id = current_user()->id;
                    $staff->vendor_id = current_vendor()->id;
                    $staff->user_email = $request->user_email;
                    $staff->role_id = $request->action;
                    $staff->token = Str::random(30);
                    $staff->save();
                    Mail::to($staff->user_email)->send(new VendorUserMember($staff, current_vendor(), 1));
                    session()->put('success','Kërkesa për shtim në staf u dërgua me sukses.');
                }
                
            }
            
            return redirect()->route('vendor.staff.index');
        }
        abort(404);
    }

    public function veditstaff($id, $rid)
    {
        if(check_permissions('manage_vendor') && vendor_status() && is_numeric($id)){
            $userRole = UserRole::where([['user_id', '=', $id],['role_id', $rid]])->first();
            if($userRole){
                $roles = Role::where('type', '=', 0)->get();
                return view('admin.roles.staff.vedit', compact('userRole', 'roles'));
            }
        }
        abort(404);
    }

    public function vsavestaff(Request $request, $id, $rid)
    {
        if(check_permissions('manage_vendor') && vendor_status()){
            $validatedDate = $request->validate([
                'user_id' => 'required',
                'action' => 'required'
            ], [
                'user_id.required' => 'Përdoruesi është i detyrueshëm',
                'action.required' => 'Roli është i detyrueshëm'
            ]);
            $roleType = Role::where('id', '=', $request->action)->first();
            if($roleType && $roleType->type == 0 && $roleType->id != 2){
                UserRole::where([['user_id', '=', $id],['role_id', $rid]])->delete();
                $staff = new UserRole();
                $staff->user_id = $request->user_id;
                $staff->role_id = $request->action;
                $staff->save();
                session()->put('success','Stafi u ndryshua me sukses.');
                return redirect()->route('vendor.staff.index');
            }
        }
        abort(404);
    }

    public function vdeletestaff($id, $rid)
    {
        if(check_permissions('manage_vendor') && vendor_status()  && check_permissions('delete_rights') && is_numeric($id) && is_numeric($rid)){
            $userRole = UserRole::where([['user_id', '=', $id],['role_id', $rid]])->first();
            if($userRole){
                if($userRole->role_id == 2){
                    session()->put('success','Nuk mund të fshihet zotëruhesi i dyqanit.');
                    return redirect()->route('vendor.staff.index');
                } else {
                    $vendorId = current_vendor()->id;
                    UserRole::where([['user_id', '=', $id],['role_id', $rid]])->delete();
                    VendorRole::where([['user_id', '=', $id],['vendor_id', $vendorId]])->delete();
                    session()->put('success','Personi u fshi me sukses nga stafi.');
                    return redirect()->route('vendor.staff.index');
                }
            }
            return redirect()->route('vendor.staff.index');
        }
        abort(404);
    }

    public function vdeleterequeststaff($id)
    {
        if(check_permissions('manage_vendor') && vendor_status() && check_permissions('delete_rights') && is_numeric($id)){
            $userRequest = UserRoleRequest::findorfail($id);
            if($userRequest){
                $userRequest->delete();
                session()->put('success','Kërkesa u fshi me sukses');
                return redirect()->route('vendor.staff.index');
            }
        }
        abort(404);
    }

    public function vendorrole()
    {
        $users = current_vendor()->owners;
        return view('admin.roles.vindex', compact('users'));
    }
}

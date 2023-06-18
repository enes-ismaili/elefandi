<?php

use Illuminate\Support\Facades\Request;


function current_user()
{
    return auth()->user();
}

function current_vendor()
{
	if(auth()->user()){
		if(auth()->user()->aroles){
			$value = session('logAsVendor');
			if($value){
				return App\Models\Vendor::where('id', '=', $value)->first();
			}
			return auth()->user()->vendor();
		} else {
			return auth()->user()->vendor();
		}
	}
}

function vendor_status(){
	if(auth()->user()){
		if(auth()->user()->aroles){
			$value = session('logAsVendor');
			if($value){
				return App\Models\Vendor::where('id', '=', $value)->first()->amembership->count();
			}
			return auth()->user()->vendor()->amembership->count();
		} else {
			return auth()->user()->vendor()->amembership->count();
		}
	}
    
}

function check_permissions($perm){
	if(auth()->user()){
		if(Request::routeIs('vendor.*')){
			if(auth()->user()->aroles && session('logAsVendor')){
				return 1;
			} else {
				$vendPermission = current_user()->vroles->first();
				if(isset($vendPermission[$perm]) && $vendPermission[$perm]){
					return 1;
				}
			}
		} else if(Request::routeIs('admin.*')) {
			$adminPermission = current_user()->aroles->first();
			if(isset($adminPermission[$perm]) && $adminPermission[$perm]){
				return 1;
			}
		} else {
			$vendPermission = current_user()->vroles->first();
			if(isset($vendPermission[$perm]) && $vendPermission[$perm]){
				return 1;
			}
		}
		return 0;
	}
}

function mcheck_permissions($perm){
	if(auth()->user()){
		$vendPermission = current_user()->vroles->first();
		if(isset($vendPermission[$perm]) && $vendPermission[$perm]){
			return 1;
		}
	}
	return 0;
}
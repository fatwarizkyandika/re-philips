<?php

namespace App\Http\Controllers\Auth;

use App\GlobalChannel;
use App\Posm;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Region;
use App\GroupProduct;
use App\Group;
use App\Account;
use App\AccountType;
use App\GroupCompetitor;
use App\QuizTarget;
use App\Role;
use Auth;
use Geotools;

class OnceController extends Controller
{
    //
    public function tesGeo(){
//        $coordA   = Geotools::coordinate([106.8920396, -6.2318409]);
        $coordA   = Geotools::coordinate([-6.1880673, 106.8746353]);
//        $coordB   = Geotools::coordinate([106.8632812, -6.2623681]);
        $coordB   = Geotools::coordinate([-6.2318409, 106.8920396]);
        $distance = Geotools::distance()->setFrom($coordA)->setTo($coordB);

        return $distance->flat();
    }

    public function createAdmin(){
        $users = DB::table('users')->count();

        if($users == 0){
            User::create([
                'name' => 'REM',
                'email' => 'rem@gmail.com',            
                'password' => bcrypt('admin'),
                'role_id' => '28',
            ]);
        }

        return redirect('/');
    }

    public function createQuizTarget(){
        $target = DB::table('quiz_targets')->count();

        if($target == 0){
            // $role = array('Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'SMD Additional', 'ASC');
            // $grading = ['Associate', 'Starfour', 'Non-Starfour'];
            // foreach ($role as $key => $value) {
            //     foreach ($grading as $key2 => $value2) {
            //         QuizTarget::create([
            //             'role_id' => $value,
            //             'grading_id' => $value2,
            //         ]);
            //     }
            // }
        }

        return redirect('/');
    }

    public function createRole(){
        $role = DB::table('roles')->count();

        if($role == 0){
            $roles = array('Promoter', 'Promoter Additional', 'Promoter Event', 'Demonstrator MCC', 'Demonstrator DA', 'Driver', 'Helper', 'ACT', 'PPE', 'BDT', 'Salesman Explorer', 'PCE', 'RE Executive', 'RE Support', 'Supervisor', 'Trainer', 'Head Trainer', 'SMD', 'SMD Coordinator', 'HIC', 'HIE', 'Supervisor Hybrid', 'SMD Additional', 'ASC', 'DM', 'RSM', 'Admin', 'Master');

            foreach ($roles as $key => $value) {
                Role::create([
                    'role' => $value,
                    'role_group' => $value,
                ]);
            }
        }

        return redirect('/');
    }

    public function createRegion(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $region = DB::table('regions')->count();

                if($region == 0){
                    Region::create(['name'=>'East']);
                    Region::create(['name'=>'Jabodetabek']);
                    Region::create(['name'=>'Java']);
                    Region::create(['name'=>'Sumatra']);
                }
            }
        }

        return redirect('/');
    }

    public function createGroupProduct(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $groupProduct = DB::table('group_products')->count();

                if($groupProduct == 0){
                    GroupProduct::create(['name'=>'DA']);
                    GroupProduct::create(['name'=>'PC']);
                    GroupProduct::create(['name'=>'MCC']);                    
                }
            }
        }

        return redirect('/');
    }

    public function createGroup(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $group = DB::table('groups')->count();

                if($group == 0){
                    Group::create(['name'=>'Beverage Appliances', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Floor Care', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Garment Care', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Kitchen Appliances', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Pain Management', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Povos', 'groupproduct_id'=>'1']);
                    Group::create(['name'=>'Male Grooming', 'groupproduct_id'=>'2']);
                    Group::create(['name'=>'Beauty', 'groupproduct_id'=>'2']);
                    Group::create(['name'=>'Bottles', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Breast Pumps', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Mealtime & Cups', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Other & Accs.', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Soothers', 'groupproduct_id'=>'3']);
                    Group::create(['name'=>'Teats', 'groupproduct_id'=>'3']);
                }
            }
        }

        return redirect('/');
    }

    public function createGroupCompetitor(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $groupCompetitor = DB::table('group_competitors')->count();

                if($groupCompetitor == 0){
                    GroupCompetitor::create(['name'=>'COSMOS']);
                    GroupCompetitor::create(['name'=>'KIRIN']);
                    GroupCompetitor::create(['name'=>'MASPION']);
                    GroupCompetitor::create(['name'=>'MIYAKO']);
                    GroupCompetitor::create(['name'=>'OXONE']);
                    GroupCompetitor::create(['name'=>'YONG MA']);
                    GroupCompetitor::create(['name'=>'BRAUN']);
                    GroupCompetitor::create(['name'=>'GILLETE']);
                    GroupCompetitor::create(['name'=>'GLAM PALM']);
                    GroupCompetitor::create(['name'=>'PANASONIC']);
                    GroupCompetitor::create(['name'=>'REPID']);
                    GroupCompetitor::create(['name'=>'SHARP']);
                    GroupCompetitor::create(['name'=>'CHICCO']);
                    GroupCompetitor::create(['name'=>'DR.BROWN']);
                    GroupCompetitor::create(['name'=>'MEDELA']);
                    GroupCompetitor::create(['name'=>'PIGEON']);
                    GroupCompetitor::create(['name'=>'OTHERS']);
                }
            }
        }

        return redirect('/');
    }

    public function createGlobalChannel(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $accountype = DB::table('global_channels')->count();

                if($accountype == 0){
                    GlobalChannel::create(['name'=>'Modern Retail']);
                    GlobalChannel::create(['name'=>'Traditional Retail']);
                    GlobalChannel::create(['name'=>'Mother Care & Child']);
                }
            }
        }

        return redirect('/');
    }

    public function createAccountType(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $accountype = DB::table('account_types')->count();

                if($accountype == 0){
                    AccountType::create(['name'=>'Counter']);
                    AccountType::create(['name'=>'Electronic Specialist']);
                    AccountType::create(['name'=>'Hypermarket']);
                    AccountType::create(['name'=>'Traditional']);
                }
            }
        }

        return redirect('/');
    }

    public function createAccount(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $accountype = DB::table('accounts')->count();

                if($accountype == 0){
                    Account::create(['name'=>'Central','accounttype_id'=>'1']);
                    Account::create(['name'=>'Debenhams','accounttype_id'=>'1']);
                    Account::create(['name'=>'Love','accounttype_id'=>'1']);
                    Account::create(['name'=>'Metro','accounttype_id'=>'1']);
                    Account::create(['name'=>'Seibu','accounttype_id'=>'1']);
                    Account::create(['name'=>'Sogo','accounttype_id'=>'1']);

                    Account::create(['name'=>'Aeon','accounttype_id'=>'2']);
                    Account::create(['name'=>'Best Denki','accounttype_id'=>'2']);
                    Account::create(['name'=>'Courts','accounttype_id'=>'2']);
                    Account::create(['name'=>'Electronic City','accounttype_id'=>'2']);
                    Account::create(['name'=>'Electronic Solution','accounttype_id'=>'2']);

                    Account::create(['name'=>'Carrefour','accounttype_id'=>'3']);
                    Account::create(['name'=>'Hypermart','accounttype_id'=>'3']);
                    Account::create(['name'=>'Lottemart','accounttype_id'=>'3']);
                    Account::create(['name'=>'Lulu','accounttype_id'=>'3']);

                    Account::create(['name'=>'Traditional','accounttype_id'=>'4']);
                }
            }
        }

        return redirect('/');
    }

    public function createPosm(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $posm = DB::table('posms')->count();

                if($posm == 0){
                    Posm::create(['name'=>'KITCHEN ISLAND', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'LEAFLET', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'PLINTHS', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'POSTER PROMOTION', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'SHELFTALKER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'SHELFTALKER DI VACUUM', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STICKERS DI JUICER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STICKERS DI BLENDER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STICKERS DI SETRIKA', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'STORE SIGN', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'TOP GONDOLA', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'TV BERJALAN BAIK', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'WALL RACK/PILLAR', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'WOBBLER', 'groupproduct_id'=>'1']);
                    Posm::create(['name'=>'OTHERS', 'groupproduct_id'=>'1']);

                    Posm::create(['name'=>'AQUA TANK', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'CUSTOMIZED RACK', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'DEMO PRODUCTS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'FEATURE CARDS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'IONIC BRUSH KARTON DISPLAY', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'KERASHINE FLOOR STANDS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'PLOTAINER/RACK', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'POSTER PROMOTION', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'SENSOTOUCH STAND', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'ABLETOP STANDS', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'TOP GONDOLA', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'WALL RACK/PILLAR', 'groupproduct_id'=>'2']);
                    Posm::create(['name'=>'OTHERS', 'groupproduct_id'=>'2']);

                    Posm::create(['name'=>'2nd DISPLAY', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'CATALOGUE', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'DEMO PRODUCT', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'FLAGCHAINS', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'LEAFLET', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'POSTER', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'ROTATING DISPLAY', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'SHELFTALKER', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'STORE SIGN', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'TV BERJALAN BAIK', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'WOBBLER', 'groupproduct_id'=>'3']);
                    Posm::create(['name'=>'OTHERS', 'groupproduct_id'=>'3']);
                }
            }
        }

        return redirect('/');
    }

    public function createMaster(){
        if(Auth::user()){
            if(Auth::user()->role->role_group == 'Master'){
                $this->createGlobalChannel();
                $this->createRegion();
                $this->createGroupProduct();
                $this->createGroup();
                $this->createGroupCompetitor();
//                $this->createAccountType();
//                $this->createAccount();
//                $this->createPosm();
            }
        }  

        return redirect('/');  
    }
}

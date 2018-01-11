<?php

namespace App\Traits;

use App\Attendance;
use App\EmployeeStore;
use App\GroupProduct;
use App\Reports\SalesmanSummaryTargetActual;
use App\Reports\SummaryTargetActual;
use App\Store;
use App\TrainerArea;
use App\User;
use Carbon\Carbon;

trait TargetTrait {

    use ActualTrait;

    public function setHeader($data){

        /* Check header exist or not, if doesn't, make it */
        $target = SummaryTargetActual::where('user_id', $data['user_id'])->where('storeId', $data['store_id'])
            ->where('sell_type', $data['sell_type'])->first();

        // Fetch some data
        $store = Store::with('district.area.region', 'subChannel.channel')->where('id', $data['store_id'])->first();
        $user = User::where('id', $data['user_id'])->first();

        if(!$target){

            /* Trainer */
            $trIds = TrainerArea::where('area_id', $store->district->area->id)->pluck('user_id');
            $tr = User::whereIn('id', $trIds)->get();

            $trainer_name = '';
            foreach ($tr as $trDetail) {
                $trainer_name .= $trDetail->name;

                if ($trDetail->id != $tr->last()->id) {
                    $trainer_name .= ', ';
                }
            }

            $target = SummaryTargetActual::create([
                            'region_id' => $store->district->area->region->id,
                            'area_id' => $store->district->area->id,
                            'district_id' => $store->district->id,
                            'storeId' => $store->id,
                            'user_id' => $user->id,
                            'region' => $store->district->area->region->name,
                            'area' => $store->district->area->name,
                            'district' => $store->district->name,
                            'nik' => $user->nik,
                            'promoter_name' => $user->name,
                            'account_type' => $store->subChannel->channel->name,
                            'title_of_promoter' => $this->getPromoterTitle($user->id, $store->id, $data['sell_type']),
                            'classification_store' => $store->classification,
                            'account' => $store->subChannel->name,
                            'store_id' => $store->store_id,
                            'store_name_1' => $store->store_name_1,
                            'store_name_2' => $store->store_name_2,
                            'spv_name' => ($store->user->name != '') ? $store->user->name : '',
                            'trainer' => $trainer_name,
                            'sell_type' => $data['sell_type'],
                        ]);

        }else{

            /* Override title of promoter */
            $this->changePromoterTitle($user->id, $store->id, $data['sell_type']);

        }

        return $target;

    }

    public function setHeaderSalesman($data){

        /* Check header exist or not, if doesn't, make it */
        $target = SalesmanSummaryTargetActual::where('user_id', $data['user_id'])->first();
        $user = User::where('id', $data['user_id'])->first();

        // Fetch some data
        $storeIds = EmployeeStore::where('user_id', $user->id)->pluck('store_id');
        $store = Store::whereIn('id', $storeIds)->get();

        $area = '';

        if($store){ // If SEE had store linked

            $arr_area = [];

            foreach ($store as $data) {
                    array_push($arr_area, $data->district->area->name);
            }

            $area = implode(", ", array_unique($arr_area));
        }

        if(!$target){

            $target = SalesmanSummaryTargetActual::create([
                            'user_id' => $user->id,
                            'nik' => $user->nik,
                            'salesman_name' => $user->name,
                            'area' => $area,
                        ]);

        }else{ // Override area (jika ada penambahan/perubahan toko)

            $target->update([
                'area' => $area
            ]);

        }

        return $target;

    }

    public function changePromoterTitle($userId, $storeId, $sellType){

        $target = SummaryTargetActual::where('user_id',$userId)->where('storeId', $storeId)->where('sell_type', $sellType)->first();
        $target->update(['title_of_promoter' => $this->getPromoterTitle($userId, $storeId, $sellType)]);

    }

    public function changeWeekly($target, $total){

        $attendance = Attendance::where('user_id', $target->user_id)
                        ->whereMonth('attendances.date', '=', Carbon::now()->month)
                        ->whereYear('attendances.date', '=', Carbon::now()->year)->get();
//        $attendance = Attendance::where('user_id', $target)->get();

        if($attendance){ // JIKA ADA DATA ABSENSI NYA

            $hkWeek1 = 0;
            $hkWeek2 = 0;
            $hkWeek3 = 0;
            $hkWeek4 = 0;
            $hkWeek5 = 0;
            $hkLimit = 0;

            foreach ($attendance as $data){

                $week = Carbon::parse($data['date'])->weekOfMonth;

                if($week == 1){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek1 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 2){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek2 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 3){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek3 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 4){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek4 += 1;
                            $hkLimit += 1;
                        }
                    }
                }else if($week == 5){
                    if($data['status'] != 'Off'){
                        if($hkLimit < 26) {
                            $hkWeek5 += 1;
                            $hkLimit += 1;
                        }
                    }
                }

            }

            /* DA */
            $targetPerWeek1 = ($total['da']/26) * $hkWeek1;
            $targetPerWeek2 = ($total['da']/26) * $hkWeek2;
            $targetPerWeek3 = ($total['da']/26) * $hkWeek3;
            $targetPerWeek4 = ($total['da']/26) * $hkWeek4;
            $targetPerWeek5 = ($total['da']/26) * $hkWeek5;

//            return $targetPerWeek1 . ' - ' . $targetPerWeek2 . ' - ' . $targetPerWeek3 . ' - ' . $targetPerWeek4 . ' - ' . $targetPerWeek5;

            $target->update([
                'target_da_w1' => $targetPerWeek1,
                'target_da_w2' => $targetPerWeek2,
                'target_da_w3' => $targetPerWeek3,
                'target_da_w4' => $targetPerWeek4,
                'target_da_w5' => $targetPerWeek5,
            ]);

            /* PC */
            $targetPerWeek1 = ($total['pc']/26) * $hkWeek1;
            $targetPerWeek2 = ($total['pc']/26) * $hkWeek2;
            $targetPerWeek3 = ($total['pc']/26) * $hkWeek3;
            $targetPerWeek4 = ($total['pc']/26) * $hkWeek4;
            $targetPerWeek5 = ($total['pc']/26) * $hkWeek5;

            $target->update([
                'target_pc_w1' => $targetPerWeek1,
                'target_pc_w2' => $targetPerWeek2,
                'target_pc_w3' => $targetPerWeek3,
                'target_pc_w4' => $targetPerWeek4,
                'target_pc_w5' => $targetPerWeek5,
            ]);

            /* MCC */
            $targetPerWeek1 = ($total['mcc']/26) * $hkWeek1;
            $targetPerWeek2 = ($total['mcc']/26) * $hkWeek2;
            $targetPerWeek3 = ($total['mcc']/26) * $hkWeek3;
            $targetPerWeek4 = ($total['mcc']/26) * $hkWeek4;
            $targetPerWeek5 = ($total['mcc']/26) * $hkWeek5;

            $target->update([
                'target_mcc_w1' => $targetPerWeek1,
                'target_mcc_w2' => $targetPerWeek2,
                'target_mcc_w3' => $targetPerWeek3,
                'target_mcc_w4' => $targetPerWeek4,
                'target_mcc_w5' => $targetPerWeek5,
            ]);

//            return $targetPerWeek1 . ' - ' . $targetPerWeek2 . ' - ' . $targetPerWeek3 . ' - ' . $targetPerWeek4 . ' - ' . $targetPerWeek5;

        }

    }

    public function changeTarget($data, $change){

        $target = $this->setHeader($data);

        /* Target Add and/or Sum */
        $targetAfter = SummaryTargetActual::where('id', $target->id)->first();
        $sumStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type']);
        $sumArea = SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type']);
        $sumRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type']);
        $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
        $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
        $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

        if(!$sumTargetStore) $sumTargetStore = 0;
        if(!$sumTargetArea) $sumTargetArea = 0;
        if(!$sumTargetRegion) $sumTargetRegion = 0;

        /* Add / Sum All Target */
        if($change == 'change'){ // INSERT / UPDATE

            // DA
            if (isset($data['targetOldDa']) && $data['targetOldDa'] > 0) {
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da - $data['targetOldDa'],
                    'sum_target_store' => $sumTargetStore - $data['targetOldDa'],
                    'sum_target_area' => $sumTargetArea - $data['targetOldDa'],
                    'sum_target_region' => $sumTargetRegion - $data['targetOldDa'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da + $data['target_da'],
                    'sum_target_store' => $targetAfter->sum_target_store + $data['target_da'],
                    'sum_target_area' => $targetAfter->sum_target_area + $data['target_da'],
                    'sum_target_region' => $targetAfter->sum_target_region + $data['target_da'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da + $data['target_da'],
                    'sum_target_store' => $sumTargetStore + $data['target_da'],
                    'sum_target_area' => $sumTargetArea + $data['target_da'],
                    'sum_target_region' => $sumTargetRegion + $data['target_da'],
                ]);
            }

            // Product Focus DA
            if (isset($data['targetOldPfDa']) && $data['targetOldPfDa'] > 0) {
                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da - $data['targetOldPfDa'],
                ]);

                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da + $data['target_pf_da'],
                ]);
            }else{
                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da + $data['target_pf_da'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

            $sumStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type']);
            $sumArea = SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type']);
            $sumRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type']);
            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // PC
            if (isset($data['targetOldPc']) && $data['targetOldPc'] > 0) {
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc - $data['targetOldPc'],
                    'sum_target_store' => $sumTargetStore - $data['targetOldPc'],
                    'sum_target_area' => $sumTargetArea - $data['targetOldPc'],
                    'sum_target_region' => $sumTargetRegion - $data['targetOldPc'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc + $data['target_pc'],
                    'sum_target_store' => $targetAfter->sum_target_store + $data['target_pc'],
                    'sum_target_area' => $targetAfter->sum_target_area + $data['target_pc'],
                    'sum_target_region' => $targetAfter->sum_target_region + $data['target_pc'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc + $data['target_pc'],
                    'sum_target_store' => $sumTargetStore + $data['target_pc'],
                    'sum_target_area' => $sumTargetArea + $data['target_pc'],
                    'sum_target_region' => $sumTargetRegion + $data['target_pc'],
                ]);
            }

            // Product Focus PC
            if (isset($data['targetOldPfPc']) && $data['targetOldPfPc'] > 0) {
                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc - $data['targetOldPfPc'],
                ]);

                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc + $data['target_pf_pc'],
                ]);
            }else{
                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc + $data['target_pf_pc'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

            $sumStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type']);
            $sumArea = SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type']);
            $sumRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type']);
            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // MCC
            if (isset($data['targetOldMcc']) && $data['targetOldMcc'] > 0) {
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc - $data['targetOldMcc'],
                    'sum_target_store' => $sumTargetStore - $data['targetOldMcc'],
                    'sum_target_area' => $sumTargetArea - $data['targetOldMcc'],
                    'sum_target_region' => $sumTargetRegion - $data['targetOldMcc'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc + $data['target_mcc'],
                    'sum_target_store' => $targetAfter->sum_target_store + $data['target_mcc'],
                    'sum_target_area' => $targetAfter->sum_target_area + $data['target_mcc'],
                    'sum_target_region' => $targetAfter->sum_target_region + $data['target_mcc'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc + $data['target_mcc'],
                    'sum_target_store' => $sumTargetStore + $data['target_mcc'],
                    'sum_target_area' => $sumTargetArea + $data['target_mcc'],
                    'sum_target_region' => $sumTargetRegion + $data['target_mcc'],
                ]);
            }

            // Product Focus MCC
            if (isset($data['targetOldPfMcc']) && $data['targetOldPfMcc'] > 0) {
                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc - $data['targetOldPfMcc'],
                ]);

                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc + $data['target_pf_mcc'],
                ]);
            }else{
                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc + $data['target_pf_mcc'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

        }else{ // DELETE

            // DA
            if (isset($data['target_da']) && $data['target_da'] > 0) {
                $targetAfter->update([
                    'target_da' => $targetAfter->target_da - $data['target_da'],
                    'sum_target_store' => $sumTargetStore - $data['target_da'],
                    'sum_target_area' => $sumTargetArea - $data['target_da'],
                    'sum_target_region' => $sumTargetRegion - $data['target_da'],
                ]);
            }

            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // PC
            if (isset($data['target_pc']) && $data['target_pc'] > 0) {
                $targetAfter->update([
                    'target_pc' => $targetAfter->target_pc - $data['target_pc'],
                    'sum_target_store' => $sumTargetStore - $data['target_pc'],
                    'sum_target_area' => $sumTargetArea - $data['target_pc'],
                    'sum_target_region' => $sumTargetRegion - $data['target_pc'],
                ]);
            }

            $sumTargetStore = SummaryTargetActual::where('storeId',$target->storeId)->where('sell_type', $data['sell_type'])->first()->sum_target_store;
            $sumTargetArea =  SummaryTargetActual::where('area_id', $target->area_id)->where('sell_type', $data['sell_type'])->first()->sum_target_area;
            $sumTargetRegion = SummaryTargetActual::where('region_id', $target->region_id)->where('sell_type', $data['sell_type'])->first()->sum_target_region;

            // MCC
            if (isset($data['target_mcc']) && $data['target_mcc'] > 0) {
                $targetAfter->update([
                    'target_mcc' => $targetAfter->target_mcc - $data['target_mcc'],
                    'sum_target_store' => $sumTargetStore - $data['target_mcc'],
                    'sum_target_area' => $sumTargetArea - $data['target_mcc'],
                    'sum_target_region' => $sumTargetRegion - $data['target_mcc'],
                ]);
            }

            // Product Focus DA
            if (isset($data['target_pf_da']) && $data['target_pf_da'] > 0) {
                $targetAfter->update([
                    'target_pf_da' => $targetAfter->target_pf_da - $data['target_pf_da'],
                ]);
            }

            // Product Focus PC
            if (isset($data['target_pf_pc']) && $data['target_pf_pc'] > 0) {
                $targetAfter->update([
                    'target_pf_pc' => $targetAfter->target_pf_pc - $data['target_pf_pc'],
                ]);
            }

            // Product Focus MCC
            if (isset($data['target_pf_mcc']) && $data['target_pf_mcc'] > 0) {
                $targetAfter->update([
                    'target_pf_mcc' => $targetAfter->target_pf_mcc - $data['target_pf_mcc'],
                ]);
            }

            $targetAfter = SummaryTargetActual::where('id', $target->id)->first();

            // Update Sum Target Store to All Summary
            $sumStore->update([
                'sum_target_store' => $targetAfter->sum_target_store,
            ]);

            $sumArea->update([
                'sum_target_area' => $targetAfter->sum_target_area,
            ]);

            $sumRegion->update([
                'sum_target_region' => $targetAfter->sum_target_region,
            ]);

        }

        /* Check if Promoter was hybrid or not */
        if($targetAfter->title_of_promoter == 'HYBRID' || $targetAfter->title_of_promoter == 'DA' || $targetAfter->title_of_promoter == 'PC') {

            $targetAfter->update([
                'target_dapc' => $targetAfter->target_da + $targetAfter->target_pc
            ]);
        }else{

            if($targetAfter->target_dapc > 0){
                $targetAfter->update([
                    'target_dapc' => 0
                ]);
            }

        }

        /* Create Weekly Target */
        $total['da'] = $targetAfter->target_da;
        $total['pc'] = $targetAfter->target_pc;
        $total['mcc'] = $targetAfter->target_mcc;

        $this->changeWeekly($targetAfter, $total);

        /* Reset Actual */
        $this->resetActual($data['user_id'], $data['store_id'], $data['sell_type']);


    }

    public function changeTargetSalesman($data, $change){

        $target = $this->setHeaderSalesman($data);

        /* Target Add and/or Sum */
        $targetAfter = SalesmanSummaryTargetActual::where('id', $target->id)->first();
        $targetOther = SalesmanSummaryTargetActual::where('id', '!=', $target->id);
        $sumNationalTargetCall = SalesmanSummaryTargetActual::first()->sum_national_target_call;
        $sumNationalTargetActiveOutlet = SalesmanSummaryTargetActual::first()->sum_national_target_active_outlet;
        $sumNationalTargetEffectiveCall = SalesmanSummaryTargetActual::first()->sum_national_target_effective_call;
        $sumNationalTargetSales = SalesmanSummaryTargetActual::first()->sum_national_target_sales;
        $sumNationalTargetSalesPf = SalesmanSummaryTargetActual::first()->sum_national_target_sales_pf;

        /* Add / Sum All Target */
        if($change == 'change'){ // INSERT / UPDATE

            // Call
            if (isset($data['targetOldCall']) && $data['targetOldCall'] > 0) {
                $targetAfter->update([
                    'target_call' => $targetAfter->target_call - $data['targetOldCall'],
                    'sum_national_target_call' => $sumNationalTargetCall - $data['targetOldCall'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_call' => $targetAfter->target_call + $data['target_call'],
                    'sum_national_target_call' => $targetAfter->sum_national_target_call + $data['target_call'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_call' => $targetAfter->target_call + $data['target_call'],
                    'sum_national_target_call' => $sumNationalTargetCall + $data['target_call'],
                ]);
            }

            // Active Outlet
            if (isset($data['targetOldActiveOutlet']) && $data['targetOldActiveOutlet'] > 0) {
                $targetAfter->update([
                    'target_active_outlet' => $targetAfter->target_active_outlet - $data['targetOldActiveOutlet'],
                    'sum_national_target_active_outlet' => $sumNationalTargetActiveOutlet - $data['targetOldActiveOutlet'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_active_outlet' => $targetAfter->target_active_outlet + $data['target_active_outlet'],
                    'sum_national_target_active_outlet' => $targetAfter->sum_national_target_active_outlet + $data['target_active_outlet'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_active_outlet' => $targetAfter->target_active_outlet + $data['target_active_outlet'],
                    'sum_national_target_active_outlet' => $sumNationalTargetActiveOutlet + $data['target_active_outlet'],
                ]);
            }

            // Effective Call
            if (isset($data['targetOldEffectiveCall']) && $data['targetOldEffectiveCall'] > 0) {
                $targetAfter->update([
                    'target_effective_call' => $targetAfter->target_effective_call - $data['targetOldEffectiveCall'],
                    'sum_national_target_effective_call' => $sumNationalTargetEffectiveCall - $data['targetOldEffectiveCall'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_effective_call' => $targetAfter->target_effective_call + $data['target_effective_call'],
                    'sum_national_target_effective_call' => $targetAfter->sum_national_target_effective_call + $data['target_effective_call'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_effective_call' => $targetAfter->target_effective_call + $data['target_effective_call'],
                    'sum_national_target_effective_call' => $sumNationalTargetEffectiveCall + $data['target_effective_call'],
                ]);
            }

            // Sales
            if (isset($data['targetOldSales']) && $data['targetOldSales'] > 0) {
                $targetAfter->update([
                    'target_sales' => $targetAfter->target_sales - $data['targetOldSales'],
                    'sum_national_target_sales' => $sumNationalTargetSales - $data['targetOldSales'],
                ]);

                // Sum Target
                $targetAfter->update([
                    'target_sales' => $targetAfter->target_sales + $data['target_sales'],
                    'sum_national_target_sales' => $targetAfter->sum_national_target_sales + $data['target_sales'],
                ]);
            }else{
                 // Sum Target
                $targetAfter->update([
                    'target_sales' => $targetAfter->target_sales + $data['target_sales'],
                    'sum_national_target_sales' => $sumNationalTargetSales + $data['target_sales'],
                ]);
            }

            // Product Focus Sales
            if (isset($data['targetOldSalesPf']) && $data['targetOldSalesPf'] > 0) {
                $targetAfter->update([
                    'target_sales_pf' => $targetAfter->target_sales_pf - $data['targetOldSalesPf'],
                    'sum_national_target_sales_pf' => $sumNationalTargetSalesPf - $data['targetOldSalesPf'],
                ]);

                $targetAfter->update([
                    'target_sales_pf' => $targetAfter->target_sales_pf + $data['target_sales_pf'],
                    'sum_national_target_sales_pf' => $targetAfter->sum_national_target_sales_pf + $data['target_sales_pf'],
                ]);
            }else{
                $targetAfter->update([
                    'target_sales_pf' => $targetAfter->target_sales_pf + $data['target_sales_pf'],
                    'sum_national_target_sales_pf' => $sumNationalTargetSalesPf + $data['target_sales_pf'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $targetOther->update([
                'sum_national_target_call' => $targetAfter->sum_national_target_call,
                'sum_national_target_active_outlet' => $targetAfter->sum_national_target_active_outlet,
                'sum_national_target_effective_call' => $targetAfter->sum_national_target_effective_call,
                'sum_national_target_sales' => $targetAfter->sum_national_target_sales,
                'sum_national_target_sales_pf' => $targetAfter->sum_national_target_sales_pf,
            ]);

        }else{ // DELETE

            // Call
            if (isset($data['target_call']) && $data['target_call'] > 0) {
                 // Delete target
                $targetAfter->update([
                    'target_call' => $targetAfter->target_call - $data['target_call'],
                    'sum_national_target_call' => $sumNationalTargetCall - $data['target_call'],
                ]);
            }

            // Active Outlet
            if (isset($data['target_active_outlet']) && $data['target_active_outlet'] > 0) {
                 // Delete Target
                $targetAfter->update([
                    'target_active_outlet' => $targetAfter->target_active_outlet - $data['target_active_outlet'],
                    'sum_national_target_active_outlet' => $sumNationalTargetActiveOutlet - $data['target_active_outlet'],
                ]);
            }

            // Effective Call
            if (isset($data['target_effective_call']) && $data['target_effective_call'] > 0) {
                // Delete Target
                $targetAfter->update([
                    'target_effective_call' => $targetAfter->target_effective_call - $data['target_effective_call'],
                    'sum_national_target_effective_call' => $sumNationalTargetEffectiveCall - $data['target_effective_call'],
                ]);
            }

            // Sales
            if (isset($data['target_sales']) && $data['target_sales'] > 0) {
                // Delete Target
                $targetAfter->update([
                    'target_sales' => $targetAfter->target_sales - $data['target_sales'],
                    'sum_national_target_sales' => $sumNationalTargetSales - $data['target_sales'],
                ]);
            }

            // Product Focus Sales
            if (isset($data['target_sales_pf']) && $data['target_sales_pf'] > 0) {
                // Delete Target
                $targetAfter->update([
                    'target_sales_pf' => $targetAfter->target_sales_pf - $data['target_sales_pf'],
                    'sum_national_target_sales_pf' => $sumNationalTargetSalesPf - $data['target_sales_pf'],
                ]);
            }

            // Update Sum Target Store to All Summary
            $targetOther->update([
                'sum_national_target_call' => $targetAfter->sum_national_target_call,
                'sum_national_target_active_outlet' => $targetAfter->sum_national_target_active_outlet,
                'sum_national_target_effective_call' => $targetAfter->sum_national_target_effective_call,
                'sum_national_target_sales' => $targetAfter->sum_national_target_sales,
                'sum_national_target_sales_pf' => $targetAfter->sum_national_target_sales_pf,
            ]);

        }


        /* Reset Actual */
        $this->resetActualSalesman($data['user_id']);

    }


}
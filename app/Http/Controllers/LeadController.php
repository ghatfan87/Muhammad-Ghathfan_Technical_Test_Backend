<?php

namespace App\Http\Controllers;

use App\Exceptions\Handler;
use App\Helpers\ApiFormatter;
use App\Models\Lead;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $leads = Lead::all();

            if ($leads->isEmpty()) {
                return ApiFormatter::createAPI(404, 'No Leads Found');
            } else {
                return ApiFormatter::createAPI(200, 'Success', $leads);
            }
        } catch (Exception $e) {
            return ApiFormatter::createAPI(500, 'Internal Server Error');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        try {
            $request->validate([
                'lead_name' => 'required|string|max:255',
                'email' => 'required|email|unique:leads,email',
                'phone_number' => 'required|string|max:15',
                'sales_type' => 'required|in:residential,commercial,both',
                'notes' => 'required|string',
                'survey_status' => 'nullable|in:New Lead,Follow Up,Survey Request,Survey Approved,Survey Rejected','Survey Completed','Final Proposal','Deal'
            ]);
    
            $salesTypeRequired = $request->sales_type;
    
            $nextSalesperson = User::where('role', 'salesperson')
                ->where(function($query) use ($salesTypeRequired) {
                    $query->where('sales_type', $salesTypeRequired)
                          ->orWhere('sales_type', 'both');
                })
                ->orderBy('last_assigned', 'asc')
                ->first();
    
            if (!$nextSalesperson) {
                Log::error("No suitable salesperson found for sales type: " . $salesTypeRequired);
                return ApiFormatter::createAPI(400, 'No suitable Salesperson available');
            }
    
            $lead = Lead::create([
                'lead_name' => $request->lead_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'sales_type' => $salesTypeRequired,
                'notes' => $request->notes,
                'assigned_to' => $nextSalesperson->id,
                'survey_status' => $request->survey_status ?? 'New Leads', 
            ]);
    
            $nextSalesperson->last_assigned = now();
            $nextSalesperson->save();
    
            return ApiFormatter::createAPI(200, 'Lead created and assigned successfully', $lead);
    
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Failed to create lead', $error->getMessage());
        }
    }
    
        
    


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function show(Lead $lead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function edit(Lead $lead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        try {
            // Validasi input
            $request->validate([
                'lead_name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:leads,email,' . $id,
                'phone_number' => 'nullable|string|max:15',
                'sales_type' => 'nullable|in:residential,commercial,both',
                'notes' => 'nullable|string',
                'survey_status' => 'nullable|in:New LeadS,Follow Up,Survey Request,Survey Approved,Survey Rejected,Survey Completed,Final Proposal,Deal',
                'assigned_to' => 'nullable|exists:users,id' 
            ]);
    
            $lead = Lead::findOrFail($id);
    
            $lead->lead_name = $request->lead_name ?? $lead->lead_name;
            $lead->email = $request->email ?? $lead->email;
            $lead->phone_number = $request->phone_number ?? $lead->phone_number;
            $lead->sales_type = $request->sales_type ?? $lead->sales_type;
            $lead->notes = $request->notes ?? $lead->notes;
            $lead->survey_status = $request->survey_status ?? $lead->survey_status;
    
            // Update assigned_to jika diberikan
            if ($request->has('assigned_to')) {
                $newSalesperson = User::where('id', $request->assigned_to)
                                      ->where('role', 'salesperson')
                                      ->first();
    
                if (!$newSalesperson) {
                    return ApiFormatter::createAPI(400, 'The selected user is not a Salesperson or does not exist');
                }
    
                $lead->assigned_to = $newSalesperson->id;
    
                // Update waktu terakhir assigned untuk Salesperson baru
                $newSalesperson->last_assigned = now();
                $newSalesperson->save();
            }
    
            $lead->save();
    
            return ApiFormatter::createAPI(200, 'salesperson updated successfully', $lead);
    
        } catch (Exception $e) {
            return ApiFormatter::createAPI(404, 'Lead not found');
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Failed to update lead', $error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $leads = Lead::findOrFail($id);
            $proses = $leads->delete();

            if($proses) {
                return ApiFormatter::createAPI(200, 'success delete data!');
            } else {
                return ApiFormatter::createAPI(400, 'Failed');
            }
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Failed',$error->getMessage());
        }
    }
    
}

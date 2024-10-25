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
            // Validasi input lead  
            $request->validate([
                'lead_name' => 'required|string|max:255',
                'email' => 'required|email|unique:leads,email',
                'phone_number' => 'required|string|max:15',
                'sales_type' => 'required|in:residential,commercial,both',
                'notes' => 'required|string',
                'survey_status' => 'nullable|in:Requested,Approved,Rejected' // Tambahkan survey_status dengan nilai opsional
            ]);
    
            $salesTypeRequired = $request->sales_type;
            Log::info("Sales type required: " . $salesTypeRequired);
    
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
    
            // Buat lead baru dan assign ke salesperson yang ditemukan
            $lead = Lead::create([
                'lead_name' => $request->lead_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'sales_type' => $salesTypeRequired,
                'notes' => $request->notes,
                'assigned_to' => $nextSalesperson->id,
                'survey_status' => $request->survey_status ?? 'Requested', // Default ke `requested` jika kosong
            ]);
    
            $nextSalesperson->last_assigned = now();
            $nextSalesperson->save();
    
            return ApiFormatter::createAPI(200, 'Lead created and assigned successfully', $lead);
    
        } catch (Exception $error) {
            Log::error("Error creating lead: " . $error->getMessage());
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
    public function update(Request $request, Lead $lead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lead  $lead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lead $lead)
    {
        //
    }
}

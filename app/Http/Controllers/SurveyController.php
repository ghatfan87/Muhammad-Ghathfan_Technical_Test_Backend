<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use App\Models\Lead;
use App\Models\Survey;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SurveyController extends Controller
{

    public function requestSurvey(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'requested_by' => 'required|exists:users,id',
            ]);
    
            // Update status lead menjadi "survey_request"
            $lead = Lead::findOrFail($request->lead_id);
            $lead->survey_status = 'Requested';
            $lead->save();
    
            // Membuat entri survei baru dan menyimpan lead_id
            $survey = Survey::create([
                'lead_id' => $lead->id, // Menyimpan lead_id
                'requested_by' => $request->requested_by,
                'survey_status' => 'Requested', // Atau status awal lain yang sesuai
                // Tambahkan kolom lain yang diperlukan
            ]);
    
            return ApiFormatter::createAPI(200, 'Survey requested successfully', [
                'lead' => $lead,
                'survey' => $survey,
            ]);
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Survey request failed', $error->getMessage());
        }
    }

    public function updateSurveyStatus(Request $request, $surveyId)
    {
        try {
            $request->validate([
                'survey_status' => 'required|in:Requested,Approved,Rejected',
                'approved_by' => 'nullable|exists:users,id',
                'rejected_by' => 'nullable|exists:users,id'
            ]);
    
            // Log ID yang diterima untuk debugging
            Log::info("Updating survey status: ", [
                'surveyId' => $surveyId,
                'approved_by' => $request->approved_by,
                'rejected_by' => $request->rejected_by,
            ]);
    
            $survey = Survey::findOrFail($surveyId);
            $survey->survey_status = $request->survey_status;
            $survey->approved_by = $request->approved_by;
    
            // Hanya assign rejected_by jika ada nilai
            if ($request->has('rejected_by')) {
                $survey->rejected_by = $request->rejected_by; // Pastikan ini valid
            }
    
            $survey->save();
    
            // Update status lead sesuai dengan status survey
            $lead = Lead::findOrFail($survey->lead_id);
            $lead->survey_status = $request->survey_status;
            $lead->save();
    
            return ApiFormatter::createAPI(200, 'Survey status updated successfully', $survey);
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Survey status update failed', $error->getMessage());
        }
    }

    public function completeSurvey(Request $request, $surveyId)
    {
        try {
            $request->validate([
                'notes' => 'required|string|max:500',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $survey = Survey::findOrFail($surveyId);

            // Proses unggah gambar
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('survey_images', 'public');
                $survey->image_path = $path;
            }

            $survey->notes = $request->notes;
            $survey->survey_status = 'survey_completed';
            $survey->save();

            // Update status lead menjadi "survey_completed"
            $lead = Lead::findOrFail($survey->lead_id);
            $lead->survey_status = 'survey_completed';
            $lead->save();

            return ApiFormatter::createAPI(200, 'Survey completed successfully', $survey);
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Survey completion failed', $error->getMessage());
        }
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function show(Survey $survey)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function edit(Survey $survey)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Survey $survey)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Survey  $survey
     * @return \Illuminate\Http\Response
     */
    public function destroy(Survey $survey)
    {
        //
    }
}

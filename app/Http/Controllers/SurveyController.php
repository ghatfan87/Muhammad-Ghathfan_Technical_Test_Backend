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

    public function index()
    {
        try {
            $survey = Lead::all();

            if ($survey->isEmpty()) {
                return ApiFormatter::createAPI(404, 'No Leads Found');
            } else {
                return ApiFormatter::createAPI(200, 'Success', $survey);
            }
        } catch (Exception $e) {
            return ApiFormatter::createAPI(500, 'Internal Server Error');
        }
    }

    public function requestSurvey(Request $request)
    {
        try {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'requested_by' => 'required|exists:users,id',
            ]);

            $lead = Lead::findOrFail($request->lead_id);
            $lead->survey_status = 'Survey Request';
            $lead->save();

            $survey = Survey::create([
                'lead_id' => $lead->id,
                'requested_by' => $request->requested_by,
                'survey_status' => 'Survey Request',
            ]);

            return ApiFormatter::createAPI(200, 'Survey requested successfully', $survey,);
        } catch (Exception $error) {
            return ApiFormatter::createAPI(400, 'Survey request failed', $error->getMessage());
        }
    }

    public function updateSurveyStatus(Request $request, $surveyId)
    {
        try {
            $request->validate([
                'survey_status' => 'nullable|in:New Leads,Follow Up,Survey Request,Survey Approved,Survey Rejected,Survey Completed,Final Proposal,Deal',
                'approved_by' => 'nullable|exists:users,id',
                'rejected_by' => 'nullable|exists:users,id'
            ]);

            $survey = Survey::findOrFail($surveyId);

            if ($request->role == 'operational') {
                return ApiFormatter::createAPI(403, 'only operational can approve or reject surveys');
            }

            $survey->survey_status = $request->survey_status;
            $survey->approved_by = $request->approved_by;

            if ($request->has('rejected_by')) {
                $survey->rejected_by = $request->rejected_by;
            }

            $survey->save();

            $lead = Lead::findOrFail($survey->lead_id);
            $lead->survey_status = $request->survey_status;
            $lead->save();

            return ApiFormatter::createAPI(200, 'Survey status updated successfully', $survey);
        } catch (Exception $error) {
            Log::error("Error updating survey status: " . $error->getMessage());
            return ApiFormatter::createAPI(400, 'Survey status update failed', $error->getMessage());
        }
    }

    public function completeSurvey(Request $request, $surveyId)
    {
        try {
            $request->validate([
                'notes' => 'nullable|string|max:500',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $survey = Survey::findOrFail($surveyId);

            // Proses unggah gambar
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('survey_images', 'public');
                $survey->image_path = $path;
            }

            $survey->notes = $request->notes;
            $survey->survey_status = 'Survey Completed';
            $survey->save();

            $lead = Lead::findOrFail($survey->lead_id);
            $lead->survey_status = 'Survey Completed';
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

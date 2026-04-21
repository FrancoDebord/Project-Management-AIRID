<?php

namespace App\Http\Controllers;

use App\Models\Pro_DmDatabase;
use App\Models\Pro_DmDataloggerFile;
use App\Models\Pro_DmDataloggerValidation;
use App\Models\Pro_DmDoubleEntry;
use App\Models\Pro_DmPcAssignment;
use App\Models\Pro_DmSoftwareValidation;
use App\Models\Pro_DmSoftwareValidationFile;
use App\Models\Pro_Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataManagementController extends Controller
{
    // ────────────────────────────────────────────────────────────────────────
    //  DATABASES
    // ────────────────────────────────────────────────────────────────────────

    public function saveDatabase(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:pro_projects,id',
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:lab_test,field,experimental,case_data,other',
        ]);

        $data = $request->only(['project_id', 'name', 'type', 'lab_test_id', 'description']);

        if ($request->filled('id')) {
            $db = Pro_DmDatabase::findOrFail($request->input('id'));
            $db->update($data);
        } else {
            $db = Pro_DmDatabase::create($data);
        }

        return response()->json(['code_erreur' => 0, 'message' => 'Base de données enregistrée.', 'data' => $db]);
    }

    public function deleteDatabase(Request $request)
    {
        $db = Pro_DmDatabase::findOrFail($request->input('id'));
        $db->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Base de données supprimée.']);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  PC ASSIGNMENTS
    // ────────────────────────────────────────────────────────────────────────

    public function savePcAssignment(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|integer|exists:pro_projects,id',
            'pc_name'     => 'required|string|max:255',
            'assigned_at' => 'required|date',
        ]);

        $data = array_merge(
            $request->only(['project_id', 'pc_name', 'pc_serial', 'is_glp', 'assigned_at', 'returned_at', 'reason_for_return', 'notes']),
            ['assigned_by' => auth()->id()]
        );
        $data['is_glp'] = (bool) ($data['is_glp'] ?? false);

        if ($request->filled('id')) {
            $pc = Pro_DmPcAssignment::findOrFail($request->input('id'));
            $pc->update($data);
        } else {
            $pc = Pro_DmPcAssignment::create($data);
        }

        return response()->json(['code_erreur' => 0, 'message' => 'Attribution PC enregistrée.', 'data' => $pc]);
    }

    public function returnPc(Request $request)
    {
        $request->validate([
            'id'          => 'required|integer|exists:pro_dm_pc_assignments,id',
            'returned_at' => 'required|date',
        ]);

        $pc = Pro_DmPcAssignment::findOrFail($request->input('id'));
        $pc->update([
            'returned_at'       => $request->input('returned_at'),
            'reason_for_return' => $request->input('reason_for_return'),
        ]);

        return response()->json(['code_erreur' => 0, 'message' => 'PC retourné enregistré.']);
    }

    public function deletePcAssignment(Request $request)
    {
        $pc = Pro_DmPcAssignment::findOrFail($request->input('id'));
        $pc->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Attribution supprimée.']);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  SOFTWARE / DATABASE VALIDATIONS
    // ────────────────────────────────────────────────────────────────────────

    public function saveSoftwareValidation(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|integer|exists:pro_projects,id',
            'software_name' => 'required|string|max:255',
        ]);

        $fields = [
            'project_id', 'database_id', 'computer_id', 'software_name',
            'validation_date', 'validation_done_by', 'reason_for_validation',
            'current_software_version', 'operating_system', 'cpu', 'ram',
            'is_recorded_in_computer', 'validation_kit_status',
            'validation_folder_name', 'validation_file_name',
            'sop_document_code', 'sop_section',
            'env_temperature', 'env_humidity', 'data_logger_env',
            'details_of_procedure', 'status',
        ];
        $data = $request->only($fields);
        $data['is_recorded_in_computer'] = (bool) ($data['is_recorded_in_computer'] ?? false);

        if ($request->filled('id')) {
            $val = Pro_DmSoftwareValidation::findOrFail($request->input('id'));
            $val->update($data);
        } else {
            $val = Pro_DmSoftwareValidation::create($data);
        }

        return response()->json(['code_erreur' => 0, 'message' => 'Validation logicielle enregistrée.', 'data' => $val]);
    }

    public function uploadSoftwareValidationFile(Request $request)
    {
        $request->validate([
            'validation_id' => 'required|integer|exists:pro_dm_software_validations,id',
            'file'          => 'required|file|max:20480',
        ]);

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path         = $file->store('dm/software-validations', 'public');

        $record = Pro_DmSoftwareValidationFile::create([
            'validation_id' => $request->input('validation_id'),
            'file_path'     => $path,
            'original_name' => $originalName,
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json(['code_erreur' => 0, 'message' => 'Fichier téléversé.', 'data' => $record]);
    }

    public function deleteSoftwareValidationFile(Request $request)
    {
        $file = Pro_DmSoftwareValidationFile::findOrFail($request->input('id'));
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Fichier supprimé.']);
    }

    public function deleteSoftwareValidation(Request $request)
    {
        $val = Pro_DmSoftwareValidation::with('files')->findOrFail($request->input('id'));
        foreach ($val->files as $f) {
            Storage::disk('public')->delete($f->file_path);
        }
        $val->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Validation supprimée.']);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  DATA LOGGER VALIDATIONS
    // ────────────────────────────────────────────────────────────────────────

    public function saveDataloggerValidation(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer|exists:pro_projects,id',
            'name'       => 'required|string|max:255',
        ]);

        $data = $request->only([
            'project_id', 'name', 'serial_number', 'location',
            'validation_date', 'validated_by', 'notes', 'status',
        ]);

        if ($request->filled('id')) {
            $dl = Pro_DmDataloggerValidation::findOrFail($request->input('id'));
            $dl->update($data);
        } else {
            $dl = Pro_DmDataloggerValidation::create($data);
        }

        return response()->json(['code_erreur' => 0, 'message' => 'Validation Data Logger enregistrée.', 'data' => $dl]);
    }

    public function uploadDataloggerFile(Request $request)
    {
        $request->validate([
            'datalogger_validation_id' => 'required|integer|exists:pro_dm_datalogger_validations,id',
            'file'                     => 'required|file|max:20480',
        ]);

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $path         = $file->store('dm/datalogger-validations', 'public');

        $record = Pro_DmDataloggerFile::create([
            'datalogger_validation_id' => $request->input('datalogger_validation_id'),
            'file_path'                => $path,
            'original_name'            => $originalName,
            'uploaded_by'              => auth()->id(),
        ]);

        return response()->json(['code_erreur' => 0, 'message' => 'Fichier téléversé.', 'data' => $record]);
    }

    public function deleteDataloggerFile(Request $request)
    {
        $file = Pro_DmDataloggerFile::findOrFail($request->input('id'));
        Storage::disk('public')->delete($file->file_path);
        $file->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Fichier supprimé.']);
    }

    public function deleteDataloggerValidation(Request $request)
    {
        $dl = Pro_DmDataloggerValidation::with('files')->findOrFail($request->input('id'));
        foreach ($dl->files as $f) {
            Storage::disk('public')->delete($f->file_path);
        }
        $dl->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Validation Data Logger supprimée.']);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  DOUBLE DATA ENTRIES
    // ────────────────────────────────────────────────────────────────────────

    public function saveDoubleEntry(Request $request)
    {
        $request->validate([
            'project_id'        => 'required|integer|exists:pro_projects,id',
            'first_entry_date'  => 'required|date',
            'first_entry_by'    => 'required|string|max:500',
            'second_entry_date' => 'required|date',
            'second_entry_by'   => 'required|string|max:500',
        ]);

        $data = $request->only([
            'project_id', 'database_id',
            'first_entry_date', 'first_entry_by',
            'second_entry_date', 'second_entry_by',
            'is_compliant', 'comments',
        ]);

        if (isset($data['is_compliant']) && $data['is_compliant'] !== '') {
            $data['is_compliant'] = (bool) $data['is_compliant'];
        } else {
            $data['is_compliant'] = null;
        }

        if ($request->filled('id')) {
            $entry = Pro_DmDoubleEntry::findOrFail($request->input('id'));

            // Handle file upload if present
            if ($request->hasFile('comparison_file')) {
                if ($entry->comparison_file_path) {
                    Storage::disk('public')->delete($entry->comparison_file_path);
                }
                $file = $request->file('comparison_file');
                $data['comparison_file_path'] = $file->store('dm/double-entries', 'public');
                $data['comparison_file_name'] = $file->getClientOriginalName();
            }

            $entry->update($data);
        } else {
            if ($request->hasFile('comparison_file')) {
                $file = $request->file('comparison_file');
                $data['comparison_file_path'] = $file->store('dm/double-entries', 'public');
                $data['comparison_file_name'] = $file->getClientOriginalName();
            }
            $entry = Pro_DmDoubleEntry::create($data);
        }

        return response()->json(['code_erreur' => 0, 'message' => 'Double saisie enregistrée.', 'data' => $entry]);
    }

    public function deleteDoubleEntry(Request $request)
    {
        $entry = Pro_DmDoubleEntry::findOrFail($request->input('id'));
        if ($entry->comparison_file_path) {
            Storage::disk('public')->delete($entry->comparison_file_path);
        }
        $entry->delete();
        return response()->json(['code_erreur' => 0, 'message' => 'Double saisie supprimée.']);
    }

    // ────────────────────────────────────────────────────────────────────────
    //  SOFTWARE VALIDATION PDF
    // ────────────────────────────────────────────────────────────────────────

    public function softwareValidationPdf(Request $request, int $id)
    {
        $validation = Pro_DmSoftwareValidation::with(['project', 'database', 'files'])->findOrFail($id);
        $project    = $validation->project;

        $headerImagePath = 'file://' . str_replace('\\', '/', public_path('storage/assets/header/entete_airid.png'));

        $pdf = Pdf::loadView('pdf.software-validation-checklist', compact('validation', 'project', 'headerImagePath'))
            ->setPaper('a4', 'portrait');

        $safeCode = str_replace(['/', '\\', ' '], '-', $project->project_code ?? $id);
        $filename = 'Software-Validation-' . $safeCode . '-' . $id . '.pdf';

        return $pdf->download($filename);
    }
}

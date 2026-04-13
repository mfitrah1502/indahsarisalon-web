<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Treatment;
use App\Models\Category;

class TrashController extends Controller
{
    /**
     * Restore the specified resource from trash.
     */
    public function restore($type, $id)
    {
        try {
            if ($type === 'karyawan' || $type === 'pelanggan') {
                $user = User::onlyTrashed()->findOrFail($id);
                $user->restore();
                return response()->json(['success' => true, 'message' => ucfirst($type) . ' berhasil dipulihkan!']);
            } elseif ($type === 'treatment') {
                $treatment = Treatment::onlyTrashed()->findOrFail($id);
                $treatment->restore();
                return response()->json(['success' => true, 'message' => 'Treatment berhasil dipulihkan!']);
            }
            return response()->json(['success' => false, 'message' => 'Tipe data tidak valid.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memulihkan data: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource completely.
     */
    public function forceDelete($type, $id)
    {
        try {
            if ($type === 'karyawan' || $type === 'pelanggan') {
                $user = User::onlyTrashed()->findOrFail($id);
                $user->forceDelete();
                return response()->json(['success' => true, 'message' => ucfirst($type) . ' berhasil dihapus permanen!']);
            } elseif ($type === 'treatment') {
                $treatment = Treatment::onlyTrashed()->findOrFail($id);
                // Kita tidak hapus gambarnya biar gampang, as per usual soft delete practice 
                $treatment->forceDelete();
                return response()->json(['success' => true, 'message' => 'Treatment berhasil dihapus permanen!']);
            }
            return response()->json(['success' => false, 'message' => 'Tipe data tidak valid.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}

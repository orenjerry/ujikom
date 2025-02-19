<?php

namespace App\Http\Controllers;

use App\Models\Foto;
use App\Models\Like;
use App\Models\Users;
use App\Models\UsersRejectReason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function showDashboard(Request $request)
    {
        $filter = $request->query('filter');
        $search = $request->query('search');

        $query = Foto::with('user')
            ->withCount('like', 'komen')
            ->with('like');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul_foto', 'LIKE', '%' . $search . '%')
                    ->orWhere('deskripsi_foto', 'LIKE', '%' . $search . '%');
            });
        }

        switch ($filter) {
            case 'likes_desc':
                $query->orderBy('like_count', 'desc');
                break;
            case 'likes_asc':
                $query->orderBy('like_count', 'asc');
                break;
            case 'komen_desc':
                $query->orderBy('komen_count', 'desc');
                break;
            case 'komen_asc':
                $query->orderBy('komen_count', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'only_liked':
                $query->whereHas('like', function ($q) {
                    $q->where('id_user', Session::get('user_id'));
                });
                break;
            default:
                $query->inRandomOrder();
                break;
        }

        $foto = $query->get()->map(function ($foto) {
            $foto->is_liked = $foto->like->contains('id_user', Session::get('user_id')) ? true : false;
            return $foto;
        });

        return view('dashboard', compact('foto'));
    }

    public function showAdminDashboard()
    {
        $users = Users::where('accepted', 'nothing')->get();

        $rejected = Users::where('accepted', 'rejected')
            ->with('rejectionReason')
            ->paginate(5);

        return view('admin.dashboard', compact('users', 'rejected'));
    }

    public function approveUser(Request $request, $id)
    {
        $user = Users::findOrFail($id);
        try {
            if ($request->action == 'approve') {
                $approve = 'accepted';
            } elseif ($request->action == 'reject') {
                $approve = 'rejected';
                $reason = $request->reason;
                UsersRejectReason::create([
                    'id_user' => $id,
                    'reason' => $reason,
                ]);
            }
            $user->update([
                'accepted' => $approve,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user status: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update user status');
        }

        return response()->json(['success' => 1, 'message' => 'User status updated'], 200);
    }

    public function markAsRead()
    {
        $userId = Session::get('user_id');
        $user = Users::where('id', $userId)->first();

        $user->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}

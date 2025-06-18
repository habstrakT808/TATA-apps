<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Editor;
use App\Models\Revisi;
use Illuminate\Http\Request;

class PengerjaanController extends Controller
{
    /**
     * Show all revision requests page
     */
    public function showAll()
    {
        $title = 'Manajemen Pengerjaan';
        
        // Get basic stats for the page
        $totalRevisions = Revisi::count();
        
        $pendingRevisions = Revisi::where('status', 'revisi')->count();
        $inProgressRevisions = Revisi::where('status', 'dikerjakan')->count();

        return view('page.pengerjaan.index', compact(
            'title',
            'totalRevisions',
            'pendingRevisions', 
            'inProgressRevisions'
        ));
    }

    /**
     * Show revision detail page
     */
    public function showDetail($uuid)
    {
        $revisi = Revisi::join('pesanan', 'pesanan.id_pesanan', '=', 'revisi.id_pesanan')
            ->where('revisi.uuid', $uuid)
            ->first();

        if (!$revisi) {
            return redirect('/pengerjaan')->with('error', 'Pengerjaan tidak ditemukan');
        }

        // Get available editors
        $availableEditors = Editor::all();

        // Filter revision-related messages
        $revisionMessages = $revisi->chatMessages->filter(function($message) {
            return stripos($message->message, 'revisi') !== false ||
                   stripos($message->message, 'revision') !== false;
        });

        $title = 'Detail Pengerjaan - ' . $revisi->uuid;

        return view('page.pengerjaan.detail', compact(
            'title',
            'revisi',
            'availableEditors',
            'revisionMessages'
        ));
    }

    /**
     * Show revision statistics page
     */
    public function showStatistics()
    {
        $title = 'Statistik Pengerjaan';

        // Get comprehensive statistics
        $totalRevisionRequests = Revisi::count();

        $pendingRevisions = Revisi::where('status', 'revisi')->count();
        $inProgressRevisions = Revisi::where('status', 'dikerjakan')->count();

        $completedRevisions = Revisi::where('status', 'selesai')->count();

        // Most active editors in revisions
        $activeEditors = Editor::withCount(['revisi' => function($q) {
            $q->where('status', 'dikerjakan');
        }])->orderBy('revisi_count', 'desc')->take(10)->get();

        // Recent revision activities
        $recentRevisions = Revisi::orderBy('created_at', 'desc')->take(10)->get();

        return view('page.pengerjaan.statistics', compact(
            'title',
            'totalRevisionRequests',
            'pendingRevisions',
            'inProgressRevisions', 
            'completedRevisions',
            'activeEditors',
            'recentRevisions'
        ));
    }
    /**
     * ADMIN: Get all pesanan that have revision requests (chat-based)
     */
    public function getAllRevisionRequests(Request $request)
    {
        try {
            $status = $request->get('status', 'all');
            $search = $request->get('search');
            $perPage = $request->get('per_page', 15);

            // Get pesanan that have revision-related chat messages
            $query = Pesanan::with(['toUser', 'toJasa', 'toPaketJasa', 'toEditor'])
                ->whereHas('chatMessages', function($q) {
                    $q->where('message', 'like', '%revisi%')
                      ->orWhere('message', 'like', '%revision%')
                      ->orWhere('message', 'like', '%perbaikan%');
                });

            // Filter by status
            if ($status !== 'all') {
                $query->where('status_pesanan', $status);
            }

            // Search by user name or pesanan UUID
            if ($search) {
                $query->whereHas('toUser', function($q) use ($search) {
                    $q->where('nama_user', 'like', "%{$search}%");
                })->orWhere('uuid', 'like', "%{$search}%");
            }

            $pesanan = $query->orderBy('updated_at', 'desc')->paginate($perPage);

            // Add latest revision message for each pesanan
            $pesanan->getCollection()->transform(function ($item) {
                $latestRevisionMessage = $item->chatMessages()
                    ->where(function($q) {
                        $q->where('message', 'like', '%revisi%')
                          ->orWhere('message', 'like', '%revision%')
                          ->orWhere('message', 'like', '%perbaikan%');
                    })
                    ->latest()
                    ->first();
                
                $item->latest_revision_message = $latestRevisionMessage;
                return $item;
            });

            return response()->json([
                'status' => 'success',
                'data' => $pesanan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data pengerjaan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ADMIN: Get revision detail for specific pesanan
     */
    public function getRevisionDetail($uuid)
    {
        try {
            $pesanan = Pesanan::with([
                'toUser',
                'toJasa',
                'toPaketJasa',
                'toEditor',
                'chatMessages' => function($query) {
                    $query->orderBy('created_at', 'asc');
                }
            ])->where('uuid', $uuid)->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Filter revision-related messages
            $revisionMessages = $pesanan->chatMessages->filter(function($message) {
                return stripos($message->message, 'revisi') !== false ||
                       stripos($message->message, 'revision') !== false ||
                       stripos($message->message, 'perbaikan') !== false;
            });

            // Get available editors
            $availableEditors = Editor::all();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'pesanan' => $pesanan,
                    'revision_messages' => $revisionMessages->values(),
                    'all_messages' => $pesanan->chatMessages,
                    'available_editors' => $availableEditors,
                    'revision_count' => $revisionMessages->count(),
                    'max_revisions' => $pesanan->maksimal_revisi
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail pengerjaan: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * ADMIN: Get revision statistics
     */
    public function getRevisionStatistics()
    {
        try {
            // Count pesanan with revision requests
            $totalRevisionRequests = Pesanan::whereHas('chatMessages', function($q) {
                $q->where('message', 'like', '%revisi%')
                  ->orWhere('message', 'like', '%revision%')
                  ->orWhere('message', 'like', '%perbaikan%');
            })->count();

            // Count by status
            $pendingRevisions = Pesanan::where('status_pesanan', 'revisi')->count();
            $inProgressRevisions = Pesanan::where('status_pesanan', 'dikerjakan')
                ->whereHas('chatMessages', function($q) {
                    $q->where('message', 'like', '%revisi%')
                      ->orWhere('message', 'like', '%revision%')
                      ->orWhere('message', 'like', '%perbaikan%');
                })->count();

            // Average revision count per pesanan
            $avgRevisionsPerOrder = ChatMessage::where('message', 'like', '%revisi%')
                ->orWhere('message', 'like', '%revision%')
                ->orWhere('message', 'like', '%perbaikan%')
                ->count() / max($totalRevisionRequests, 1);

            // Most active editors in revisions
            $activeEditors = Editor::withCount(['chatMessages' => function($q) {
                $q->where('message', 'like', '%revisi%')
                  ->orWhere('message', 'like', '%revision%')
                  ->orWhere('message', 'like', '%perbaikan%');
            }])->orderBy('chat_messages_count', 'desc')->take(5)->get();

            $stats = [
                'total_revision_requests' => $totalRevisionRequests,
                'pending_revisions' => $pendingRevisions,
                'in_progress_revisions' => $inProgressRevisions,
                'avg_revisions_per_order' => round($avgRevisionsPerOrder, 2),
                'active_editors' => $activeEditors
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik pengerjaan: ' . $e->getMessage()
            ], 500);
        }
    }
} 
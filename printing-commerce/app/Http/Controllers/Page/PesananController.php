<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\UtilityController;
use App\Models\Admin;
use App\Models\Pesanan;
use App\Models\Editor;
use Carbon\Carbon;
class PesananController extends Controller
{
    public function showAll(Request $request){
        $status = $request->query('status', 'menunggu');
        $statusType = 'pengerjaan'; // Selalu gunakan status pengerjaan
        
        // Validasi status pengerjaan
        $validStatusPengerjaan = ['menunggu', 'diproses', 'dikerjakan', 'selesai'];
        if (!in_array($status, $validStatusPengerjaan)) {
            $status = 'menunggu'; // Default untuk status pengerjaan
        }
        
        if (!$request->has('status')) {
            return redirect('/pesanan?status='.$status);
        }
        
        $orderBy = 'asc';
        $pesananQuery = Pesanan::select(
            'pesanan.uuid', 
            'pesanan.id_pesanan',
            'nama_user', 
            'status_pesanan',
            'status_pengerjaan', 
            'estimasi_waktu',
            'estimasi_mulai',
            'estimasi_selesai',
            'jasa.kategori',
            'paket_jasa.kelas_jasa',
            'catatan_pesanan.catatan_pesanan as deskripsi'
        )
            ->join('jasa', 'jasa.id_jasa', '=', 'pesanan.id_jasa')
            ->join('users', 'users.id_user', '=', 'pesanan.id_user')
            ->join('paket_jasa', 'paket_jasa.id_paket_jasa', '=', 'pesanan.id_paket_jasa')
            ->leftJoin('catatan_pesanan', 'catatan_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->orderBy('pesanan.created_at', $orderBy);
        
        // Filter berdasarkan status pengerjaan
        $pesananQuery->where('status_pengerjaan', $status);
        
        $pesananList = $pesananQuery->get();
        
        $pesananList->each(function($pesanan) {
            $latestEditor = $pesanan->editorFiles()->with('editor')->latest('updated_at')->first();
            $pesanan->nama_editor = $latestEditor ? $latestEditor->editor->nama_editor : '-';
        });
        
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'dataPesanan' => $pesananList,
            'headerData' => UtilityController::getHeaderData(),
            'currentStatus' => $status,
        ];
        
        return view('page.pesanan.data',$dataShow);
    }
    public function showDetail(Request $request, $uuid){
        $pesanan = Pesanan::with([
            'toUser',
            'toJasa',
            'toPaketJasa',
            'toEditor',
            'fromCatatanPesanan',
            'revisions.userFiles',
            'revisions.editorFiles.editor'
        ])->where('pesanan.uuid', $uuid)->first();
        
        if (!$pesanan) {
            return redirect('/pesanan')->with('error', 'Data Pesanan tidak ditemukan');
        }
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'pesananData' => [
                'uuid' => $pesanan->uuid,
                'nama_pelanggan' => $pesanan->toUser->nama_user ?? '-',
                'jenis_jasa' => $pesanan->toJasa->kategori ?? '-',
                'kelas_jasa' => $pesanan->toPaketJasa->kelas_jasa,
                'maksimal_revisi' => $pesanan->maksimal_revisi ?? 0,
                'status_pesanan_list' => ['pending' => 'Menunggu Pembayaran', 'diproses' => 'Proses', 'menunggu_editor' => 'Menunggu Editor', 'dikerjakan' => 'Dikerjakan', 'revisi' => 'Revisi', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'],
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pengerjaan' => $pesanan->status_pengerjaan ?? 'menunggu',
                'revisi_used' => $pesanan->revisi_used,
                'sisa_revisi' => $pesanan->revisi_tersisa,
                'deskripsi' => $pesanan->fromCatatanPesanan->catatan_pesanan ?? '-',
                'gambar_referensi' => $pesanan->fromCatatanPesanan->gambar_referensi ?? null,
                'revisi_editor_terbaru' => $pesanan->latestRevision && $pesanan->latestRevision->editorFiles->count() > 0 ? $pesanan->latestRevision->editorFiles->first()->nama_file : null,
                'file_hasil_desain' => $pesanan->file_hasil_desain,
                'revisions' => $pesanan->revisions ?? [],
                'estimasi_waktu' => [
                    'dari' => $pesanan->estimasi_waktu ? Carbon::parse($pesanan->estimasi_waktu)->format('Y-m-d') : null,
                    'sampai' => $pesanan->estimasi_waktu ? Carbon::parse($pesanan->estimasi_waktu)->format('Y-m-d') : null,
                    'durasi' => $pesanan->toPaketJasa->waktu_pengerjaan ?? '-'
                ],
                'estimasi_mulai' => $pesanan->estimasi_mulai ? Carbon::parse($pesanan->estimasi_mulai)->format('Y-m-d') : null,
                'estimasi_selesai' => $pesanan->estimasi_selesai ? Carbon::parse($pesanan->estimasi_selesai)->format('Y-m-d') : null,
                'id_editor' => $pesanan->id_editor,
                'status' => ucfirst($pesanan->status_pesanan),
                'status_raw' => $pesanan->status_pesanan,
                'editor_assigned' => $pesanan->toEditor,
            ],
            'headerData' => UtilityController::getHeaderData(),
            'editorList' => Editor::select('id_editor', 'nama_editor')->get(),
            'editMode' => $request->query('edit', false),
            'statusConfig' => [
                'pending' => [
                    'showEditor' => false,
                    'showCatatan' => true,
                    'showRevisions' => false,
                    'allowEditStatus' => true,
                    'allowEditEditor' => false,
                    'nextStatuses' => ['diproses', 'dibatalkan']
                ],
                'diproses' => [
                    'showEditor' => false,
                    'showCatatan' => true,
                    'showRevisions' => false,
                    'allowEditStatus' => true,
                    'allowEditEditor' => false,
                    'nextStatuses' => ['menunggu_editor']
                ],
                'menunggu_editor' => [
                    'showEditor' => true,
                    'showCatatan' => true,
                    'showRevisions' => false,
                    'allowEditStatus' => true,
                    'allowEditEditor' => true,
                    'nextStatuses' => ['dikerjakan', 'dibatalkan']
                ],
                'dikerjakan' => [
                    'showEditor' => true,
                    'showCatatan' => true,
                    'showRevisions' => true,
                    'allowEditStatus' => false,
                    'allowEditEditor' => false,
                    'nextStatuses' => ['menunggu_review']
                ],
                'revisi' => [
                    'showEditor' => true,
                    'showCatatan' => true,
                    'showRevisions' => true,
                    'allowEditStatus' => true,
                    'allowEditEditor' => true,
                    'nextStatuses' => ['dikerjakan']
                ],
                'menunggu_review' => [
                    'showEditor' => true,
                    'showCatatan' => true,
                    'showRevisions' => true,
                    'allowEditStatus' => false,
                    'allowEditEditor' => false,
                    'nextStatuses' => []
                ],
                'selesai' => [
                    'showEditor' => true,
                    'showCatatan' => true,
                    'showRevisions' => true,
                    'allowEditStatus' => false,
                    'allowEditEditor' => false,
                    'nextStatuses' => []
                ],
                'dibatalkan' => [
                    'showEditor' => false,
                    'showCatatan' => true,
                    'showRevisions' => false,
                    'allowEditStatus' => false,
                    'allowEditEditor' => false,
                    'nextStatuses' => []
                ]
            ]
        ];
        return view('page.pesanan.detail', $dataShow);
    }
    public function getStatistics()
    {
        try {
            $stats = [
                'total_pesanan' => Pesanan::count(),
                'menunggu' => Pesanan::where('status_pesanan', 'pending')->count(),
                'proses' => Pesanan::where('status_pesanan', 'diproses')->count(),
                'dikerjakan' => Pesanan::where('status_pesanan', 'dikerjakan')->count(),
                'revisi' => Pesanan::where('status_pesanan', 'revisi')->count(),
                'selesai' => Pesanan::where('status_pesanan', 'selesai')->count(),
                'dibatalkan' => Pesanan::where('status_pesanan', 'dibatalkan')->count(),
                'total_revenue' => Pesanan::where('status_pesanan', 'lunas')->sum('total_harga'),
                'pending_payment' => Pesanan::where('status_pesanan', 'menunggu_editor')->count()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}
?>
<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Transaksi::with(['toPesanan', 'toMetodePembayaran']);
        
        // Apply filters if provided
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }
        
        if ($this->status && $this->status != 'all') {
            $query->where('status', $this->status);
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Order ID',
            'Deskripsi Pesanan',
            'Pelanggan',
            'Jumlah',
            'Status',
            'Metode Pembayaran',
            'Tanggal Transaksi',
            'Tanggal Pembayaran',
            'Expired',
        ];
    }
    
    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($row): array
    {
        // Format status for readability
        $status = '';
        if ($row->status == 'belum_bayar') {
            $status = 'Belum Bayar';
        } elseif ($row->status == 'menunggu_konfirmasi') {
            $status = 'Menunggu Konfirmasi';
        } elseif ($row->status == 'lunas') {
            $status = 'Lunas';
        }
        
        // Get customer name
        $customerName = 'N/A';
        if ($row->toPesanan && $row->toPesanan->toUser) {
            $customerName = $row->toPesanan->toUser->name;
        }
        
        // Get order description
        $orderDesc = 'N/A';
        if ($row->toPesanan) {
            $orderDesc = $row->toPesanan->deskripsi;
        }
        
        // Format dates
        $createdAt = $row->created_at ? Carbon::parse($row->created_at)->format('d/m/Y H:i:s') : 'N/A';
        $paymentDate = $row->waktu_pembayaran ? Carbon::parse($row->waktu_pembayaran)->format('d/m/Y H:i:s') : 'N/A';
        $expiredAt = $row->expired_at ? Carbon::parse($row->expired_at)->format('d/m/Y H:i:s') : 'N/A';
        
        // Format currency
        $amount = 'Rp ' . number_format($row->jumlah, 0, ',', '.');
        
        return [
            $row->id_transaksi,
            $row->order_id,
            $orderDesc,
            $customerName,
            $amount,
            $status,
            $row->toMetodePembayaran ? $row->toMetodePembayaran->nama_metode_pembayaran : 'N/A',
            $createdAt,
            $paymentDate,
            $expiredAt,
        ];
    }
    
    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as headers
            1 => ['font' => ['bold' => true]],
        ];
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NasabahController extends Controller
{
    public function getNasabah(Request $request)
    {
        $nasabah = Nasabah::orderBy('id', 'desc');

        if ($request->search != null) {
            $nasabah = $nasabah->where(function ($query) use ($request) {
                $query->where('id', 'like', "%$request->search%");
                $query->orWhere('name', 'like', "%$request->search%");
            });
        }

        return response([
            'data' => $request->isAll != null ? $nasabah->selectRaw('id as value, name as label')->get() : $nasabah->paginate()
        ]);
    }

    public function getNasabahPoint()
    {
        $nasabah = Nasabah::with([
            'Transaction' => function ($query) {
                $query->select('id', 'nasabah_id', 'amount', 'description');
            }
        ])->whereHas('Transaction', function ($query) {
            $query->whereIn('description', ['Beli Pulsa', 'Bayar Listrik']);
        })->paginate()->toArray();


        foreach ($nasabah['data'] as $index => $row) {
            $nasabah['data'][$index]['total_point'] = 0;
            $total_point = $nasabah['data'][$index]['total_point'] ?? 0;
            foreach ($row['transaction'] as $val) {
                if ($val['description'] == 'Beli Pulsa') {
                    if ($val['amount'] <= 10000) {
                        $total_point += 0;
                    } else if ($val['amount'] <= 30000) {
                        $total_point += ($val['amount'] / 1000) * 1;
                    } else {
                        $total_point += ($val['amount'] / 1000) * 2;
                    }
                } else if ($val['description'] == 'Bayar Listrik') {
                    if ($val['amount'] <= 50000) {
                        $total_point += 0;
                    } else if ($val['amount'] <= 100000) {
                        $total_point += ($val['amount'] / 2000) * 1;
                    } else {
                        $total_point += ($val['amount'] / 2000) * 2;
                    }
                }
            }
            $nasabah['data'][$index]['total_point'] += $total_point;
        }

        return response([
            'data' => $nasabah
        ]);
    }

    public function GetNasabahReport(Request $request)
    {
        $trx = Transaction::where('nasabah_id', $request->nasabah_id)
            ->whereDate('transaction_date', '>=', $request->dateStart)
            ->whereDate('transaction_date', '<=', $request->dateEnd);

        if ($request->search != null) {
            $trx = $trx->where(function ($query) use ($request) {
                $query->where('description', 'like', "%$request->search%");
                $query->orWhere('transaction_date', 'like', "%$request->search%");
                $query->orWhere('debit_credit_status', 'like', "%$request->search%");
                $query->orWhere('amount', 'like', "%$request->search%");
            });
        }

        return response([
            'data' => $trx->paginate()
        ]);
    }

    public function makeNasabah(Request $request)
    {
        $id = $request->id;

        DB::beginTransaction();
        try {
            if ($request->id != null) {
                $nasabah = Nasabah::find($id);
            } else {
                $nasabah = new Nasabah();
            }

            $nasabah->name = $request->name;
            $nasabah->save();

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Nasabah berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response([
                'success' => false,
                'message' => 'Internal Server Error',
                'error' => $e
            ]);
        }
    }

    public function deleteNasabah($id)
    {
        $nasabah = Nasabah::find($id);

        if ($nasabah) {
            $nasabah->delete();

            return response([
                'success' => true,
                'message' => 'Nasabah berhasil dihapus'
            ]);
        }

        return response([
            'success' => false,
            'message' => 'Nasabah tidak ditemukan'
        ]);
    }
}

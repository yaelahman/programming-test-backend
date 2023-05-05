<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function getTransaction(Request $request)
    {
        $trx = Transaction::with('Nasabah')->orderBy('id', 'desc');

        if ($request->search != null) {
            $trx = $trx->where(function ($query) use ($request) {
                $query->where('description', 'like', "%$request->search%");
                $query->orWhere('transaction_date', 'like', "%$request->search%");
                $query->orWhere('debit_credit_status', 'like', "%$request->search%");
                $query->orWhere('amount', 'like', "%$request->search%");
                $query->orWhereHas('Nasabah', function ($query) use ($request) {
                    $query->where('id', 'like', "%$request->search%");
                    $query->orWhere('name', 'like', "%$request->search%");
                });
            });
        }

        return response([
            'data' => $trx->paginate()
        ]);
    }

    public function makeTransaction(Request $request)
    {
        $id = $request->id;

        DB::beginTransaction();
        try {
            if ($request->id != null) {
                $trx = Transaction::find($id);
            } else {
                $trx = new Transaction();
            }

            $trx->nasabah_id = $request->nasabah_id;
            $trx->description = $request->description;
            $trx->transaction_date = $request->transaction_date;
            $trx->debit_credit_status = $request->debit_credit_status;
            $trx->amount = $request->amount;
            $trx->save();

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Transaction berhasil dibuat'
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

    public function deleteTransaction($id)
    {
        $trx = Transaction::find($id);

        if ($trx) {
            $trx->delete();

            return response([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus'
            ]);
        }

        return response([
            'success' => false,
            'message' => 'Transaksi tidak ditemukan'
        ]);
    }
}

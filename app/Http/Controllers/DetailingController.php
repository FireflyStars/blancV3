<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\returnSelf;

class DetailingController extends Controller
{
    public function initDetailing(Request $request)
    {
        $search = $request->post('search');
        $order_id = $request->post('order_id');
        $item_id = $request->post('item_id');
        $detailingitem_id = $request->post('detailingitem_id');
        $user = Auth::user();

        $detailingitem = DB::table('detailingitem')
            ->where('order_id', '=', $order_id)
            ->where('item_id', '=', $item_id)
            ->get();
        if (count($detailingitem) == 0) {
            $detailingitem = (array) $detailingitem->first();
            $order = DB::table('infoOrder')->where('id', '=', $order_id)->get();
            if (count($order) > 0) {
                $order = (array) $order->first();
                $detailingitem_id = DB::table('detailingitem')->insertGetId(
                    [
                        'order_id' => $order_id,
                        'item_id' => $item_id,
                        'customer_id' => $order['CustomerID'],
                        'status' => 'In Process',
                        'etape' => 1,
                        'created_at'=>date('Y-m-d H:i:s'),
                        'updated_at'=>date('Y-m-d H:i:s')
                    ]
                );
            } else {
                return response()->json(['message' => 'Order not found.']);
            }
        }


        $departements = DB::table('departments')
            ->where('deleted_at', NULL)
            ->get();
        $searched_dept = null;
        if ($search) {
            $searched_dept = DB::table('typeitem')->select('typeitem.name as item_name', 'departments.id as dept_id', 'departments.name as dept_name')
                ->join('departments', 'typeitem.department_id', '=', 'departments.id')
                ->where('typeitem.name', 'LIKE', '%' . $search . '%')
                ->first();
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['department_id' => $searched_dept->dept_id,'updated_at'=>date('Y-m-d H:i:s')]);
        }
        $detailingitem = DB::table('detailingitem')
            ->where('order_id', '=', $order_id)
            ->where('item_id', '=', $item_id)
            ->get();
        $detailingitem = (array) $detailingitem->first();

        echo json_encode(
            [
                'user' => $user,
                'departements' => $departements,
                'detailingitem' => $detailingitem,
                'search' => $search,
                'searched_dept' => $searched_dept,
            ]
        );
    }
    public function updateItemDetailing(Request $request)
    {
        $detailingitem_id = $request->post('detailingitem_id');
        $dept_id = $request->post('dept_id');
        $status = $request->post('status');
        if (isset($dept_id)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['department_id' => $dept_id,'updated_at'=>date('Y-m-d H:i:s')]);
        }
        if (isset($status)) {
            DB::table('detailingitem')->where('id', $detailingitem_id)->update(['status' => $status,'updated_at'=>date('Y-m-d H:i:s')]);
        }
        $detailingitem = DB::table('detailingitem')
            ->where('id', '=', $detailingitem_id)
            ->get();
        $detailingitem=(array) $detailingitem->first();

        echo json_encode(
            [
                'detailingitem' => $detailingitem
            ]
        );
    }
}

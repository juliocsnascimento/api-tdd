<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\SellersRequest;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellersController extends Controller
{
    public function __construct(private Seller $seller)
    {

    }
    public function index() {
        return response()->json($this->seller->all());
    }

    public function show($id)
    {
        $seller = $this->seller->find($id);
        return response()->json($seller);
    }

    public function store(SellersRequest $request)
    {
        $seller = $this->seller->create($request->all());

        return response()->json($seller, 201);
    }

    public function update($id, Request $request)
    {
        $seller = $this->seller->find($id);
        $seller->update($request->all());

        return response()->json($seller, 200);
    }
    public function destroy($id)
    {
        $seller = $this->seller->find($id);
        $seller->delete();

        return response()->json([], 204);
    }
}

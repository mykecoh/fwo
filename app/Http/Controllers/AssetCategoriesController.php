<?php

namespace App\Http\Controllers;

use App\AssetCategories;
use Illuminate\Http\Request;

class AssetCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Kategori Aset";
        $assetCategories = AssetCategories::all();

        return view('assetCategories.index',compact('assetCategories','title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AssetCategories  $assetCategories
     * @return \Illuminate\Http\Response
     */
    public function show(AssetCategories $assetCategories)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AssetCategories  $assetCategories
     * @return \Illuminate\Http\Response
     */
    public function edit(AssetCategories $assetCategories)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AssetCategories  $assetCategories
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AssetCategories $assetCategories)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AssetCategories  $assetCategories
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssetCategories $assetCategories)
    {
        //
    }
}

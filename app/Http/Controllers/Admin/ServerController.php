<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Server;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = Server::orderby('title','asc')->get();
        $data = [
            'category_name' => 'servers',
            'page_name' => 'list',
            'servers' => $servers
        ];
        return view('admin.servers.list')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'category_name' => 'servers',
            'page_name' => 'create'
        ];
        return view('admin.servers.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            Server::updateOrCreate([
                'title' => $request->title,
                'embed' => $request->embed,
                'type' => $request->type,
                'status' => $request->status
            ]);
            return redirect()->route('admin.servers.index')->with('success', 'Servidor añadido correctamente');
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $server = Server::find($id);
        $data = [
            'category_name' => 'servers',
            'page_name' => 'create',
            'server' => $server
        ];
        return view('admin.servers.edit')->with($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            Server::updateOrCreate(['id' => $id], [
                'title' => $request->title,
                'embed' => $request->embed,
                'type' => $request->type,
                'status' => $request->status
            ]);
            return redirect()->route('admin.servers.index')->with('success', 'Servidor editado correctamente');
        } catch (Exception $e) {
            return redirect()->back()->withInput($request->all())->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Server::findOrFail($id)->destroy($id);
            return redirect()->route('admin.servers.index')->with('success', 'Servidor eliminado correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.servers.index')->with('error', $e->getMessage());
        }
    }
}

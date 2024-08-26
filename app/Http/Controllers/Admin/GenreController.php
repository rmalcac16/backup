<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Models\Genre;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $genres = Genre::orderby('title','asc')->get();
        $data = [
            'category_name' => 'genres',
            'page_name' => 'list',
            'genres' => $genres
        ];
        return view('admin.genres.list')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'category_name' => 'genres',
            'page_name' => 'create'
        ];
        return view('admin.genres.create')->with($data);
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
            Genre::updateOrCreate([
                'title' => $request->title,
                'slug' => Str::slug($request->title)
            ]);
            return redirect()->route('admin.genres.index')->with('success', 'Genero añadido correctamente');
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
        $genre = Genre::find($id);
        $data = [
            'category_name' => 'genres',
            'page_name' => 'edit',
            'genre' => $genre
        ];
        return view('admin.genres.edit')->with($data);
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
            Genre::updateOrCreate(['id' => $id], [
                'title' => $request->title,
                'slug' => Str::slug($request->title)
            ]);
            return redirect()->route('admin.genres.index')->with('success', 'Genero editado correctamente');
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
            Genre::findOrFail($id)->destroy($id);
            return redirect()->route('admin.genres.index')->with('success', 'Genero eliminado correctamente');
        } catch (Exception $e) {
            return redirect()->route('admin.genres.index')->with('error', $e->getMessage());
        }
    }
}

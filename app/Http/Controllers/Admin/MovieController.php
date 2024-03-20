<?php

namespace App\Http\Controllers\Admin;

use App\Models\Movie;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request; //menangkap value dari form html
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class MovieController extends Controller
{

    public function index() {
        $movies = Movie::all();

        return view('admin.movies', ['movies' => $movies]);
    }

    public function create() {
        return view('admin.movie-create');
    }
    
    public function store(Request $request) {
        $data = $request->except('_token');

        //memvalidasi data
        $request->validate([ 
            'title' => 'required|string',
            'small_thumbnail' => 'required|image|mimes:jpeg,jpg,png',
            'large_thumbnail' => 'required|image|mimes:jpeg,jpg,png',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required',
        ]);

        $smallThumbnail = $request->small_thumbnail; //ambil semua atribute data ke variable
        $largeThumbnail = $request->large_thumbnail;

        $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName(); //ambil original name
        $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName();

        //1. simpan nama file
        //2. upload file
        $smallThumbnail->storeAs('public/thumbnail', $originalSmallThumbnailName);
        $largeThumbnail->storeAs('public/thumbnail', $originalLargeThumbnailName);

        $data['small_thumbnail'] = $originalSmallThumbnailName; 
        $data['large_thumbnail'] = $originalLargeThumbnailName;
        
        Movie::create($data); //menyimpan semua data ke model Movie

        return redirect()->route('admin.movie')->with('success', 'Movie created');

    }

    public function edit($id){
        $movie = Movie::find($id);

        return view('admin.movie-edit', ['movie' => $movie]); //parsing data
    }

    public function update(Request $request, $id) {
        $data = $request->except('_token');

        //memvalidasi data
        $request->validate([ 
            'title' => 'required|string',
            'small_thumbnail' => 'image|mimes:jpeg,jpg,png',
            'large_thumbnail' => 'image|mimes:jpeg,jpg,png',
            'trailer' => 'required|url',
            'movie' => 'required|url',
            'casts' => 'required|string',
            'categories' => 'required|string',
            'release_date' => 'required|string',
            'about' => 'required|string',
            'short_about' => 'required|string',
            'duration' => 'required|string',
            'featured' => 'required',
        ]);

        $movie = Movie::find($id);

        if($request->small_thumbnail) {
            //save new image
            $smallThumbnail = $request->small_thumbnail;
            $originalSmallThumbnailName = Str::random(10).$smallThumbnail->getClientOriginalName(); //ambil original name
            $smallThumbnail->storeAs('public/thumbnail', $originalSmallThumbnailName); //simpan new image
            $data['small_thumbnail'] = $originalSmallThumbnailName; //overwrite new image
            
            //delete old image
            Storage::delete('public/thumbnail/'.$movie->small_thumbnail);
        }

        if($request->large_thumbnail) {
            //save new image
            $largeThumbnail = $request->large_thumbnail;
            $originalLargeThumbnailName = Str::random(10).$largeThumbnail->getClientOriginalName(); //ambil original name
            $largeThumbnail->storeAs('public/thumbnail', $originalLargeThumbnailName); //simpan new image
            $data['large_thumbnail'] = $originalLargeThumbnailName; //overwrite new image
            
            //delete old image
            Storage::delete('public/thumbnail/'.$movie->large_thumbnail);
        }

        $movie->update($data); //parsing data di method update, name atribut & database sama

        return redirect()->route('admin.movie')->with('success', 'Movie updated');
    }

    public function destroy($id){
        Movie::find($id)->delete(); //mendapatkan data movie berdasarkan id

        return redirect()->route('admin.movie')->with('success', 'Movie deleted');
    }
}

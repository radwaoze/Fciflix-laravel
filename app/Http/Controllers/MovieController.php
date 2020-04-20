<?php

namespace App\Http\Controllers;
use App\Observers\MoiveObserver;
use App\Repositories\Movie\MovieInterface;
use Illuminate\Http\Request;
use App\movie;
use DB;
class MovieController extends Controller
{
    public function  __construct(MovieInterface $Movie)
    {
     $this->Movie = $Movie;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     return view('fciflix.movie');
    }
    public function dashboard(){

        return view('admin.dashboard');

    }

     public function add_movies(){
        return view('admin.add-movie');
    }
    public function edit_movie($id){
      $data=Movie::findOrFail($id);
        return view('admin.edit_Movie',compact('data'));
    }
   public function list_movie(){
        // $data=DB::table('movie')->get();
        $data =$this->Movie->getAll();
        return view('admin.list_movie',compact('data'))
            ->with('i',(request()->input('page',1)-1)*5);
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
    public static function listmov($id){
        $movies = DB::table('movie')->where('cat_id', '=',$id)->get();
        return $movies;
    }
    public static function viewMovie( $id){
      $movies = DB::table('movie')->where('id', '=',$id)->get();
        return view('fciflix.movie',['movies' => $movies]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeM(Request $request)
    {
        $request->validate([
            'mov_name'    => 'required',
            'img_path'    => 'required|image|max:2048',
            'cat_id'      => 'required',
            'source_path' => 'required'

        ]);
        $img_path = $request->file('img_path');
        $new_name = rand().'.'.$img_path->
            getClientOriginalExtension();
        $img_path->move(public_path('images'),$new_name);
        $form_data=array(
            'mov_name' => $request->mov_name,
            'img_path' => $new_name,
            'cat_id' => $request->cat_id,
            'source_path'=>$request->source_path
        );
       // Movie::create($form_data);
        $this->Movie->create($form_data);
        //return redirect('add-movie')->with('success','Data Added successfully.');
    }
    public function search(Request $request)
    {   
        $request->validate([
            'query'=>'required|min:3',
        ]);
        $query= $request->input('query');
        $movies= movie::where('mov_name', 'like', "%$query%" )->get();
        return view('fciflix.search-results')->with('movies', $movies);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateM(Request $request, $id)
    {
        $image_name = $request->hid_img_path;
        $img_path   = $request->file('img_path');
        if($img_path !=''){
              $request->validate([
            'mov_name'    => 'required',
            'img_path'    => 'required|image|max:2048',
            'cat_id'      => 'required',
            'source_path' => 'required'

        ]);
                $image_name = rand().'.'.$img_path->
            getClientOriginalExtension();
        $img_path->move(public_path('images'),$image_name);
            }else{
          $request->validate([
            'mov_name'    => 'required',
            'cat_id'      => 'required',
            'source_path' => 'required'

        ]);
        }
        $form_data=array(
            'mov_name' => $request->mov_name,
            'img_path' => $image_name,
            'cat_id' => $request->cat_id,
            'source_path'=>$request->source_path
        );
       // Movie::whereId($id)->update($form_data);
        $this->Movie->update($id,$form_data);
        return redirect('list_movie')->with('success','data is successfully updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Movie::findOrFail($id);
        $data->delete();
        return redirect('list_movie')->with('success','data is successfully Deleted');
    }
}

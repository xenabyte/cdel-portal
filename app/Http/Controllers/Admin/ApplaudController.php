<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Board;
use App\Models\BoardUser;
use App\Models\BoardMessage;

use Log;
use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Carbon\Carbon;


class ApplaudController extends Controller
{
    //

    public function applaudBoards(){
        $applaudBoards = Board::get();

        return view('admin.applaudBoards', [
            'applaudBoards' => $applaudBoards,
        ]);
    }


    public function applaudBoard($slug){
        $applaudBoard = Board::with('board_messages', 'board_messages.board_user')->where('slug', $slug)->first();

        return view('admin.applaudBoard', [
            'applaudBoard' => $applaudBoard,
        ]);
    }

    public function createBoard(Request $request){

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
            'email' => 'required',
        ]);


        $title = $request->title.' '.$request->email;
        $date = Carbon::now();

        $slug = md5(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title.'-'.$date))));

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newBoard = ([
            'slug' => $slug,
            'title' => $request->title,
            'description' => $request->description,
            'image'=> $request->email,
        ]);

        if($board = Board::create($newBoard)){

            if($request->hasFile('image')){
                $dir = 'uploads/board';
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
    
                $imageUrl = 'uploads/board/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move('uploads/board', $imageUrl);
                $board->image = $imageUrl;
                $board->save();
            }
           

            alert()->success('Success', 'Applaud board created successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Error while creating applaud board, Report to Administrator')->persistent('Close');
        return redirect()->back();
    }

    public function deleteBoard(Request $request){

        $validator = Validator::make($request->all(), [
            'board_id' => 'required|exists:boards,id',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $board = Board::findOrFail($request->board_id);
        $boardImage = $board->image;

        if (file_exists($boardImage)) {
            unlink($boardImage);
        }

        if($board->delete()){
            alert()->success('Record deleted successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
    
    public function updateBoard(Request $request){
        $validator = Validator::make($request->all(), [
            'board_id' => 'required|exists:boards,id',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $board = Board::findOrFail($request->board_id);

        if(!empty($request->title) && $request->title != $board->title){
            $board->title = $request->title;
        }

        if(!empty($request->description) && $request->description != $board->description){
            $board->description = $request->description;
        }

        if(!empty($request->email) && $request->email != $board->email){
            $board->email = $request->email;
        }

        if(!empty($request->image)){
            $boardImage = $board->image;
            if (file_exists($boardImage)) {
                unlink($boardImage);
            }

            $slug = md5($board->slug . time());
            $imageUrl = 'uploads/board/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/board', $imageUrl);

            $board->image = $imageUrl;
        }

        if($board->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();

    }
}

<?php

namespace App\Http\Controllers;

use App\Lib\Notification;
use App\Listeners\LogUserActivity;
use App\Listeners\UsersLoginSession;
use App\Models\Notification as ModelsNotification;
use App\Models\Role;
use App\Models\SkpdBendahara;
use App\Models\User;
use App\Repositories\Impl\NoticationImplement;
use App\Repositories\Impl\SKPDImplement;
use App\Repositories\Impl\StsHeaderImplement;
use App\Repositories\Impl\TbpHeaderImplement;
use App\Repositories\Impl\UserImplement;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Traits\HasRoles;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use AuthenticatesUsers;

    protected $skpdRepo;
    protected $userRepo;
    protected $tbpRepo;
    protected $stsRepo;
    protected $notifRepo;

    public function __construct(SKPDImplement $skpdRepo, UserImplement $userRepo, TbpHeaderImplement $tbpRepo,
    StsHeaderImplement $stsRepo, NoticationImplement $notifRepo) {
        $this->middleware('permission:show-settings|show-control-data|show-transaction|show-input-data|show-master|show-users', ['only' => 'index']);
        $this->skpdRepo = $skpdRepo;
        $this->userRepo = $userRepo;
        $this->tbpRepo  = $tbpRepo;
        $this->stsRepo  = $stsRepo;
        $this->notifRepo = $notifRepo;
    }

    public function index(Request $request)
    {
        $roles = Role::all();
        return view('user.index',compact('roles'));
    }

    public function json(Request $request)
    {
        $users = User::all();
        $roleAdmin = auth()->user()->hasRole('production-manager');
        $rolePenyelia = auth()->user()->hasRole('operator');
        return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('action', function($row) use($roleAdmin,$rolePenyelia){
                        if($roleAdmin || $rolePenyelia) {
                        $btn = '<a href='.route('settings.users.directlogin',['id'=>$row->id]).' data-toggle="tooltip"  data-original-title="Login" class="btn btn-warning btn-sm directLogin">Login</a>
                                <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editUser">Edit</a>';
                        }else{
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Edit" class="edit btn btn-primary btn-sm editUser">Edit</a>';
                        }
                        $btn = $btn.' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="'.$row->id.'" data-original-title="Delete" class="btn btn-danger btn-sm deleteUser">Delete</a>';

                        return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }

    public function create_skpd(Request $request)
    {
        $skpd = $this->skpdRepo->find($request->query('id'));
        $roles = Role::pluck('name','name')->all();
        $users = $this->userRepo->whereNotIn('id',1,'all');
        return view('user.create',compact('roles','skpd','users'));
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();


            if(!isset($input['userId'])) {
                $this->validate($request, [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'username'=>'required|unique:users,username',
                    'roles' => 'required',
                    'nip'=>'required',
                ]);

            }



            $input['password'] = Hash::make("Sa123456");
            if(isset($input['image'])) {
                $input['ttd'] = $request->file('image')->getClientOriginalName();

                $request->file('image')->storeAs('public/images', $input['ttd']);
            }
            $user = User::updateOrCreate(['id'=>$input['userId']],$input);
            $user->assignRole($request->input('roles'));
            Session::flash('status','User created successfully');
            return response()->json(['success'=>'Record saved successfully.']);
        } catch (\Throwable $th) {
            Notification::sendException($th);
            return response()->json(['errors'=>'Cannot saved successfully.']);
        }

    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->all();
        $data = [
            'user' => $user,
            'roles'=> $roles,
            'userRole' => $userRole
        ];
        // return view('users.edit',compact('user','roles','userRole'));
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));
        Session::flash('status','User updated successfully');
        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }

    public function destroy(Request $request)
    {
        User::find($request->id)->delete();
        Session::flash('status','User deleted successfully');
        return response()->json(['success'=>'Record deleted successfully.']);
    }

    public function showLockScreenForm() {
        $user = Auth::user();
        return view('auth-lock-screen',compact('user'));
    }

    function viewProfile(Request $request) {
        $user = auth()->user();
        $tbpCount = $this->tbpRepo->getCountTbp();
        $tbpBatalCount = $this->tbpRepo->getCountTbpBatal();
        $stsCount = $this->stsRepo->getCountSts();
        $stsBatalCount = $this->stsRepo->getCountStsBatal();
        $notifications = $this->notifRepo->getAllNotif();
        $statusUser = statusUser($user);
        return view('contacts-profile',compact('user', 'tbpCount',
            'tbpBatalCount','stsCount', 'stsBatalCount','notifications','statusUser'));
    }

    function updatePassword(Request $request) {
        $dataAll = $request->all();
        $user = User::find(auth()->user()->id);
        $user->password = Hash::make($dataAll['password']);
        $user->save();
        $data = [
            'type' => 4,
            'data' => "User ".$user->name." melakukan perubahan password",
        ];
        ModelsNotification::create($data);
        $tbpCount = $this->tbpRepo->getCountTbp();
        $tbpBatalCount = $this->tbpRepo->getCountTbpBatal();
        $stsCount = $this->stsRepo->getCountSts();
        $stsBatalCount = $this->stsRepo->getCountStsBatal();
        $notifications = $this->notifRepo->getAllNotif();
        $statusUser = statusUser($user);
        Session::flash('status','Berhasil melakukan perubahan password user');

        return view('contacts-profile',compact('user', 'tbpCount',
            'tbpBatalCount','stsCount', 'stsBatalCount','notifications','statusUser'));
    }

    public function directLogin(Request $request)
    {
        try {
            $this->redirectTo = "index";
            $url = $request->server()['REQUEST_URI'];
            $id = explode("&", explode("?", $url)[1])[0];
            $id = explode("=", $id)[1];
            $user = Auth::guard('web')->loginUsingId($id);
            $skpd_id = SkpdBendahara::where('id_bendahara', $id)->where('actived', 1)->firstOrFail();

            Cookie::queue(Cookie::make('isAdmin', 'true', 60));
            // Set the session data for the modal
            Session::flash('direct_login', '1');
            Session::put("skpd_id", $skpd_id->id_skpd);
            Session::put("bendahara_id", $skpd_id->id_bendahara);
            LogUserActivity::get()->store($user, 'Login Attempt', User::class, "Login Attempt");
            UsersLoginSession::get()->store($user,$skpd_id);
            return $this->sendLoginResponse($request);
        } catch (\Throwable $th) {
            Notification::sendException($th);
        }
        return back()->withErrors($th->getMessage());
    }
}

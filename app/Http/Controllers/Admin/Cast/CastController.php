<?php

namespace App\Http\Controllers\Admin\Cast;

use App\BankAccount;
use App\Cast;
use App\CastClass;
use App\Enums\MessageType;
use App\Enums\RoomType;
use App\Enums\SystemMessageType;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Notifications\CreateCast;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class CastController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->search;

        $casts = Cast::query();

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $casts->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $casts->where(function ($query) use ($fromDate, $toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($request->has('search')) {
            $casts->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        $casts = $casts->orderBy('created_at', 'DESC')->paginate($request->limit ?: 10);

        return view('admin.casts.index', compact('casts'));
    }

    public function registerCast(User $user)
    {
        $castClass = CastClass::all();

        return view('admin.casts.register', compact('user', 'castClass'));
    }

    public function validRegister($request)
    {
        $rules = $this->validate($request,
            [
                'last_name' => 'required',
                'first_name' => 'required',
                'last_name_kana' => 'required',
                'first_name_kana' => 'required',
                'nick_name' => 'required',
                'phone' => 'required|regex:/^[0-9]+$/',
                'line' => 'required',
                'number' => 'nullable|numeric|digits:7',
                'front_side' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'back_side' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
            ]
        );

        $year = $request->start_year;
        $month = $request->start_month;
        $date = $request->start_date;
        if (!checkdate($month, $date, $year)) {
            return false;
        }
        $age = Carbon::createFromDate($year, $month, $date)->age;

        $data = [
            'lastname' => $request->last_name,
            'firstname' => $request->first_name,
            'lastname_kana' => $request->last_name_kana,
            'firstname_kana' => $request->first_name_kana,
            'nickname' => $request->nick_name,
            'phone' => $request->phone,
            'line_id' => $request->line,
            'note' => $request->note,
            'gender' => $request->gender,
            'class_id' => $request->cast_class,
            'year' => $year,
            'month' => $month,
            'date' => $date,
            'age' => $age,
        ];

        if ($request->bank_name && $request->number && $request->branch_name) {
            $data['branch_name'] = $request->branch_name;
            $data['bank_name'] = $request->bank_name;
            $data['number'] = $request->number;
        }

        $frontImage = request()->file('front_side');
        $backImage = request()->file('back_side');

        $frontImageName = Uuid::generate()->string . '.' . strtolower($frontImage->getClientOriginalExtension());
        $backImageName = Uuid::generate()->string . '.' . strtolower($backImage->getClientOriginalExtension());

        $frontFileUploaded = Storage::put($frontImageName, file_get_contents($frontImage), 'public');
        $backFileUploaded = Storage::put($backImageName, file_get_contents($backImage), 'public');

        if ($frontFileUploaded && $backFileUploaded) {
            $data['front_id_image'] = $frontImageName;
            $data['back_id_image'] = $backImageName;
        }

        return $data;
    }

    public function confirmRegister(Request $request, User $user)
    {
        $data = $this->validRegister($request);

        if (!$data) {
            $request->session()->flash('msgdate', trans('messages.date_not_valid'));

            return redirect()->route('admin.casts.register', compact('user'));
        }

        return view('admin.casts.confirm', compact('data', 'user'));
    }

    public function saveCast(Request $request, User $user)
    {
        $castClass = CastClass::where('id', $request->class_id)->first();

        $year = $request->year;
        $month = $request->month;
        $date = $request->date;

        $data = [
            'lastname' => $request->lastname,
            'firstname' => $request->firstname,
            'lastname_kana' => $request->lastname_kana,
            'firstname_kana' => $request->firstname_kana,
            'nickname' => $request->nickname,
            'phone' => $request->phone,
            'line_id' => $request->line_id,
            'note' => $request->note,
            'gender' => $request->gender,
            'class_id' => $request->class_id,
            'front_id_image' => $request->front_id_image,
            'back_id_image' => $request->back_id_image,
            'cost' => $castClass->cost,
            'date_of_birth' => $year . '-' . $month . '-' . $date,
            'type' => UserType::CAST,
        ];

        $user->update($data);

        if (isset($request->bank_name)) {
            BankAccount::create([
                'user_id' => $user->id,
                'bank_name' => $request->bank_name,
                'branch_name' => $request->branch_name,
                'number' => $request->number,
            ]);
        }

        $message = 'キャスト登録おめでとうございます♪'
            . PHP_EOL . 'あなたは立派なCheers familyです☆'
            . PHP_EOL . PHP_EOL . '解散後のメッセージで心をつかんでリピートも狙ってみましょう！'
            . PHP_EOL . PHP_EOL . 'まずはゲストにメッセージを送ってアピールしてみてください！'
            . PHP_EOL . PHP_EOL . '不安なこと、分からないことがあればいつでもCheers運営側にお問い合わせくださいね♪';

        $room = $user->rooms()
            ->where('rooms.type', RoomType::SYSTEM)
            ->where('rooms.is_active', true)->first();

        $roomMessage = $room->messages()->create([
            'user_id' => 1,
            'type' => MessageType::SYSTEM,
            'message' => $message,
            'system_type' => SystemMessageType::NOTIFY,
        ]);

        $roomMessage->recipients()->attach($user->id, ['room_id' => $room->id]);
        $user->notify(new CreateCast());

        return redirect()->route('admin.casts.index');
    }
}

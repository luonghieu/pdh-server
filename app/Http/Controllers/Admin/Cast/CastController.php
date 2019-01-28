<?php

namespace App\Http\Controllers\Admin\Cast;

use App\BankAccount;
use App\Cast;
use App\CastClass;
use App\Enums\BankAccountType;
use App\Enums\PointCorrectionType;
use App\Enums\PointType;
use App\Enums\ProviderType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Room;
use App\Http\Controllers\Controller;
use App\Notifications\CreateCast;
use App\Prefecture;
use App\Services\CSVExport;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class CastController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->search;
        $orderBy = $request->only('last_active_at', 'rank');
        $casts = Cast::query();

        if ($request->has('from_date') && !empty($request->from_date)) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $casts->where(function ($query) use ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $casts->where(function ($query) use ($toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($request->has('search') && !empty($request->search)) {
            $casts->where(function ($query) use ($keyword) {
                $query->where('id', "$keyword")
                    ->orWhere('nickname', 'like', "%$keyword%");
            });
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $key => $value) {
                $casts->orderBy($key, $value);
            }
        } else {
            $casts->orderBy('last_active_at', 'DESC');
        }

        $casts = $casts->paginate($request->limit ?: 10);

        return view('admin.casts.index', compact('casts'));
    }

    public function registerCast(User $user)
    {
        $castClass = CastClass::all();
        $prefectures = Prefecture::supported()->get();

        return view('admin.casts.register', compact('user', 'castClass', 'prefectures'));
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
            'prefecture_id' => $request->prefecture,
            'rank' => $request->cast_rank,
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
            'prefecture_id' => $request->prefecture,
            'rank' => $request->cast_rank,
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

        $user->notify(new CreateCast());

        return redirect()->route('admin.casts.index');
    }

    public function sumPointReceive($points)
    {
        $sumPointReceive = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_receive) {
                $sum += $product->point;
            }

            return $sum;
        });

        return $sumPointReceive;
    }

    public function sumConsumedPoint($points)
    {
        $sumConsumedPoint = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_transfer) {
                $sum += $product->point;
            }

            if ($product->is_adjusted && $product->point < 0) {
                $sum += -$product->point;
            }

            return $sum;
        });

        return $sumConsumedPoint;
    }

    public function getOperationHistory(Cast $user, Request $request)
    {
        $keyword = $request->search_point_type;
        $pointTypes = [
            0 => '全て', // all
            PointType::ADJUSTED => '調整',
            PointType::RECEIVE => 'ポイント受取',
            PointType::TRANSFER => '振込',
        ];

        $pointCorrectionTypes = [
            PointCorrectionType::ACQUISITION => '取得ポイント',
            PointCorrectionType::CONSUMPTION => '消費ポイント',
        ];

        $with['order'] = function ($query) {
            return $query->withTrashed();
        };
        $points = $user->points()->with($with)
            ->whereIn('type', [PointType::RECEIVE, PointType::TRANSFER, PointType::ADJUSTED])
            ->where('status', Status::ACTIVE);

        $fromDate = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : null;

        if ($fromDate) {
            $points->where(function ($query) use ($fromDate) {
                $query->where('created_at', '>=', $fromDate);
            });
        }

        if ($toDate) {
            $points->where(function ($query) use ($toDate) {
                $query->where('created_at', '<=', $toDate);
            });
        }

        if ($keyword) {
            if ('0' != $keyword) {
                $points->where(function ($query) use ($keyword) {
                    $query->where('type', $keyword);
                });
            }
        }

        $points = $points->orderBy('created_at', 'DESC');
        $pointsExport = $points->get();
        $points = $points->paginate($request->limit ?: 10);

        $sumPointReceive = $this->sumPointReceive($pointsExport);
        $sumConsumedPoint = -$this->sumConsumedPoint($pointsExport);
        $sumBalance = $sumPointReceive - $sumConsumedPoint;

        $sumDebitAmount = $points->sum(function ($product) {
            $sum = 0;
            if ($product->is_transfer && $product->point < 0) {
                $sum += $product->point;
            }

            return $sum;
        });

        if ('export' == $request->submit) {
            $data = collect($pointsExport)->map(function ($item) {
                return [
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                    PointType::getDescription($item->type),
                    ($item->is_receive) ? $item->order->id : '--',
                    ($item->is_receive || ($item->is_adjusted && $item->point > 0)) ? number_format($item->point) : '',
                    (($item->is_transfer) || ($item->is_adjusted && $item->point < 0)) ? number_format($item->point) : '',
                    number_format($item->balance),
                    ($item->is_transfer) ? '￥' . number_format(abs($item->point)) : '',
                ];
            })->toArray();

            $sum = [
                '合計',
                '-',
                '-',
                $sumPointReceive,
                $sumConsumedPoint,
                $sumBalance,
                '¥' . number_format(abs($sumDebitAmount)),
            ];

            array_push($data, $sum);

            $header = [
                '日付',
                '取引種別',
                '予約ID',
                '取得ポイント',
                '消費ポイント',
                '残高',
                '引き落とし額',
            ];

            try {
                $file = CSVExport::toCSV($data, $header);
            } catch (\Exception $e) {
                LogService::writeErrorLog($e);
                $request->session()->flash('msg', trans('messages.server_error'));

                return redirect()->route('admin.casts.operation_history', compact('user'));
            }
            $file->output('operation_history_point_of_cast_' . $user->fullname . '_' . Carbon::now()->format('Ymd_Hi') . '.csv');

            return;
        }

        return view('admin.casts.operation_history', compact('user', 'points', 'pointTypes',
            'sumPointReceive', 'sumConsumedPoint', 'sumBalance', 'sumDebitAmount', 'pointCorrectionTypes')
        );
    }

    public function changePoint(Request $request, Cast $user)
    {
        $rules = [
            'point' => 'regex:/^[0-9]+$/',
        ];

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        switch ($request->correction_type) {
            case PointCorrectionType::ACQUISITION:
                $point = $request->point;
                break;
            case PointCorrectionType::CONSUMPTION:
                $point = -$request->point;
                break;

            default:break;
        }

        $newPoint = $user->point + $point;

        $input = [
            'point' => $point,
            'balance' => $newPoint,
            'type' => PointType::ADJUSTED,
            'status' => Status::ACTIVE,
        ];

        try {
            DB::beginTransaction();

            $user->points()->create($input);

            $user->point = $newPoint;
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);
            $request->session()->flash('msg', trans('messages.server_error'));
        }

        return response()->json(['success' => true]);
    }

    public function changeStatusWork(Cast $user)
    {
        try {
            $user->working_today = !$user->working_today;
            $user->save();

            return back();
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }

    public function create()
    {
        $castClass = CastClass::all();
        $prefectures = Prefecture::supported()->get();

        return view('admin.casts.create', compact('castClass', 'prefectures'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $maxYear = Carbon::parse(now())->subYear(52)->format('Y');
            $minYear = Carbon::parse(now())->subYear(20)->format('Y');

            $rules = [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'gender' => 'required',
                'lastname' => 'required|string',
                'firstname' => 'required|string',
                'lastname_kana' => 'required|string',
                'firstname_kana' => 'required|string',
                'nickname' => 'required|string|max:20',
                'phone' => 'required|regex:/^[0-9]+$/|digits_between:10,11|unique:users',
                'line_id' => 'required|string',
                'front_side' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'back_side' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'date_of_birth' => 'required|date|after:'. $maxYear.'|before:'.$minYear,
                'account_number' => 'nullable|numeric|digits:7|required_with:bank_name,branch_name',
                'bank_name' => 'required_with:branch_name,account_number',
                'branch_name' => 'required_with:bank_name,account_number',
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withErrors($validator->errors())->withInput();
            }

            $castClass = CastClass::where('id', $request->class_id)->first();

            $year = $request->year;
            $month = $request->month;
            $date = $request->date;

            $input = [
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'fullname' => $request->lastname . $request->firstname,
                'lastname_kana' => $request->lastname_kana,
                'firstname_kana' => $request->firstname_kana,
                'fullname_kana' => $request->lastname_kana . $request->firstname_kana,
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
                'prefecture_id' => $request->prefecture,
                'rank' => $request->cast_rank,
                'provider' => ProviderType::EMAIL,
            ];

            $frontImage = request()->file('front_side');
            $backImage = request()->file('back_side');

            $frontImageName = Uuid::generate()->string . '.' . strtolower($frontImage->getClientOriginalExtension());
            $backImageName = Uuid::generate()->string . '.' . strtolower($backImage->getClientOriginalExtension());

            $frontFileUploaded = Storage::put($frontImageName, file_get_contents($frontImage), 'public');
            $backFileUploaded = Storage::put($backImageName, file_get_contents($backImage), 'public');

            if ($frontFileUploaded && $backFileUploaded) {
                $input['front_id_image'] = $frontImageName;
                $input['back_id_image'] = $backImageName;
            }

            $user = new Cast;
            $user = $user->create($input);

            if ($request->bank_name && $request->branch_name && $request->account_number) {
                BankAccount::create([
                    'user_id' => $user->id,
                    'bank_name' => $request->bank_name,
                    'branch_name' => $request->branch_name,
                    'number' => $request->account_number,
                ]);
            }

            $room = Room::create([
                'owner_id' => $user->id
            ]);

            $room->users()->attach([1, $user->id]);
            $user->notify(new CreateCast());

            DB::commit();

            return redirect()->route('admin.casts.index');
        } catch (\Exception $e) {
            DB::rollBack();
            LogService::writeErrorLog($e);

            return back();
        }
    }

    public function exportBankAccounts(Request $request)
    {
        $bankAccounts = BankAccount::all();

        $data = collect($bankAccounts)->map(function ($item) {
                return [
                    $item->id,
                    $item->user_id,
                    $item->bank_name,
                    $item->number,
                    $item->holder_name,
                    $item->holder_type,
                    BankAccountType::getDescription($item->type),
                    $item->bank_code,
                    $item->branch_name,
                    $item->branch_code,
                    Carbon::parse($item->created_at)->format('Y年m月d日'),
                ];
            })->toArray();

        $header = [
            'No.',
            'User ID',
            'Bank Name',
            'Number',
            'Holder Name',
            'Holder Type',
            'Type',
            'Bank Code',
            'Branch Name',
            'Branch Code',
            'Create At',
        ];

        try {
            $file = CSVExport::toCSV($data, $header);
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
            $request->session()->flash('msg', trans('messages.server_error'));

            return redirect()->route('admin.casts.index');
        }

        $file->output('list_bank_account_' . Carbon::now()->format('Ymd_Hi') . '.csv');

        return;
    }

    public function bankAccount($user)
    {
        $user = User::withTrashed()->find($user);
        $bankAccount = BankAccount::where('user_id', $user->id)->first();

        return view('admin.casts.bank_account', compact('user', 'bankAccount'));
    }

    public function updateNote(Request $request, Cast $user)
    {
        try {
            $user->note = $request->note;
            $user->save();

            return redirect()->route('admin.users.show', compact('user'));
        } catch (\Exception $e) {
            LogService::writeErrorLog($e);
        }
    }
}

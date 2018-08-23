<?php

namespace App\Http\Controllers\Admin\Cast;

use App\BankAccount;
use App\Cast;
use App\CastClass;
use App\Enums\PointType;
use App\Enums\PointCorrectionType;
use App\Enums\Status;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Notifications\CreateCast;
use App\Prefecture;
use App\Services\CSVExport;
use App\Services\LogService;
use App\User;
use Carbon\Carbon;
use DB;
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

            if ($product->is_auto_charge && $product->point < 0) {
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

        $points = $user->points()->with('order')
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
            if ($product->is_receive) {
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
                    ($item->is_transfer) ? '￥' . number_format(-$item->point) : '',
                ];
            })->toArray();

            $sum = [
                '合計',
                '-',
                '-',
                $sumPointReceive,
                $sumConsumedPoint,
                $sumBalance,
                '¥' . number_format($sumDebitAmount),
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

    public function changePoint(Cast $user, Request $request)
    {
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

        return redirect(route('admin.casts.operation_history', ['user' => $user->id]));
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{

    public function getEstimate(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'surname' => 'required|string|max:100',
            'name' => 'required|string|max:100',
            'patronymic' => 'nullable|string|max:100',
            'sex' => 'required|integer',
            'birthday' => 'required|date|after:1900-01-01',
            'minor_children' => 'required|integer|min:0|max:30',
            'family_status' => 'required|integer|min:0|max:1',
            'monthly_income' => 'required|integer|min:0',
            'type_employment' => 'required|integer|min:0|max:4',
            'availability_real_estate' => 'required|boolean',
            'outstanding_loans' => 'required|boolean',
            'outstanding_current_loans' => $request['outstanding_loans'] === true ? 'required' : 'nullable' . '|boolean',
            'monthly_payment_current_loans' => $request['outstanding_loans'] === true ? 'required' : 'nullable' . '|integer|min:0',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()]);
        }
        $messageReject = 'К сожалению Вы не можете получить кредит в нашем банке...';
        //если возраст от 14 до 65, иначе сразу отказ...
        $years = Carbon::parse($request['birthday'])->diffInYears();
        if ( $years < 14 || $years > 65) {
            return response()->json([
                'reject' => $messageReject
            ]);
        }
        //простая проверка по существующим правилам
        $estimate = 0;
        /*if ($years < 18) { //не проверял, но должна отработать (решение см.ниже)
            $estimate += 5;
        } else {
            if ($years > 30 && (int)$request['sex'] === 1 && (int)$request['family_status'] === 0) {
                if ((int)$request['monthly_income'] < 25000 && (int)$request['minor_children'] === 0) {
                    $estimate += 2;
                } elseif ((int)$request['monthly_income'] >= 25000 && (int)$request['monthly_income'] < 30000 && (int)$request['minor_children'] > 0) {
                    $estimate += 3;
                }
            } elseif ($years > 26 && (int)$request['sex'] === 0 && (int)$request['family_status'] === 0){
                if ((int)$request['monthly_income'] < 22000 && (int)$request['minor_children'] === 0) {
                    $estimate += 2;
                } elseif ((int)$request['monthly_income'] >= 22000 && (int)$request['monthly_income'] < 28000 && (int)$request['minor_children'] > 2) {
                    $estimate += 3;
                }
            }
            if ((bool)$request['outstanding_current_loans'] === true) {
                if ($years > 65 && (int)$request['type_employment'] === 0) {
                    $estimate += 3;
                }
                if ((int)$request['monthly_income']/(int)$request['monthly_payment_current_loans'] <= 2) {
                    $estimate += 3;
                }
            }
            if ((int)$request['monthly_income'] < 15000) {
                $estimate += 2;
            }
            if ($years < 35) {
                if ((int)$request['minor_children'] === 1) {
                    $estimate += 1;
                } elseif ((int)$request['minor_children'] > 1) {
                    $estimate += 2;
                }
            }
        }*/

        //сложная проверка, как я это вижу (нужно будет так же производить проверку на фронте, чтобы правила не противоречили друг другу...):

        //проверок null видно не будет, поставил для наглядности.
        //в методе checkAndResult ниже, заменяем !is_null на isset...

        /* Если необходимо, могу сделать форму добавления правил (если это правильный путь конечно...)
        Т.к. правила не ограничены, значит ответственный сотрудник банка должен иметь возможность
        добавлять/редактировать правила из формы на фронте.
        Я бы сделал динамическое создание каждого правила, именно на фронте, каждое правило формируется отдельно.
        Получился бы массив объектов правил, который мы сохраняем в БД(в json) для данной анкеты. Здесь его получаем и
        проходимся в цикле, осуществляя проверку по каждому параметру правила.
        */

        //Правила полученные из таблицы БД...
        $rules = [ //заданные правила в ТЗ (уже с расширенными полями, чтобы можно было использовать все ответы пользователя)...
            [
                'age_from' => null, 'age_to' => 18, 'sex' => null, 'family_status' => null,
                'monthly_income_from' => null, 'monthly_income_to' => null, 'minor_children' => null, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 5
            ],
            [
                'age_from' => 30, 'age_to' => null, 'sex' => 1, 'family_status' => 0,
                'monthly_income_from' => null, 'monthly_income_to' => 25000, 'minor_children' => false, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 2
            ],
            [
                'age_from' => 30, 'age_to' => null, 'sex' => 1, 'family_status' => 0,
                'monthly_income_from' => null, 'monthly_income_to' => 30000, 'minor_children' => true, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 3
            ],
            [
                'age_from' => 26, 'age_to' => null, 'sex' => 0, 'family_status' => 0,
                'monthly_income_from' => null, 'monthly_income_to' => 22000, 'minor_children' => false, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 2
            ],
            [
                'age_from' => 26, 'age_to' => null, 'sex' => 0, 'family_status' => 0,
                'monthly_income_from' => null, 'monthly_income_to' => 28000, 'minor_children' => true, 'minor_children_equally' => null, 'minor_children_from' => 2, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 3
            ],
            [
                'age_from' => 65, 'age_to' => null, 'sex' => null, 'family_status' => null,
                'monthly_income_from' => null, 'monthly_income_to' => null, 'minor_children' => null, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => true, 'outstanding_current_loans' => true, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 3
            ],
            [
                'age_from' => null, 'age_to' => null, 'sex' => null, 'family_status' => null,
                'monthly_income_from' => null, 'monthly_income_to' => null, 'minor_children' => null, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => true, 'outstanding_current_loans' => true, 'monthly_payment_current_loans_difference_monthly_income_to' => 50,
                'estimate' => 3
            ],
            [
                'age_from' => 18, 'age_to' => null, 'sex' => null, 'family_status' => null,
                'monthly_income_from' => null, 'monthly_income_to' => 15000, 'minor_children' => null, 'minor_children_equally' => null, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 2
            ],
            [
                'age_from' => 18, 'age_to' => 35, 'sex' => null, 'family_status' => null,
                'monthly_income_from' => null, 'monthly_income_to' => 15000, 'minor_children' => null, 'minor_children_equally' => 1, 'minor_children_from' => null, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 1
            ],
            [
                'age_from' => 18, 'age_to' => 35, 'sex' => null, 'family_status' => null,
                'monthly_income_from' => null, 'monthly_income_to' => null, 'minor_children' => null, 'minor_children_equally' => null, 'minor_children_from' => 2, 'minor_children_to' => null,
                'type_employment' => null, 'availability_real_estate' => null,
                'outstanding_loans' => null, 'outstanding_current_loans' => null, 'monthly_payment_current_loans_difference_monthly_income_to' => null,
                'estimate' => 2
            ],
        ];

        foreach ($rules as $rule) {
            $estimate += $this->checkAndResult($rule, $years, $request);
        }
        if ($estimate >= 5) {
            return response()->json([
                'reject' => $messageReject
            ]);
        }
        return response()->json([
            'success' => 'Вы можете получить кредит в нашем банке...'
        ]);
    }

    private function checkAndResult($rule, $years, Request $request): int
    {
        $check = false;
        if (!is_null($rule['age_from'])) {
            $check = $rule['age_from'] <= $years;
            if ($check === false) return 0;
        }
        if (!is_null($rule['age_to'])) {
            $check = $rule['age_to'] > $years;
            if ($check === false) return 0;
        }
        if (!is_null($rule['sex'])) {
            $check = $rule['sex'] === $request['sex'];
            if ($check === false) return 0;
        }
        if (!is_null($rule['family_status'])) {
            $check = $rule['family_status'] === $request['family_status'];
            if ($check === false) return 0;
        }
        if (!is_null($rule['monthly_income_from'])) {
            $check = $rule['monthly_income_from'] <= (int)$request['monthly_income'];
            if ($check === false) return 0;
        }
        if (!is_null($rule['monthly_income_to'])) {
            $check = $rule['monthly_income_to'] > (int)$request['monthly_income'];
            if ($check === false) return 0;
        }
        if (!is_null($rule['type_employment'])) {
            $check = $rule['type_employment'] === (int)$request['type_employment'];
            if ($check === false) return 0;
        }
        if (!is_null($rule['availability_real_estate'])) {
            $check = $rule['availability_real_estate'] === (bool)$request['availability_real_estate'];
            if ($check === false) return 0;
        }
        if (!is_null($rule['minor_children'])) {
            $check = $rule['minor_children'] === true ? (int)$request['minor_children'] > 0 : (int)$request['minor_children'] === 0;
            if ($check === false) return 0;
        }
        if (!is_null($rule['minor_children_equally'])) {
            $check = $rule['minor_children_equally'] === (int)$request['minor_children'];
            if ($check === false) return 0;
        } else {
            if (!is_null($rule['minor_children_from'])) {
                $check = $rule['minor_children_from'] <= (int)$request['minor_children'];
                if ($check === false) return 0;
            }
            if (!is_null($rule['minor_children_to'])) {
                $check = $rule['minor_children_to'] > (int)$request['minor_children'];
                if ($check === false) return 0;
            }
        }
        if (!is_null($rule['outstanding_loans']) && $request['outstanding_loans'] === true) {
            $check = $rule['outstanding_loans'] === $request['outstanding_loans'];
            if ($check === false) return 0;
            if (!is_null($rule['outstanding_current_loans'])) {
                $check = $rule['outstanding_current_loans'] === (bool)$request['outstanding_current_loans'];
                if ($check === false) return 0;
            }
            if (!is_null($rule['monthly_payment_current_loans_difference_monthly_income_to'])) {
                if (((int)$request['monthly_income']/(int)$request['monthly_payment_current_loans']) <= (100/$rule['monthly_payment_current_loans_difference_monthly_income_to']) ) {
                    $check = true;
                } else{
                    return 0;
                }
            }
        }
        if ($check) {
            return $rule['estimate'];
        }
        return 0;
    }


    public function store(Request $request): void
    {
        //сохраняем в БД после получения положительного ответа...
    }
}

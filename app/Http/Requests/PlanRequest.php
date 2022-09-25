<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $form_data = $this->all();

        $start_date = strtotime($form_data["start_date"]);
        $end_date   = strtotime($form_data["end_date"]);

        // 終了日が開始日よりも後であれば開始時間と終了時間が前後しても良い
        return [
            'start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'end_time'   => 'required|date_format:H:i' . ($start_date < $end_date ? '' : '|after_or_equal:start_time'),
            'color'      => 'required|integer|between:1,6',
            'content'    => 'required|max:128',
            'detail'     => 'max:255',
        ];
    }

    /**
     * パラメータ名を返す
     * 
     * @return array
     */
    public function attributes()
    {
        return [
            'start_date' => '開始日',
            'start_time' => '開始時刻',
            'end_date'   => '終了日',
            'end_time'   => '終了時刻',
            'color'      => 'カラー',
            'content'    => '内容',
            'detail'     => '詳細',
        ];
    }
}

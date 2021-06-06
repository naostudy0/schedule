<?php

namespace App\Plan;

use App\Plan\PlanGet;

class PlanView extends PlanGet
{
    protected array $html_color = [];

    /**
     * scheduleに上部に表示するタグカラーのhtmlを作成する
     * 
     * @return string
     */
    public function getTagColor()
    {
        $get_plan_colors = $this->getPlanColors();

        if ( count($get_plan_colors) >= 2) {
            $this->html_color[] = '<li class="color selected" id="all">ALL</li>';

            foreach ($get_plan_colors as $key => $value) {
                $this->html_color[] = '<li class="color" id="' . $value . '">&nbsp;</li>';
            }
        } else {
            foreach ( $get_plan_colors as $key => $value ) {
                $this->html_color[] = '<li class="color selected" id="' . $value . '">&nbsp;</li>';
            }
        }
        return implode("", $this->html_color);
    }

    protected array $html_plan = [];

    /**
     * scheduleに表示する予定一覧のhtmlを作成する
     * 
     * @return string
     */
    public function getPlanView()
    {
        $plans = $this->getPlans();
        $plan_days = $this->getPlanDays();

        // 予定がある日付
        foreach ( $plan_days as $plan_day ) {
            $this->html_plan[] = '<div class="plans-oneday">';
            $this->html_plan[] =    "<h3>" . str_replace('-', '/', $plan_day['start_date']) . "</h3>";

            // 予定
            foreach ($plans as $plan) {
                if ( $plan['start_date'] ===  $plan_day['start_date'] ) {

                    $this->html_plan[] = '<div class="plan-wrap ' . $plan['color'] . '" style="background-color: ' . $plan['color'] . ';">';
                    $this->html_plan[] =    '<section class="plan">';
                    $this->html_plan[] =        '<div class="plan-main">';
                    $this->html_plan[] =            '<i class="fas fa-angle-double-down float-right"></i>';
                    $this->html_plan[] =            '<p class="datetime">';
                    $this->html_plan[] =                '<time datetime="' . $plan['start_date'] . 'T' . $plan['start_time'] . '">';
                    $this->html_plan[] =                    str_replace('-', '/', $plan['start_date']) . '&nbsp;';
                    $this->html_plan[] =                    '<span class="start-time">' . substr($plan['start_time'], 0, 5) . '</span>';
                    $this->html_plan[] =                '</time> 〜 ';
                    $this->html_plan[] =                '<span class="inline-block"><time datetime="' . $plan['end_date'] . 'T' . $plan['end_time'] . '">';
                    $this->html_plan[] =                    str_replace('-', '/', $plan['end_date']) . '&nbsp;' . substr($plan['end_time'], 0, 5) . '</time></span>';
                    $this->html_plan[] =            '</p>'; // datetime
                    $this->html_plan[] =            '<p class="content">' . $plan['content'] . '</p>';
                    $this->html_plan[] =        '</div>'; // plan-main

                    $this->html_plan[] =        '<div class="plan-other">';
                    $this->html_plan[] =            '<div class="detail">';
                    $this->html_plan[] =                '<p>' . $plan['detail'] . '</p>';
                    $this->html_plan[] =            '</div>'; // detail

                    $this->html_plan[] =            '<div class="btn-wrap">';
                    $this->html_plan[] =                '<a class="btn btn-primary" href="/plan/update?id=' . $plan['id'] . '">修正</a>';
                    $this->html_plan[] =                '<a class="btn btn-danger" href="/plan/delete_confirm?id=' . $plan['id'] . '">削除</a>';
                    $this->html_plan[] =            '</div>'; // btn-wrap
                    $this->html_plan[] =        '</div>'; // plan-other
                    $this->html_plan[] =    '</section>'; // plan
                    $this->html_plan[] = '</div>'; // plan_wrap
                }
            }

            $this->html_plan[] = '</div>'; // plans-oneday
        }

        return implode("", $this->html_plan);
    }
}
<?php

namespace App\Plan\LayoutSet;

class LayoutSet
{
    private $tag_colors;
    private $height_content;
    private $new_line;
    private $tag_color;
    private $len_content;
    private $len_detail;
    private $search;
    private $layout;

    /**
     * 「内容」「詳細」「タグ」の表示方法を設定する
     * 
     * @param array
     */
    public function __construct($plan_data)
    {
        
        // 入力された「内容」の文字数に応じて高さを設定
        $this->len_content = strlen($plan_data['content']);

        if ($this->len_content > 250 ) {
            $this->height_content = '110px';
        } elseif ($this->len_content >= 150) {
            $this->height_content = '90px';
        } else {
            $this->height_content = '50px';
        }
        
        // 入力された「詳細」の文字数に応じて上下中央揃え、
        // または上下中央揃えをせずスクロールできるようにするかを設定
        $this->new_line = strpos($plan_data['detail'], "\n");

        $this->len_detail = strlen($plan_data['detail']);
        if ($this->len_detail > 250 || $this->new_line) {
            $this->display_detail = 'block';
        } else {
            $this->display_detail = 'table';
        }
        
        // 定数から「タグ」の色の名前に変換
        $this->tag_colors = config('const.plan_color');
        $this->search = $plan_data['color'];
        $this->tag_color = array_search($this->search, $this->tag_colors);

        $this->layout['height_content'] = $this->height_content;
        $this->layout['display_detail'] = $this->display_detail;
        $this->layout['tag_color'] = $this->tag_color;
    }

    /**
     * 「内容」「詳細」「タグ」の表示方法を配列で返す
     * 
     * @return array
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
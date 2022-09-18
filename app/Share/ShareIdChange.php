<?php

namespace App\Share;

use App\Models\User;

class ShareIdChange
{
    private $id;
    private $name;

    /**
     * share_id（ユーザ検索用ID）からIDと名前を取得する
     * 
     * @param int
     */
    public function __construct($share_id)
    {
        $user = User::where('share_id', $share_id)->first();
        $this->id = $user['id'];
        $this->name = $user['name'];
    }

    /**
     * IDを返す
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 名前を返す
     * 
     * @param string
     */
    public function getName()
    {
        return $this->name;
    }
}
<?php
/**
 * BaseTim.php
 * @author wufeng
 * @date 2021/7/5
 */

namespace wufeng\tim;


class BaseTim
{
    protected $error;

    public function getMessage(){
        return $this->error;
    }
}

<?php
/**
 * a workflow for Generate Password.
 * @version 0.5.1
 * @author Zjmainstay(admin@zjmainstay.cn)
 */

require 'vendor/workflows.php';

/**
 * Class GeneratePassword
 */
class GeneratePassword
{
    protected $argv = '';
    public function __construct($argv = '') {
        $this->argv = $argv;
    }
    /**
     * Present the result.
     */
    public function show()
    {
        $workflow = new Workflows();
        if($this->argv == '-h' || $this->argv == '--help') {
            $usageTextArr = [
                "length:       length of password",
                "type:         number/lower/upper/special join by -",
                "type:         or all/custom",
                "customStr:    type=custom, customStr=123 use 123 only",
                "default: length=32&times=5&type=number-lower-upper", //Maybe bug. This will go to the first line
                "splitChar:    split char for password, default is empty",
                "splitLength:  split password by every splitLength step",
                "times:        create how many password in one time",
            ];
            foreach ($usageTextArr as $key => $value) {
                $workflow->result($key + 1, '', $value, null, '', null);
            }
        } else {
            $defaultData = array(
                    'length'        => 32,
                    'type'          => 'number-lower-upper',
                    'customStr'     => '',
                    'splitChar'     => '',
                    'splitLength'   => 4,
                    'times'         => 5,
                );

            parse_str($this->argv, $post);

            $post = array_merge($defaultData, $post);

            if(empty($post['times'])) {
                $post['times'] = $defaultData['times'];
            }

            if(empty($post['splitLength'])) {
                $post['splitLength'] = $defaultData['splitLength'];
            }

            $passwordArr    = array();
            for($i = 0; $i < $post['times']; $i++) {
                $passwordArr[] = $this->getPassword($post['type'], $post['customStr'], $post['length'], $post['splitChar'], $post['splitLength']);
            }

            foreach ($passwordArr as $key => $value) {
                $workflow->result($key + 1, $value, $value, null, '', null);
            }
        }

        echo $workflow->toxml();
    }

    /**
     *  获取随机密码函数
     *  @param string $type 密码字符串类型
     *  @param string $customStr 自定义密码组成字符
     *  @param int    $length 密码长度
     *  @param string $splitChar 分隔符，默认不分隔
     *  @param int    $splitLength 密码字符分隔长度 比如：密码为abc-def-ghi时$splitLength = 3
     *
     */
    protected function getPassword($type = 'number-lower-upper', $customStr = '', $length = 32, $splitChar = '', $splitLength = 4) {
        $strArr         = array(
            'number'    => '0123456789',
            'lower'     => 'abcdefghijklmnopqrstuvwxyz',
            'upper'     => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            'special'   => '~!@#$%^&*()_+{}|[]\-=:<>?/',
        );
        $defaultType    = 'number-lower-upper';

        if($type == 'all') {        //全部
            $str = implode('', $strArr);
        } else if($type == 'custom') {        //自定义类型
            if(empty($customStr)) {    //未填写自定义类型，则默认为数字+大小写字母
                $type    = $defaultType;
            } else {
                $str     = $customStr;
            }
        }

        //custom 没带自定义类型 或 其他类型
        if(empty($str)) {
            $typeParts  = array_intersect(array_keys($strArr), (array)explode('-', $type));
            if(empty($typeParts)) {
                $typeParts = explode('-', $defaultType);
            }
            $str        = '';
            foreach($typeParts as $part) {
                $str   .= $strArr[$part];
            }
        }

        if(empty($length)) {
            $length = 32;
        }

        // 大数据下面str_shuffle会导致随机种子耗尽而导致结果循环（错误做法）
        // $passwordStr    = '';
        // do {
            // $randStr        = str_shuffle($str);
            // $passwordStr   .= substr($randStr, 0, 1);        //每次取一个字符
            // $passwordLength = strlen($passwordStr);
        // } while($passwordLength < $length);

        //纠正
        $passwordStr    = '';
        $strMaxIndex    = strlen($str) - 1;
        do {
            $randIndex      = mt_rand(0, $strMaxIndex);
            $passwordStr   .= $str[$randIndex];        //每次取一个字符
            $passwordLength = strlen($passwordStr);
        } while($passwordLength < $length);

        //需要分隔
        if($splitChar != '') {
            $passwordStr = implode($splitChar, str_split($passwordStr, $splitLength));
        }

        return $passwordStr;
    }

}


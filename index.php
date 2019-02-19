<?php
/**
 * Created by PhpStorm.
 * User: Алия
 * Date: 18.02.2019
 * Time: 11:15
 **/
/* При получении введённых пользователей данных, скрипт посимвольно выполняет команды из строки.
 Символы, не являющиеся управляющими, игнорируются.
Если встречается команда "," (ввод данных пользователем), считывается 1 символ из строки входных параметров,
если встречается команда "." (вывод данных), выводимое значение записывается в конец строки вывода.
После окончания выполнения, строка вывода передаётся пользователю. */


$code = "";
if (isset($_POST["code"])){
    $code = $_POST["code"];
}

$paramString = "";
if (isset($_POST["param"])){
    $paramString = $_POST["param"];
}

$param = [];
for ($i = 0; $i < strlen($paramString); $i++){
    $param[$i] = ord($paramString[$i]);
}

function interpret($code, $param){

    $paramIndex = 0;
    $myParam = [];
    $myParamLastIndex = -1;

    if (empty($param)){
        for ($i = 0; $i < 100; $i++){
            array_push($myParam, 0);
            $myParamLastIndex++;
        }
    }

    $result = "";
    $hooks = [];

    for ($i = 0; $i < strlen($code);){
        switch ($code{$i}){

            case ">":
                if (count($myParam) == $myParamLastIndex + 1){
                    array_push($myParam, 0);
                }
                $myParamLastIndex++;
                $i++;
                break;

            case "<":
                $myParamLastIndex--;
                $i++;
                break;

            case "+":
                if ($myParam[$myParamLastIndex] == 255){
                    $myParam[$myParamLastIndex] = 0;
                }
                else{
                    $myParam[$myParamLastIndex]++;
                }
                $i++;
                break;

            case "-":
                if ($myParam[$myParamLastIndex] == 0){
                    $myParam[$myParamLastIndex] = 255;
                }
                else{
                    $myParam[$myParamLastIndex]--;
                }
                $i++;
                break;

            case ".":
                $result .= chr($myParam[$myParamLastIndex]);
                $i++;
                break;

            case ",":
                if ($myParamLastIndex == -1){
                    $myParamLastIndex++;
                }
                $myParam[$myParamLastIndex] = $param[$paramIndex];
                $paramIndex++;
                $i++;
                break;

            case "[":
                if ($myParam[$myParamLastIndex] == 0){
                    $closeHooks = 1;
                    while (1){
                        $i++;
                        if ($code[$i] == "["){
                            $closeHooks++;
                        }
                        elseif ($code[$i] == "]"){
                            $closeHooks--;
                        }
                        if ($closeHooks == 0){
                            break;
                        }
                    }
                }
                else{
                    array_push($hooks, $i);
                    $i++;
                }
                break;

            case "]":
                if ($myParam[$myParamLastIndex] != 0){
                    $i = $hooks[count($hooks) - 1] + 1;
                }
                else{
                    array_pop($hooks);
                    $i++;
                }
                break;

            default:
                $i++;
                break;
        }
    }
    return $result;
}
echo @interpret($code, $param);
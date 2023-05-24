<?php
error_reporting(0);
ini_set('display_errors', 0);

function generateRandomIp() {
    $randNum1 = mt_rand(1, 255);
    $randNum2 = mt_rand(1, 255);
    $randNum3 = mt_rand(1, 255);
    $ip = "101.$randNum1.$randNum2.$randNum3";
    return $ip;
}

function getToken() {
    $timeStamp = time();
    $authKey = md5("testtest" . $timeStamp);
    $tokenUrl = "https://hlwicpfwc.miit.gov.cn/icpproject_query/api/auth";
    $data = [
        "authKey" => $authKey,
        "timeStamp" => $timeStamp
    ];
    $headers = [
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
        "Origin: https://beian.miit.gov.cn/",
        "Referer: https://beian.miit.gov.cn/",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36",
        "CLIENT-IP: " . generateRandomIp(),
        "X-FORWARDED-FOR: " . generateRandomIp()
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['params']['bussiness'];
}

function gt(){
    global $tokens;
    $tokens = getToken();
    return $tokens;
}


function getCode() {
    $codeUrl = "https://hlwicpfwc.miit.gov.cn/icpproject_query/api/image/getCheckImage";
    $headers = [
        "Content-Type: application/x-www-form-urlencoded;charset=UTF-8",
        "Origin: https://beian.miit.gov.cn/",
        "Referer: https://beian.miit.gov.cn/",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.87 Safari/537.36",
        "token: " . gt()
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $codeUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['params']['uuid'];
}

function getDomain($name) {
    $url = "https://hlwicpfwc.miit.gov.cn/icpproject_query/api/icpAbbreviateInfo/queryByCondition";
    $headers = [
    "Content-Type: application/json;charset=UTF-8",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36",
    "Accept: application/json, text/plain, */*",
    "uuid: " . getCode($tokens),
    "token: " . gt(),
    "Origin: https://beian.miit.gov.cn/",
    "Referer: https://beian.miit.gov.cn/"
    ];

    $data = [
        "pageNum" => "1",
        "pageSize" => "100",
        "unitName" => $name
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $domains = $result['params']['list'];
    foreach ($domains as $domain) {
        echo $domain['domain'] . "<br>";
    }

}

function getICP($name) {
    $url = "https://hlwicpfwc.miit.gov.cn/icpproject_query/api/icpAbbreviateInfo/queryByCondition";
    $headers = [
        "Content-Type: application/json;charset=UTF-8",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36",
        "Accept: application/json, text/plain, */*",
        "uuid: " . getCode($tokens),
        "token: " . gt(),
        "Origin: https://beian.miit.gov.cn/",
        "Referer: https://beian.miit.gov.cn/"
    ];

    $data = [
        "pageNum" => "1",
        "pageSize" => "100",
        "unitName" => $name
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $icps = $result['params']['list'];
    return $icps[0]['mainLicence'];
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['domain'])) {
//         $icp = getICP($_POST['domain']);
//         getDomain($icp);
//     }
//     if (isset($_POST['name'])) {
//         getDomain($_POST['name']);
//     }
//     if (isset($_POST['icp'])) {
//         getDomain($_POST['icp']);
//     }
// }
?>

<!DOCTYPE html>
<html>
<head>
    <title>ICP备案域名查询</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            margin-top: 30px;
        }
        .container {
            width: 400px;
            margin: 0 auto;
            margin-top: 50px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #fff;
            background-color: #337ab7;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .result {
            margin-top: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .result h2 {
            margin: 0;
            font-size: 18px;
        }
        .result p {
            margin-top: 10px;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ICP备案域名查询</h1>
        <form method="POST" action="icp.php">
            <label>查询域名备案单位下的其他域名:</label>
            <input type="text" name="domain"><br>

            <label>查询该备案单位下的域名:</label>
            <input type="text" name="name"><br>

            <label>备案号查域名:</label>
            <input type="text" name="icp"><br>

            <input type="submit" value="查询">
        </form>

<?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $domain = $_POST['domain'];
            $name = $_POST['name'];
            $icp = $_POST['icp'];

            if (!empty($domain)) {
                $icp = getICP($domain);
                if (!empty($icp)) {
                    echo '<div class="result">';
                    echo '<h2>查询域名备案单位下的其他域名:</h2>';
                    echo '<p>';
                    getDomain($icp);
                    echo '</p>';
                    echo '</div>';
                }
            }

            if (!empty($name)) {
                echo '<div class="result">';
                echo '<h2>查询到'.$name.'下的域名为:</h2>';
                echo '<p>';
                getDomain($name);
                echo '</p>';
                echo '</div>';
            }

            if (!empty($icp)) {
                echo '<div class="result">';
                echo '<h2>备案号查域名:</h2>';
                echo '<p>';
                getDomain($icp);
                echo '</p>';
                echo '</div>';
            }
        }
    ?>
</div>
</body>
</html>


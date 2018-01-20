<?php

namespace system;

use app\model\ConstantModel;
use lib\Log;
use lib\Route;

class Controller
{
    protected $params = [];

    protected $view = null;

    protected $siteSet = null;

    protected $user = null;

    protected $start_time;

    protected $end_time;

    protected $config = [];

    public function __construct()
    {
        $this->start_time = microtime(true);
        $view             = new View();
        $this->view       = $view->view;
        $this->config     = \Init::getConfig();
        $this->params     = $this->paramsFilter();
    }

    protected function paramsFilter()
    {
        $params = [];
        /* 过滤所有GET过来变量 */
        foreach ($_GET as $get_key => $get_var) {
            $params[strtolower($get_key)] = $this->get_str($get_var);
        }

        /* 过滤所有POST过来的变量 */
        foreach ($_POST as $post_key => $post_var) {
            $params[strtolower($post_key)] = $this->get_str($post_var);
        }

        $query  = Route::getQuerys();
        $query  = empty($query) ? [] : $query;
        $params = array_merge($params, $query);

        return $params;
    }

    protected function get_str($string)
    {
        if (!get_magic_quotes_gpc()) {
            return addslashes($string);
        }
        return trim($string);
    }

    protected function redirect($c, $a, $url = '', $params = array())
    {
        if (!empty($url)) {
            header("Location:" . $url);
            exit();
        }
        $query = '';
        if (!empty($params)) {
            foreach ($params as $k => $v) {
                $query .= '/' . $k . '/' . $v;
            }
        }

        header("Location:" . "/" . $c . "/" . $a . $query);

        exit();
    }

    protected function alert($data)
    {
        if (is_array($data) && isset($data['code'])) {
            if (is_array($data['code'])) {
                $msg = '';
                foreach ($data['code'] as $code) {
                    $msg .= (!empty(constant::$codeMsg[$code]) ? constant::$codeMsg[$code] : ConstantModel::$codeMsg[$code]) . ' or ';
                }
                $msg = substr($msg, 0, -4);
            } else {
                $msg = (!empty(constant::$codeMsg[$data['code']]) ? constant::$codeMsg[$data['code']] : ConstantModel::$codeMsg[$data['code']]);
            }
        } else {
            $msg = $data;
        }
        echo '<script>alert("' . $msg . '");history.go(-1);</script>';
        exit();
    }

    protected function checkRefer()
    {

    }

    protected function renderJson($data = array())
    {
        header('HTTP/1.1 200 OK');
        header('Content-type: application/json');
        if (!isset($data['code'])) {
            $data["code"] = constant::SUCCESS;
        }
        if (!isset($data["msg"])) {
            if (is_array($data['code'])) {
                $msg = '';
                foreach ($data['code'] as $code) {
                    $msg .= (!empty(constant::$codeMsg[$code]) ? constant::$codeMsg[$code] : ConstantModel::$codeMsg[$code]) . ' or ';
                }
                $msg = substr($msg, 0, -4);
            } else {
                $msg = (!empty(constant::$codeMsg[$data['code']]) ? constant::$codeMsg[$data['code']] : ConstantModel::$codeMsg[$data['code']]);
            }
            $data["msg"] = empty($msg) ? '' : $msg;
        }
        if (isset($this->params["callback"])) {
            echo isset($this->params["callback"]) . '(' . json_encode($data) . ')';
        } else {
            echo json_encode($data, true);
        }
        exit();
    }


    protected function renderView($template, $data = array())
    {
        // 渲染模板
        echo $this->view->render($template . '.html', $data);
        exit();
    }


    protected function renderError($data = array(), $template = 'error')
    {
        if (!isset($data['code'])) {
            $data['code'] = constant::FALSE;
        }
        if (!isset($data['msg'])) {
            if (is_array($data['code'])) {
                $msg = '';
                foreach ($data['code'] as $code) {
                    $msg .= (!empty(constant::$codeMsg[$code]) ? constant::$codeMsg[$code] : ConstantModel::$codeMsg[$code]) . ' or ';
                }
                $msg = substr($msg, 0, -4);
            } else {
                $msg = (!empty(constant::$codeMsg[$data['code']]) ? constant::$codeMsg[$data['code']] : ConstantModel::$codeMsg[$data['code']]);
            }
            $data['msg'] = empty($msg) ? '' : $msg;
        }
        $this->renderView($template, $data);
    }

    public function imgUpload($file, $allowTypes, $path)
    {
        if (empty($file['name'])) {
            $this->renderError(['code' => constant::IMAGE_UPLOAD_ERROR]);
        }

        if (!in_array($file['type'], $allowTypes)) {
            $this->renderError(['code' => constant::IMAGE_TYPE_ERROR]);
        }

        if (constant::IMAGE_ALLOW_UPLOAD_SIZE < $file['size']) {
            $this->renderError(['code' => constant::IMAGE_SIZE_ERROR]);
        }


        if (empty($path)) {
            $path = constant::IMAGE_UPLOAD_PATH;
        }

        $filename = md5(date('YmdHis') . rand(1000, 9999)) . substr($file['name'], strpos($file['name'], '.'));

        // 访问路径
        $url = $path . $filename;
        // 存储路径
        $savePath = ROOT_PATH . $url;

        try {
            move_uploaded_file($file["tmp_name"], $savePath);  // 文件存储
        } catch (\Exception $e) {
            $this->renderError(['code' => constant::IMAGE_UPLOAD_ERROR]);
        }

        // 返回文件访问路径(不含域名)
        return $url;
    }

    public function fileUp($file, $allowTypes, $path)
    {
        if (empty($file['name'])) {
            $this->renderJson(['code' => constant::FILE_UPLOAD_ERROR]);
        }

        if (!in_array($file['type'], $allowTypes)) {
            $this->renderJson(['code' => constant::FILE_TYPE_ERROR]);
        }

        if (constant::FILE_ALLOW_UPLOAD_SIZE < $file['size']) {
            $this->renderJson(['code' => constant::FILE_SIZE_ERROR]);
        }

        $filename = md5(date('YmdHis') . rand(1000, 9999)) . substr($file['name'],
                strpos($file['name'], '.'));

        // 访问路径
        $url = $path . $filename;
        // 存储路径
        $savePath = ROOT_PATH . $url;

        try {
            move_uploaded_file($file["tmp_name"], $savePath);  // 文件存储
        } catch (\Exception $e) {
            $this->renderJson(['code' => constant::FILE_UPLOAD_ERROR]);
        }

        // 返回文件访问路径(不含域名)
        return $url;
    }

    // for sunmernote
    public function imageUpload()
    {
        $file = $_FILES['file'];

        $allowTypes = [
            'image/jpg',
            'image/png',
            'image/jpeg',
            'image/pjpeg',
            'image/gif',
            'image/bmp',
            'image/x-png',
        ];

        $path = ConstantModel::IMAGE_UPLOAD_PATH;
        echo $this->getShowUrls([$this->imgUpload($file, $allowTypes, $path)]);
        exit();
    }

    public function upBase64Image($data, $allowTypes = array(), $path)
    {
        $data = explode("data:", $data);
        unset($data[0]);
        foreach ($data as $key => $v) {
            $data[$key] = 'data:' . $v;
        }

        $img_urls = [];

        foreach ($data as $value) {
            if (preg_match('/^(data:\s*image\/)(\w+)(;base64,)/', $value, $matches)) {
                $type = $matches[2];
            } else {
                $this->renderJson(['code' => constant::IMAGE_UPLOAD_ERROR]);
            }

            if (!in_array($type, $allowTypes)) {
                $this->renderJson(['code' => constant::IMAGE_TYPE_ERROR]);
            }

            $img      = str_replace($matches[0], '', $value);
            $img      = base64_decode($img);
            $filename = md5(date('YmdHis') . rand(1000, 9999)) . '.' . $type;

            // 访问路径
            $url = $path . $filename;
            // 存储路径
            $savePath = ROOT_PATH . $url;

            try {
                file_put_contents($savePath, $img);
                $img_urls[] = $url;
            } catch (\Exception $e) {
                $this->renderJson(['code' => constant::IMAGE_UPLOAD_ERROR]);
            }
        }

        return $img_urls;
    }

    public function fileNameEncrypt($fileName)
    {
        return md5(date('YmdHis') . rand(1000, 9999)) . substr($fileName, strpos($fileName, '.'));
    }

    public function webUploader()
    {
        header('Content-type: application/json');
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // 5 minutes execution time
        @set_time_limit(5 * 60);

        // Uncomment this one to fake upload time
        // usleep(5000);

        // Get a file name
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
        }

        $targetDir = UPLOAD_PATH . $this->config['upload_dir']['tmp'];

        function isAllowType($fileName, $types)
        {
            return stripos($types, substr($fileName, strpos($fileName, '.') + 1));
        }

        $shortDir = '';
        if ($this->params['type'] == 'image') {
            if (isAllowType($fileName, $this->config['upload_type']['images']) !== false) {
                $shortDir = $this->config['upload_dir']['images'];
            } else {
                $this->renderJson([
                    'code' => ConstantModel::IMAGE_TYPE_ERROR,
                    'msg'  => '图片格式错误：仅允许 ' . $this->config['upload_type']['images'] . ' 格式',
                ]);
            }
        } elseif ($this->params['type'] == 'file') {
            if (isAllowType($fileName, $this->config['upload_type']['files']) !== false) {
                $shortDir = $this->config['upload_dir']['files'];
            } else {
                $this->renderJson([
                    'code' => ConstantModel::FILE_TYPE_ERROR,
                    'msg'  => '文件格式错误：仅允许 ' . $this->config['upload_type']['files'] . ' 格式',
                ]);
            }
        } else {
            $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
        }

        $uploadDir = UPLOAD_PATH . $shortDir;
        $fileName  = $this->fileNameEncrypt($fileName);

        $cleanupTargetDir = true; // Remove old files
        $maxFileAge       = 5 * 3600; // Temp file age in seconds

        if (!file_exists($targetDir)) {
            @mkdir($targetDir);
        }
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir);
        }

        $filePath   = $targetDir . DIR_SIGN . $fileName;
        $uploadPath = $uploadDir . DIR_SIGN . $fileName;

        // Chunking might be enabled
        $chunk  = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;

        // Remove old temp files
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
            }

            while (($file = readdir($dir)) !== false) {
                $tmpfilePath = $targetDir . DIR_SIGN . $file;
                if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
                    continue;
                }
                if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);
                }
            }
            closedir($dir);
        }

        if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
            $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
        }

        if (!empty($_FILES)) {
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
            }
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
            }
        } else {
            if (!$in = @fopen("php://input", "rb")) {
                $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
            }
        }

        while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

        $index = 0;
        $done  = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (!file_exists("{$filePath}_{$index}.part")) {
                $done = false;
                break;
            }
        }
        if ($done) {
            if (!$out = @fopen($uploadPath, "wb")) {
                $this->renderJson(['code' => ConstantModel::FILE_UPLOAD_ERROR]);
            }

            if (flock($out, LOCK_EX)) {
                for ($index = 0; $index < $chunks; $index++) {
                    if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
                        break;
                    }

                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }

                    @fclose($in);
                    @unlink("{$filePath}_{$index}.part");
                }

                flock($out, LOCK_UN);
            }
            @fclose($out);
        }

        $shortPath = $shortDir . DIR_SIGN . $fileName;

        // Return Success JSON-RPC response
        $this->renderJson(['url' => trim($shortPath, '.')]);
    }

    public function getShowUrls($urls, $only_need_array = false)
    {
        if (!is_array($urls)) {
            $urls = json_decode($urls, true);
        }
        $res_urls = [];

        foreach ($urls as $url) {
            if (strpos($url, $this->config['upload_dir']['base']) !== false) {
                $res_urls[] = BASE_URL . $url;
            } else {
                $res_urls[] = BASE_URL . $this->config['upload_dir']['base'] . $url;
            }
        }
        if (count($res_urls) < 2 && !$only_need_array) {
            return $res_urls[0];
        } else {
            return $res_urls;
        }

    }

    public function getIP()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');

        } elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function __destruct()
    {
        $this->end_time = microtime(true);
        $time           = round(($this->end_time - $this->start_time) * 1000) . 'ms';
        $errMsg         = error_get_last();
        if (null != $errMsg) {
            Log::error(json_encode($errMsg) . '-' . $time);
        }
    }
}

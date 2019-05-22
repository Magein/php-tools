<?php

namespace magein\php_tools\extra;

class UEditor
{

    /**
     * 上传图片的动作名称
     */
    const UPLOAD_TYPE_IMAGE = 'upload_image';

    /**
     * 上传图片用于接受数据的名称
     */
    const UPLOAD_FILENAME_IMAGE = 'image';

    private $fileField; //文件域名
    private $file; //文件上传对象
    private $base64; //文件上传对象
    private $config; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $savePath = 'uploads/'; //完整文件名,即从当前配置目录开始的URL
    private $filePath; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        'SUCCESS', //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        '文件大小超出 upload_max_filesize 限制',
        '文件大小超出 MAX_FILE_SIZE 限制',
        '文件未被完整上传',
        '没有文件被上传',
        '上传文件为空',
        'ERROR_TMP_FILE' => '临时文件错误',
        'ERROR_TMP_FILE_NOT_FOUND' => '找不到临时文件',
        'ERROR_SIZE_EXCEED' => '文件大小超出网站限制',
        'ERROR_TYPE_NOT_ALLOWED' => '文件类型不允许',
        'ERROR_CREATE_DIR' => '目录创建失败',
        'ERROR_DIR_NOT_WRITEABLE' => '目录没有写权限',
        'ERROR_FILE_MOVE' => '文件保存时出错',
        'ERROR_FILE_NOT_FOUND' => '找不到上传文件',
        'ERROR_WRITE_CONTENT' => '写入文件内容错误',
        'ERROR_UNKNOWN' => '未知错误',
        'ERROR_DEAD_LINK' => '链接不可用',
        'ERROR_HTTP_LINK' => '链接不是http链接',
        'ERROR_HTTP_CONTENTTYPE' => '链接contentType不正确',
        'INVALID_URL' => '非法 URL',
        'INVALID_IP' => '非法 IP'
    );

    /**
     * 兼用web接口访问
     *
     * 使用场景：为小程序，app提供接口的时候，图片如果是基于项目目录下回导致访问失败
     *
     * @var bool
     */
    private $useWebUrl = true;

    /**
     * @var string
     */
    private $host = '';

    /**
     * @param $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @param bool $use
     */
    public function setUseWebUrl(bool $use = true)
    {
        $this->useWebUrl = $use ? true : false;
    }

    /**
     * @param $params
     * @return array|false|string
     */
    public function init($params)
    {

        $action = 'config';
        if (isset($params['action'])) {
            $action = $params['action'];
        }

        $config = $this->config();
        $result = [];
        switch ($action) {
            case 'config':
                $result = $config;
                break;
            case self::UPLOAD_TYPE_IMAGE:
                $this->config = [
                    'pathFormat' => $config['imagePathFormat'],
                    'maxSize' => $config['imageMaxSize'],
                    'allowFiles' => $config['imageAllowFiles']
                ];
                $this->file = $_FILES[self::UPLOAD_FILENAME_IMAGE];
                $this->upFile();
                break;
        }

        if (empty($result)) {
            $result = $this->getFileInfo();
        }

        return $result;
    }


    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file;

        if (!$file) {
            $this->stateInfo = $this->getStateInfo('ERROR_FILE_NOT_FOUND');
            return;
        }
        if ($file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            return;
        } else if (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo('ERROR_TMP_FILE_NOT_FOUND');
            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo('ERROR_TMPFILE');
            return;
        }

        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);
        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');
            return;
        }

        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo('ERROR_TYPE_NOT_ALLOWED');
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo('ERROR_CREATE_DIR');
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo('ERROR_DIR_NOT_WRITEABLE');
            return;
        }

        //移动文件
        if (!(move_uploaded_file($file['tmp_name'], $this->filePath) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo('ERROR_FILE_MOVE');
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);

        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo('ERROR_CREATE_DIR');
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo('ERROR_DIR_NOT_WRITEABLE');
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo('ERROR_WRITE_CONTENT');
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }

    }

    /**
     * 拉取远程图片
     * @return mixed
     */
    private function saveRemote()
    {
        $imgUrl = htmlspecialchars($this->fileField);
        $imgUrl = str_replace('&amp;', '&', $imgUrl);

        //http开头验证
        if (strpos($imgUrl, 'http') !== 0) {
            $this->stateInfo = $this->getStateInfo('ERROR_HTTP_LINK');
            return;
        }

        preg_match('/(^https*:\/\/[^:\/]+)/', $imgUrl, $matches);
        $host_with_protocol = count($matches) > 1 ? $matches[1] : '';

        // 判断是否是合法 url
        if (!filter_var($host_with_protocol, FILTER_VALIDATE_URL)) {
            $this->stateInfo = $this->getStateInfo('INVALID_URL');
            return;
        }

        preg_match('/^https*:\/\/(.+)/', $host_with_protocol, $matches);
        $host_without_protocol = count($matches) > 1 ? $matches[1] : '';

        // 此时提取出来的可能是 ip 也有可能是域名，先获取 ip
        $ip = gethostbyname($host_without_protocol);
        // 判断是否是私有 ip
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
            $this->stateInfo = $this->getStateInfo('INVALID_IP');
            return;
        }

        //获取请求头并检测死链
        $heads = get_headers($imgUrl, 1);
        if (!(stristr($heads[0], '200') && stristr($heads[0], 'OK'))) {
            $this->stateInfo = $this->getStateInfo('ERROR_DEAD_LINK');
            return;
        }
        //格式验证(扩展名验证和Content-Type验证)
        $fileType = strtolower(strrchr($imgUrl, '.'));
        if (!in_array($fileType, $this->config['allowFiles']) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], 'image')) {
            $this->stateInfo = $this->getStateInfo('ERROR_HTTP_CONTENTTYPE');
            return;
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl, false, $context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match('/[\/]([^\/]*)[\.]?[^\.\/]*$/', $imgUrl, $m);

        $this->oriName = $m ? $m[1] : '';
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->filePath = $this->getFilePath();
        $this->fileName = $this->getFileName();
        $dirname = dirname($this->filePath);

        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo('ERROR_SIZE_EXCEED');
            return;
        }

        //创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            $this->stateInfo = $this->getStateInfo('ERROR_CREATE_DIR');
            return;
        } else if (!is_writeable($dirname)) {
            $this->stateInfo = $this->getStateInfo('ERROR_DIR_NOT_WRITEABLE');
            return;
        }

        //移动文件
        if (!(file_put_contents($this->filePath, $img) && file_exists($this->filePath))) { //移动失败
            $this->stateInfo = $this->getStateInfo('ERROR_WRITE_CONTENT');
        } else { //移动成功
            $this->stateInfo = $this->stateMap[0];
        }

    }

    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap['ERROR_UNKNOWN'] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getFullName()
    {
        //替换日期事件
        $t = time();
        $d = explode('-', date('Y-y-m-d-H-i-s'));
        $format = $this->config['pathFormat'];
        $format = str_replace('{yyyy}', $d[0], $format);
        $format = str_replace('{yy}', $d[1], $format);
        $format = str_replace('{mm}', $d[2], $format);
        $format = str_replace('{dd}', $d[3], $format);
        $format = str_replace('{hh}', $d[4], $format);
        $format = str_replace('{ii}', $d[5], $format);
        $format = str_replace('{ss}', $d[6], $format);
        $format = str_replace('{time}', $t, $format);

        //过滤文件名的非法自负,并替换文件名
        $oriName = substr($this->oriName, 0, strrpos($this->oriName, '.'));
        $oriName = preg_replace('/[\|\?\'\<\>\/\*\\\\]+/', '', $oriName);
        $format = str_replace('{filename}', $oriName, $format);

        //替换随机字符串
        $randNum = rand(1, 999999999) . rand(1, 999999999);
        if (preg_match('/\{rand\:([\d]*)\}/i', $format, $matches)) {
            $format = preg_replace('/\{rand\:[\d]*\}/i', substr($randNum, 0, $matches[1]), $format);
        }

        $ext = $this->getFileExt();

        return trim($format . $ext, '/');
    }

    /**
     * 获取文件名
     * @return string
     */
    private function getFileName()
    {
        return substr($this->filePath, strrpos($this->filePath, '/') + 1);
    }

    /**
     * 上传路径 使用绝对路径
     * @param $path
     */
    public function setSavePath($path)
    {
        $path = trim($path, '/') . '/';

        $this->savePath = $path;
    }

    /**
     * 获取文件完整路径
     * @return string
     */
    private function getFilePath()
    {
        if (empty($this->savePath)) {
            $this->setSavePath($_SERVER['DOCUMENT_ROOT']);
        }

        return $this->savePath . $this->fullName;
    }

    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config['allowFiles']);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function checkSize()
    {
        return $this->fileSize <= ($this->config['maxSize']);
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {

        $url = $this->savePath . $this->fullName;

        if ($this->useWebUrl) {
            if (empty($this->host)) {
                $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
            } else {
                $host = $this->host;
            }

            $host = trim($host, '/');

            if (!preg_match('/^http/', $host)) {
                $host = 'http://' . $host;
            }

            $url = $host . '/' . $url;
        } else {
            $url = '/' . $url;
        }

        return array(
            'state' => $this->stateInfo,
            'url' => $url,
            'title' => $this->fileName,
            'original' => $this->oriName,
            'type' => $this->fileType,
            'size' => $this->fileSize
        );
    }

    /**
     * @param bool $toJson
     * @return array|false|string
     */
    public function config($toJson = false)
    {
        $config = [
            /**
             * 上传图片配置项
             */

            // 执行上传图片的action名称
            'imageActionName' => self::UPLOAD_TYPE_IMAGE,
            // 提交的图片表单名称
            'imageFieldName' => self::UPLOAD_FILENAME_IMAGE,
            // 上传大小限制，单位B
            'imageMaxSize' => 2048000,
            // 上传图片格式显示
            'imageAllowFiles' => [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
                '.bmp'
            ],
            // 是否压缩图片
            'imageCompressEnable' => true,
            // 图片压缩最长边限制
            'imageCompressBorder' => 1600,
            // 插入的图片浮动方式
            'imageInsertAlign' => 'none',
            // 图片访问路径前缀
            'imageUrlPrefix' => '',

            /* 上传保存路径,可以自定义保存路径和文件名格式 */
            /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
            /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
            /* {time} 会替换成时间戳 */
            /* {yyyy} 会替换成四位年份 */
            /* {yy} 会替换成两位年份 */
            /* {mm} 会替换成两位月份 */
            /* {dd} 会替换成两位日期 */
            /* {hh} 会替换成两位小时 */
            /* {ii} 会替换成两位分钟 */
            /* {ss} 会替换成两位秒 */
            /* 非法字符 \ : * ? ' < > | */
            /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */
            'imagePathFormat' => '/ueditor/image/{yyyy}{mm}{dd}/{time}{rand:6}',


            /**
             * 涂鸦图片上传配置项
             */

            // 执行上传涂鸦的action名称
            'scrawlActionName' => 'upload_scrawl',
            // 提交的图片表单名称
            'scrawlFieldName' => 'upload_scrawl',
            // 上传保存路径,可以自定义保存路径和文件名格式
            'scrawlPathFormat' => '/ueditor/image/{yyyy}{mm}{dd}/{time}{rand:6}',
            // 上传大小限制，单位B
            'scrawlMaxSize' => 2048000,
            // 图片访问路径前缀
            'scrawlUrlPrefix' => '',
            // 截图工具上传
            'scrawlInsertAlign' => 'none',
            // 执行上传截图的action名称
            'snapscreenActionName' => 'uploadimage',
            // 上传保存路径,可以自定义保存路径和文件名格式
            'snapscreenPathFormat' => '/ueditor/image/{yyyy}{mm}{dd}/{time}{rand:6}',
            // 图片访问路径前缀
            'snapscreenUrlPrefix' => '',
            // 插入的图片浮动方式
            'snapscreenInsertAlign' => 'none',

            /* 抓取远程图片配置 */
            'catcherLocalDomain' => [
                '127.0.0.1',
                'localhost',
                'img.baidu.com'
            ],
            'catcherActionName' => 'catchimage',
            /* 执行抓取远程图片的action名称 */
            'catcherFieldName' => 'source',
            /* 提交的图片列表表单名称 */
            'catcherPathFormat' => '/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
            /* 上传保存路径,可以自定义保存路径和文件名格式 */
            'catcherUrlPrefix' => '',
            /* 图片访问路径前缀 */
            'catcherMaxSize' => 2048000,
            /* 上传大小限制，单位B */
            'catcherAllowFiles' => [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
                '.bmp'
            ],
            /* 抓取图片格式显示 */

            /* 上传视频配置 */
            'videoActionName' => 'uploadvideo',
            /* 执行上传视频的action名称 */
            'videoFieldName' => 'upfile',
            /* 提交的视频表单名称 */
            'videoPathFormat' => '/ueditor/php/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}',
            /* 上传保存路径,可以自定义保存路径和文件名格式 */
            'videoUrlPrefix' => '',
            /* 视频访问路径前缀 */
            'videoMaxSize' => 102400000,
            /* 上传大小限制，单位B，默认100MB */
            'videoAllowFiles' => [
                '.flv',
                '.swf',
                '.mkv',
                '.avi',
                '.rm',
                '.rmvb',
                '.mpeg',
                '.mpg',
                '.ogg',
                '.ogv',
                '.mov',
                '.wmv',
                '.mp4',
                '.webm',
                '.mp3',
                '.wav',
                '.mid'
            ],
            /* 上传视频格式显示 */

            /* 上传文件配置 */
            'fileActionName' => 'uploadfile',
            /* controller里,执行上传视频的action名称 */
            'fileFieldName' => 'upfile',
            /* 提交的文件表单名称 */
            'filePathFormat' => '/ueditor/php/upload/file/{yyyy}{mm}{dd}/{time}{rand:6}',
            /* 上传保存路径,可以自定义保存路径和文件名格式 */
            'fileUrlPrefix' => '',
            /* 文件访问路径前缀 */
            'fileMaxSize' => 51200000,
            /* 上传大小限制，单位B，默认50MB */
            'fileAllowFiles' => [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
                '.bmp',
                '.flv',
                '.swf',
                '.mkv',
                '.avi',
                '.rm',
                '.rmvb',
                '.mpeg',
                '.mpg',
                '.ogg',
                '.ogv',
                '.mov',
                '.wmv',
                '.mp4',
                '.webm',
                '.mp3',
                '.wav',
                '.mid',
                '.rar',
                '.zip',
                '.tar',
                '.gz',
                '.7z',
                '.bz2',
                '.cab',
                '.iso',
                '.doc',
                '.docx',
                '.xls',
                '.xlsx',
                '.ppt',
                '.pptx',
                '.pdf',
                '.txt',
                '.md',
                '.xml'
            ],
            /* 上传文件格式显示 */

            /* 列出指定目录下的图片 */
            'imageManagerActionName' => 'listimage',
            /* 执行图片管理的action名称 */
            'imageManagerListPath' => '/ueditor/php/upload/image/',
            /* 指定要列出图片的目录 */
            'imageManagerListSize' => 20,
            /* 每次列出文件数量 */
            'imageManagerUrlPrefix' => '',
            /* 图片访问路径前缀 */
            'imageManagerInsertAlign' => 'none',
            /* 插入的图片浮动方式 */
            'imageManagerAllowFiles' => [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
                '.bmp'
            ],
            /* 列出的文件类型 */

            /* 列出指定目录下的文件 */
            'fileManagerActionName' => 'listfile',
            /* 执行文件管理的action名称 */
            'fileManagerListPath' => '/ueditor/php/upload/file/',
            /* 指定要列出文件的目录 */
            'fileManagerUrlPrefix' => '',
            /* 文件访问路径前缀 */
            'fileManagerListSize' => 20,
            /* 每次列出文件数量 */
            'fileManagerAllowFiles' => [
                '.png',
                '.jpg',
                '.jpeg',
                '.gif',
                '.bmp',
                '.flv',
                '.swf',
                '.mkv',
                '.avi',
                '.rm',
                '.rmvb',
                '.mpeg',
                '.mpg',
                '.ogg',
                '.ogv',
                '.mov',
                '.wmv',
                '.mp4',
                '.webm',
                '.mp3',
                '.wav',
                '.mid',
                '.rar',
                '.zip',
                '.tar',
                '.gz',
                '.7z',
                '.bz2',
                '.cab',
                '.iso',
                '.doc',
                '.docx',
                '.xls',
                '.xlsx',
                '.ppt',
                '.pptx',
                '.pdf',
                '.txt',
                '.md',
                '.xml'
            ]
            /* 列出的文件类型 */
        ];

        if ($toJson) {
            return json_encode($config);
        }

        return $config;
    }
}
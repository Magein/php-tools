<?php

namespace magein\php_tools\think;

use think\Request;

/**
 * Class ApiLogin
 * @package magein\php_tools\think
 */
class ApiLogin
{
    /**
     * 头部
     * @var array
     */
    protected $header = array(
        'alg' => 'HS256', //生成signature的算法
        'typ' => 'JWT'  //类型
    );

    /**
     * 使用HMAC生成信息摘要时所使用的密钥
     * @var string
     */
    protected $key = 'Np9SPsaWGMCw';

    /**
     * @return array
     */
    protected function getHeader()
    {
        return $this->header;
    }

    /**
     * @return string
     */
    protected function getKey()
    {
        return $this->key;
    }

    /**
     * 公共的荷载参数
     * [
     * 'iss'=>'jwt_admin', //该JWT的签发者
     * 'iat'=>time(), //签发时间
     * 'exp'=>time()+7200, //过期时间
     * 'nbf'=>time()+60, //该时间之前不接收处理该Token
     * 'sub'=>'www.admin.com', //面向的用户
     * 'jti'=>md5(uniqid('JWT').time()) //该Token唯一标识
     * ]
     * @return bool|string
     */
    protected function getPayload()
    {
        return [];
    }

    /**
     * 获取jwt token
     * @return bool|string
     */
    public function getToken($data)
    {
        $payload = array_merge($this->getPayload(), $data);

        if (is_array($payload)) {
            $base64_header = $this->base64UrlEncode(json_encode($this->getHeader(), JSON_UNESCAPED_UNICODE));
            $base64_payload = $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_UNICODE));
            $token = $base64_header . '.' . $base64_payload . '.' . $this->signature($base64_header . '.' . $base64_payload, $this->getKey(), $this->getHeader()['alg']);
            return $token;
        } else {
            return false;
        }
    }

    /**
     * 验证token是否有效,默认验证exp,nbf,iat时间
     * @param string $token 需要验证的token
     * @return bool|string
     */
    public function verifyToken(string $token = '')
    {
        if (empty($token)) {
            $token = Request::instance()->header('Authorization');
        }

        $tokens = explode('.', $token);

        if (count($tokens) != 3) {
            return false;
        }

        list($base64_header, $base64_payload, $sign) = $tokens;

        //获取jwt算法
        $base64_decode_header = json_decode($this->base64UrlDecode($base64_header), JSON_OBJECT_AS_ARRAY);
        if (empty($base64_decode_header['alg'])) {
            return false;
        }

        //签名验证
        if ($this->signature($base64_header . '.' . $base64_payload, $this->getKey(), $base64_decode_header['alg']) !== $sign) {
            return false;
        }

        $payload = json_decode($this->base64UrlDecode($base64_payload), JSON_OBJECT_AS_ARRAY);

        //签发时间大于当前服务器时间验证失败
        if (isset($payload['iat']) && $payload['iat'] > time()) {
            return false;
        }

        //过期时间小宇当前服务器时间验证失败
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }

        //该nbf时间之前不接收处理该Token
        if (isset($payload['nbf']) && $payload['nbf'] > time()) {
            return false;
        }

        return $payload;
    }


    /**
     * base64UrlEncode  https://jwt.io/ 中base64UrlEncode编码实现
     * @param string $input 需要编码的字符串
     * @return string
     */
    private function base64UrlEncode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * base64UrlEncode https://jwt.io/ 中base64UrlEncode解码实现
     * @param string $input 需要解码的字符串
     * @return bool|string
     */
    private function base64UrlDecode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $add_length = 4 - $remainder;
            $input .= str_repeat('=', $add_length);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * HMACSHA256签名  https://jwt.io/ 中HMACSHA256签名实现
     * @param string $input 为base64UrlEncode(header).".".base64UrlEncode(payload)
     * @param string $key
     * @param string $alg 算法方式
     * @return mixed
     */
    private function signature(string $input, string $key, string $alg = 'HS256')
    {
        $alg_config = array(
            'HS256' => 'sha256'
        );
        return $this->base64UrlEncode(hash_hmac($alg_config[$alg], $input, $key, true));
    }

    /**
     * 登录用户的ID
     * @return string
     */
    public static function id()
    {
        return defined('LOGIN_USER_ID') ? LOGIN_USER_ID : '';
    }
}
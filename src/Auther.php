<?php
namespace cncoders\auth;

use Firebase\JWT\JWT;
use think\facade\Config;
use think\facade\Request;
use think\model\concern\TimeStamp;

class Auther
{
    /**
     * @var string JWT 签名SECRET
     */
    protected $jwt_secret = '';

    /**
     * @var int JWT 过期时间
     */
    protected $jwt_ttl = 60;

    /**
     * @var string
     */
    protected $jwt_alg = 'HS256';

    /**
     * 设置额外的参数
     * @var null
     */
    protected $headers = [];

    //存储的数据
    protected $tokenData = null;

    protected static $instance = null;

    public function __construct()
    {
        $secret     = Config::get('jwt.jwt_secret');
        $jwt_ttl    = Config::get('jwt.jwt_ttl');
        $jwt_alg    = Config::get('jwt.jwt_alg');

        if ( empty($secret) ) {
            throw new AutherException('The jwt_secret is Empty!');
        }

        $this->jwt_secret   = $secret;
        $this->jwt_ttl   = $jwt_ttl;

        if ( !empty($jwt_alg) ) {
            $this->jwt_alg   =  $jwt_alg;
        }

    }

    /**
     * 单例模式运行
     * @return Auther|null
     * @throws \Exception
     */
    public static function make()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置额外的加密参数
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders( array $headers = [] )
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * 获取TOKEN
     * @param array $data
     * @return string
     */
    public function token(array $data = array())
    {
        $hmc_data = [];
        $hmc_data['timestamp']    = time();
        $hmc_data['verifyTime']   = time() + $this->jwt_ttl;
        $hmc_data['alg']          = $this->jwt_alg;
        $hmc_data['data']         = $data;
        return JWT::encode($hmc_data, $this->jwt_secret, $this->jwt_alg, NULL, $this->headers);
    }

    /**
     * 校验TOKEN
     * @param $token
     * @return array
     * @throws \Exception
     */
    public function verfiyToken()
    {
        $token = $this->getToken();

        if (empty($token) ) {
            throw new AutherException('The token is Empty!', 401);
        }

        $decodeData = $this->JwtDecodeData($token);
        if (!isset($decodeData->timestamp) || !isset( $decodeData->verifyTime ) ) {
            throw new AutherException('Token is Invaild!',401);
        }

        if (time() > $decodeData->verifyTime) {
            throw new AutherException('The token is Expired!', 401);
        }

        $this->tokenData = $decodeData;
        return true;
    }

    /**
     * 解密TOKEN
     *
     * @param $token
     * @return object
     */
    private function JwtDecodeData($token)
    {
        if ( empty($token) ) {
            throw new AutherException('TOKEN is empty!', 401);
        }
        return JWT::decode($token, $this->jwt_secret,['HS256','HS384','HS512','RS256','RS384','RS512']);
    }

    /**
     * 获取传递的header数据
     *
     * @return object
     */
    public function header()
    {
        $jwt = $this->getToken();
        $tks = explode('.', $jwt);
        list($headb64, $bodyb64, $cryptob64) = $tks;
        $header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64));
        return $header;
    }

    /**
     * 刷新TOKEN
     * @return string
     */
    public function refreshToken()
    {
        $decodeData = $this->JwtDecodeData( $this->getToken() );

        if (time() - $decodeData->verifyTime > Config::get('jwt.jwt_allow_ttl')) {
            throw new AutherException('已经超过最大刷新时间', -10002);
        }

        $this->setHeaders( $this->objectToArray( $this->header() ) );
        return $this->token( $this->objectToArray( $decodeData->data ) );
    }

    /**
     * 对象转数组
     *
     * @param $data
     * @return mixed
     */
    public function objectToArray($data)
    {
        return json_decode( json_encode($data) , true );
    }

    /**
     * 获取TOKEN
     *
     * @return mixed
     */
    public function getToken()
    {
        $request = Request::instance();
        if ( Config::get('jwt.jwt_request_type') === 'header')
            return $request->header( strtolower( Config::get('jwt.jwt_request_field') ) );
        else
            return $request->param( Config::get('jwt.jwt_request_field') );
    }

    /**
     * 获取加密的内容
     *
     * @return null
     */
    public function decodeData()
    {
        return isset( $this->tokenData->data ) ? $this->tokenData->data : null;
    }

    /**
     * 获取所有的加密数据
     *
     * @return null
     */
    public function all()
    {
        return $this->tokenData;
    }
}
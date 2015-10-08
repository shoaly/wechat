<?php
/**
 * AccessToken.php
 *
 * Part of Overtrue\Wechat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015 overtrue <i@overtrue.me>
 * @link      https://github.com/overtrue
 * @link      http://overtrue.me
 */

namespace Overtrue\Wechat;
use Illuminate\Support\Facades\Cache as SystemCache;

/**
 * 全局通用 AccessToken
 */
class AccessToken
{

    /**
     * 应用ID
     *
     * @var string
     */
    protected $appId;

    /**
     * 应用secret
     *
     * @var string
     */
    protected $appSecret;

    /**
     * 缓存类
     *
     * @var Cache
     */
    protected $cache;

    /**
     * token
     *
     * @var string
     */
    protected $token;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $cacheKey = 'overtrue.wechat.access_token';

    // API
    const API_TOKEN_GET = 'https://api.weixin.qq.com/cgi-bin/token';

    /**
     * constructor
     *
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId     = $appId;
        $this->appSecret = $appSecret;
        $this->cache     = new Cache($appId);
    }

    /**
     * 缓存 setter
     *
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * 获取Token
     *
     * @return string
     */
    public function getToken()
    {
         //如果是测试 那么, 每次都会获取新的 accessToken
        
        $cacheKey    = $this->cacheKey;
        // for php 5.3
        $appId       = $this->appId;
        $appSecret   = $this->appSecret;
         
        $apiTokenGet = self::API_TOKEN_GET;
        
        $params = array(
                   'appid'      => $appId,
                   'secret'     => $appSecret,
                   'grant_type' => 'client_credential',
                  );

        $token = SystemCache::store('database')->remember($cacheKey,60,function() use($cacheKey,$params,$apiTokenGet){
            $http = new Http();
            $token = $http->get($apiTokenGet, $params);

            return $token['access_token'];
        });

        // SystemCache::store('database')->put('foo','bar',30);
        // dd(SystemCache::store('database')->get('foo'));
        return $token;
        
        

         
        
    }
}

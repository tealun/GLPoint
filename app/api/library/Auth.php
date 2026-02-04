<?php
namespace app\api\library;

use think\facade\Log;
use think\facade\Config;
use thans\jwt\facade\JWTAuth;

class Auth 
{
    /**
     * 生成Token
     */
    public static function createToken(array $data): string
    {
        try {
            // 生成Token
            return JWTAuth::builder(array_merge($data, [
                'iss' => Config::get('jwt.iss', 'api.yuanyin.com'),  // 发行人
                'aud' => Config::get('jwt.aud', 'yuanyin.com'),      // 接收人
                'iat' => time(),                                      // 签发时间
                'exp' => time() + Config::get('jwt.ttl', 7200),      // 过期时间
                'nbf' => time()                                       // 生效时间
            ]));
        } catch(\Exception $e) {
            Log::error("[Auth] 生成Token失败: " . $e->getMessage());
            throw new \Exception('Token生成失败');
        }
    }

    /**
     * 验证Token
     * @param string|null $token 要验证的token
     * @param bool $force 是否强制验证
     * @return array|null
     */
    public static function verifyToken(?string $token = null, bool $force = true): ?array
    {
        try {
            // 如果未传入token,则从请求中获取
            if(!$token) {
                $token = self::getTokenFromRequest();
            }
            
            // 记录要验证的token
            Log::debug("[Auth] 验证token: " . substr($token, 0, 20) . '...');
            
            // 检查是否有token
            if(!$token) {
                Log::debug("[Auth] Token不存在");
                if($force) {
                    throw new \Exception('请先登录');
                }
                return null;
            }

            // 验证Token
            try {
                // 记录JWT配置信息
                $jwtConfig = [
                    'secret' => substr(Config::get('jwt.secret'), 0, 10) . '...',
                    'signer' => Config::get('jwt.signer'),
                    'ttl' => Config::get('jwt.ttl')
                ];
                Log::debug("[Auth] JWT配置: " . json_encode($jwtConfig, JSON_UNESCAPED_UNICODE));
                
                $payload = JWTAuth::auth($token); 
                
                if(!$payload) {
                    throw new \Exception('Token验证失败');
                }
                
                // 记录验证通过的payload
                Log::debug("[Auth] Token验证成功,payload:", (array)$payload);
                
                return (array)$payload;
                
            } catch(\Exception $e) {
                Log::error("[Auth] JWT验证异常: " . $e->getMessage());
                throw $e;
            }

        } catch(\Exception $e) {
            Log::error("[Auth] Token验证失败: " . $e->getMessage());
            if($force) {
                throw new \Exception($e->getMessage());
            }
            return null;
        }
    }

    /**
     * 从请求获取Token
     */
    public static function getTokenFromRequest(): ?string 
    {
        $request = request();
        
        // 从Header获取
        $token = $request->header('Authorization');
        
        // 从GET参数获取
        if(!$token) {
            $token = $request->param('token');
        }

        // Bearer Token处理
        if($token && stripos($token, 'Bearer ') === 0) {
            $token = trim(substr($token, 7));
        }

        if($token) {
            Log::debug("[Auth] 获取到Token: " . substr($token, 0, 10) . '...');
        } else {
            Log::debug("[Auth] 未找到Token");
        }

        return $token ?: null;
    }


    /**
     * 检查登录状态
     * @return bool
     */
    public static function checkLogin(): bool
    {
        try {            
            // 验证Token并获取用户信息
            Log::info('===== 开始获取用户信息 =====');
            // 获取Token
            $token = self::getTokenFromRequest();
            Log::debug('获取到Token: ' . $token);
            Log::debug('Authorization Header: ' . request()->header('Authorization'));

            if (!$token) {
                Log::warning('Token获取失败,请求头信息:', [
                    'headers' => request()->header(),
                    'request_method' => request()->method()
                ]);
                return false;
            }

            // 验证Token
            Log::debug('开始验证Token...');
            $authCheck = self::verifyToken($token);
            Log::debug('Token验证结果:', [
                'result' => $authCheck,
                'token' => $token
            ]);

            if (!$authCheck) {
                Log::warning('Token验证失败:', [
                    'token' => $token,
                    'verify_result' => $authCheck
                ]);
                return false;
            }
            return true;

        } catch(\Exception $e) {
            Log::error('checkLogin error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 退出登录
     * @return bool
     */
    public static function logout(): bool
    {
        try {
            // 获取当前token
            $token = self::getTokenFromRequest();
            
            if(!$token) {
                return true;
            }

            // 加入黑名单
            JWTAuth::invalidate($token);
            
            // 记录日志
            Log::info('[Auth] 用户退出登录成功', [
                'token' => substr($token, 0, 10) . '...'
            ]);

            return true;

        } catch(\Exception $e) {
            Log::error('[Auth] 退出登录失败: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 从token中提取user_id
     * @param string|null $token
     * @return int|null
     */
    public static function getUserIdFromToken(?string $token = null): ?int
    {
        try {
            if (!$token) {
                $token = self::getTokenFromRequest();
            }
            if (!$token) {
                return null;
            }
            $payload = JWTAuth::auth($token);
            if (!$payload || !isset($payload['uid'])) {
                return null;
            }
            $uid = $payload['uid'];
            // 兼容 Lcobucci\JWT\Claim\Basic 对象
            if (is_object($uid) && method_exists($uid, 'getValue')) {
                $uid = $uid->getValue();
            }
            return is_numeric($uid) ? (int)$uid : null;
        } catch (\Exception $e) {
            Log::error('[Auth] 从Token提取user_id失败: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * 获取当前登录用户的完整信息
     * @param string|null $token
     * @return array|null
     */
    public static function getUser(?string $token = null): ?array
    {
        try {
            // 获取用户ID
            $userId = self::getUserIdFromToken($token);
            if (!$userId) {
                Log::warning('[Auth] 无法从Token获取用户ID');
                return null;
            }

            // 调用User控制器的getUser方法获取完整用户信息
            $userInfo = \app\api\controller\User::getUser($userId);
            
            if (!$userInfo) {
                Log::warning('[Auth] 用户不存在或未激活: ' . $userId);
                return null;
            }

            return $userInfo;

        } catch (\Exception $e) {
            Log::error('[Auth] 获取用户信息失败: ' . $e->getMessage());
            return null;
        }
    }

}

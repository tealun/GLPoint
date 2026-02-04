<?php
declare (strict_types=1);

namespace woo\common\middleware;

use woo\common\helper\Str;

class Before
{
    public function handle($request, \Closure $next)
    {
        if (is_woo_installed()) {
            if ($apps = get_app()) {
                foreach ($apps as $item) {
                    if (!is_file(woo_path() . $item['name'] . DIRECTORY_SEPARATOR . 'helper.php')) {
                        continue;
                    }
                    include_once woo_path() . $item['name'] . DIRECTORY_SEPARATOR . 'helper.php';
                }
            }
        }
        // 全局RSA解密
        if (
            $request->isPost()
            && $request->post('RSA_FIELD_LIST')
            && extension_loaded('openssl')
        ) {
            $post = $request->post();
            $rsa = !is_array($post['RSA_FIELD_LIST'])? explode(',', $post['RSA_FIELD_LIST']): $post['RSA_FIELD_LIST'];
            foreach ($rsa as $field) {
                if (empty($post[$field])) {
                    continue;
                }
                $post[$field] = Str::setDecrypt($post[$field]);
            }
            unset($post['RSA_FIELD_LIST']);
            $request->withPost($post);
        }

        return $next($request);
    }
}
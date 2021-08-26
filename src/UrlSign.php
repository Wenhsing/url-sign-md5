<?php

namespace Wenhsing\UrlSign;

use Carbon\Carbon;

class UrlSign
{
    // 排除
    protected $except = [];

    // 时间误差
    protected $timeError = 300;

    // 密钥
    protected $secretKey = '';

    // 时间
    protected $tsField = 'timestamp';

    // 签名字段
    protected $signField = 'sign';

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function verify(string $uri, array $datas)
    {
        if (
            $this->inExceptArray($uri)
            || ($this->allowTimestamp($datas[$this->tsField] ?? 0) && $this->signMatch($datas))
        ) {
            return true;
        }

        return false;
    }

    /**
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @param string $uri
     *
     * @return bool
     */
    public function inExceptArray($uri)
    {
        $except = array_map(function ($i) {
            if ('/' !== $i) {
                return trim($i, '/');
            }
            return $i;
        }, $this->except);
        if ($this->strMatch($except, $uri)) {
            return true;
        }

        return false;
    }

    /**
     * 判断用户请求是否在对应时间范围.
     *
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @param int $timestamp
     *
     * @return bool
     */
    public function allowTimestamp($timestamp)
    {
        $queryTime = Carbon::createFromTimestamp($timestamp);
        $lfTime = Carbon::now()->subSeconds($this->timeError);
        $rfTime = Carbon::now()->addSeconds($this->timeError);
        if ($queryTime->between($lfTime, $rfTime, true)) {
            return true;
        }

        return false;
    }

    /**
     * 签名验证
     *
     * @author Wenhsing <wenhsing@qq.com>
     *
     * @return bool
     */
    public function signMatch(array $data, callable $custom = null)
    {
        if (null !== $custom) {
            return $custom($data);
        }
        ksort($data);
        $sign = '';
        foreach ($data as $k => $v) {
            if ($this->signField !== $k) {
                $sign .= $k.$v;
            }
        }
        if (md5($sign.$this->secretKey) === $data[$this->signField] ?? null) {
            return true;
        }

        return false;
    }

    protected function strMatch(array $datas, $value)
    {
        foreach ($datas as $v) {
            if ($v == $value) {
                return true;
            }
            $v = preg_quote($v, '#');
            $v = str_replace('\*', '.*', $v);
            if (preg_match('#^'.$v.'\z#u', $value) > 0) {
                return true;
            }
        }

        return false;
    }

    public function setConfig(array $config)
    {
        if (isset($config['except'])) {
            $this->setExcept($config['except']);
        }
        if (isset($config['time_error'])) {
            $this->setTimeError($config['time_error']);
        }
        if (isset($config['secret_key'])) {
            $this->setSecretKey($config['secret_key']);
        }
        if (isset($config['ts_field'])) {
            $this->setTsField($config['ts_field']);
        }
        if (isset($config['sign_field'])) {
            $this->setSignField($config['sign_field']);
        }
        return $this;
    }

    public function setExcept(array $except)
    {
        $this->except = array_merge($this->except, $except);
        return $this;
    }

    public function setTimeError(int $t)
    {
        if ($t > 0) {
            $this->timeError = $t;
        }
        return $this;
    }

    public function setSecretKey(string $secretKey)
    {
        $this->secretKey = $secretKey;
        return $this;
    }

    public function setTsField(string $tsField)
    {
        $this->tsField = $tsField;
        return $this;
    }

    public function setSignField(string $signField)
    {
        $this->signField = $signField;
        return $this;
    }
}

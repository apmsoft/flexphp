<?php
namespace Flex\Annona\Http;

class HttpUrlFilter
{
    public const __version = '0.5';
    public function __construct(
        private string $url
    )
    {}

    # http|https 가 있는지 확인 후 glue 붙이기
    public function httpPrefix(string $glue='http') : HttpUrlFilter
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $this->url)) {
            $this->url = $glue . '://' .$this->url;
        }
        return $this;
    }

    public function wwwPrefix() : HttpUrlFilter
    {
        // http(s)://가 있지만 www가 없는 경우
        if (preg_match("/^https?:\/\/(?!www\.)/i", $this->url)) {
            $this->url = preg_replace("/^https?:\/\//i", "$0www.", $this->url);
        }
        // htts(s)가 없이 ://만 있고 www가 없는 경우
        else if (preg_match("/^:\/\/(?!www\.)/i", $this->url)) {
                $this->url = preg_replace("/^:\/\//i", "$0www.", $this->url);
        }
        else if (!preg_match("/^www\./i", $this->url)) {
            $this->url = "www." . $this->url;
        }

        return $this;
    }

    # 가져오기
	public function __get(string $propertyName) : mixed{
		return $this->url;
	}
}
?>
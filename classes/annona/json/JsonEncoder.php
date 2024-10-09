<?php
namespace Flex\Annona\Json;

final class JsonEncoder
{
	public const __version = '0.1.1';

	# 배열을 json string utf8
	final public static function toJson(array $data, array $except_numberic_keys = [], int $options=JSON_UNESCAPED_UNICODE) : string
	{
		$data = JsonEncoder::applyNumericExceptions($data, $except_numberic_keys);
        return json_encode($data, $options);

	}

	# 특정키를 제외한 numberic으로 변형하기
	private static function applyNumericExceptions(array $data, array $exceptNumericKeys) : array
    {
        foreach ($data as $key => &$value) {
            if (is_array($value)) {
                $value = JsonEncoder::applyNumericExceptions($value, $exceptNumericKeys);
            } elseif (is_numeric($value) && in_array($key, $exceptNumericKeys)) {
                $value = (string) $value;
            } elseif (is_numeric($value)) {
                $value = $value + 0; // 숫자 형 변환
            }
        }
        return $data;
    }

}
?>

<?php
declare(strict_types=1);

namespace Assertis\Util;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class LinkApplicator
{
    private const URI_KEY = 'uri';

    /**
     * @param array $data
     * @param array $links
     * @param array $applied
     * @return array
     */
    public function apply(array $data, array $links, array $applied = []): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->apply($value, $links, $applied);
            } elseif (is_string($value) &&
                array_key_exists($value, $links) &&
                !array_key_exists($value, $applied) &&
                $key !== self::URI_KEY
            ) {
                if (is_array($links[$value])) {
                    $newValue = $this->apply($links[$value], $links, $applied + [$value => true]);
                } else {
                    $newValue = $links[$value];
                }

                $data[$key] = [self::URI_KEY => $value] + $newValue;
            }
        }

        return $data;
    }
}

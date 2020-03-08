<?php

namespace app\modules\page\models;

abstract class AbstractStatus
{
    const STATUS_GUEST = 'guest';
    const STATUS_USER  = 'user';
    const STATUS_ADMIN = 'admin';
    const STATUS_LINK  = 'link';
    const LIST_STATUS  = [self::STATUS_GUEST, self::STATUS_USER, self::STATUS_ADMIN, self::STATUS_LINK];

    /**
     * @return array
     */
    public static function getStatusList(): array
    {
        $list = [];

        foreach (AbstractStatus::LIST_STATUS as $status) {
            $list[$status] = $status;
        }

        return $list;
    }
}
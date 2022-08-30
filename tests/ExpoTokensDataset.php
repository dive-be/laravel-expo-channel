<?php declare(strict_types=1);

namespace Tests;

trait ExpoTokensDataset
{
    public function invalid(): array
    {
        return [
            ['exponentpushtoken[FtT1dBIc5Wp92HEGuJUhL4]'],
            ['ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4'],
            ['ExponentPushToken-FtT1dBIc5Wp92HEGuJUhL4'],
            ['ExpoPushToken[FtT1dBIc5Wp92HEGuJUhL4'],
            ['FtT1dBIc5Wp92HEGuJUhL4'],
            ['ExpoPushToken[]'],
        ];
    }

    public function valid(): array
    {
        return [
            ['ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]'],
            ['ExpoPushToken[FtT1dBIc5Wp92HEGuJUhL4]'],
        ];
    }
}

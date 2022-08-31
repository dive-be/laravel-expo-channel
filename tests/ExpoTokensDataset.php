<?php declare(strict_types=1);

namespace Tests;

trait ExpoTokensDataset
{
    protected function invalid(): array
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

    protected function valid(): array
    {
        return [
            ['ExponentPushToken[FtT1dBIc5Wp92HEGuJUhL4]'],
            ['ExpoPushToken[FtT1dBIc5Wp92HEGuJUhL4]'],
        ];
    }
}

<?php

declare(strict_types=1);

set_error_handler(
    static function (int $severity, string $message, string $file, int $line): bool {
        throw new ErrorException($message, 0, $severity, $file, $line);
    },
    -1,
);

const ROOT_DIR = __DIR__ . '/../..';
const DATA_FILE = ROOT_DIR . '/data/data.json';

#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class DebianVersionData
{
    public function __construct(
        public readonly int $id,
        public readonly string $codename,
    ) {}
}

enum DebianVersion
{
    #[DebianVersionData(8, 'jessie')]
    case Jessie;
    #[DebianVersionData(9, 'stretch')]
    case Stretch;
    #[DebianVersionData(10, 'buster')]
    case Buster;
    #[DebianVersionData(11, 'bullseye')]
    case Bullseye;
    #[DebianVersionData(12, 'bookworm')]
    case Bookworm;
    #[DebianVersionData(13, 'trixie')]
    case Trixie;
    #[DebianVersionData(14, 'forky')]
    case Forky;
    #[DebianVersionData(15, 'duke')]
    case Duke;

    public static function getData(self $case): DebianVersionData
    {
        static $cache = [];
        if (!isset($cache[$case->name])) {
            $reflection = new ReflectionEnumUnitCase($case::class, $case->name);
            $cache[$case->name] = $reflection->getAttributes('DebianVersionData')[0]->newInstance();
        }
        return $cache[$case->name];
    }

    public static function fromCodename(string $codename): ?self
    {
        foreach (self::cases() as $case) {
            $data = self::getData($case);
            if ($data->codename === $codename) {
                return $case;
            }
        }
        return null;
    }
}

abstract class PHPImage
{
    public readonly string $phpMajorMinorVersion;
    protected function __construct(
        public readonly string $phpVersion,
        public readonly bool $isRC,
        public readonly DateTimeImmutable $lastUpdated,
        public readonly string $os,
        public bool $isDefaultForMajorMinor,
    ) {
        $parts = explode('.', $this->phpVersion);
        $this->phpMajorMinorVersion = $parts[0] . '.' . $parts[1];
    }

    public static function fromArray(array $data): ?self
    {
        static $rxOSVersion;
        $matches = null;
        if (!preg_match('#^(?<phpVersion>\d+\.\d+(\.\d+|-rc))-cli#', $data['name'], $matches)) {
            return null;
        }
        $lastUpdated = new DateTimeImmutable($data['last_updated']);
        $phpVersion = $matches['phpVersion'];
        if (str_ends_with($phpVersion, '-rc')) {
            $phpVersion = substr($phpVersion, 0, -3) . '.0';
            $isRC = true;
        } else {
            $isRC = false;
        }
        if (version_compare($phpVersion, '5.5') <= 0) {
            return new DebianPHPImage(
                $phpVersion,
                $isRC,
                $lastUpdated,
                DebianVersion::Jessie,
                true,
            );
        }
        if ($rxOSVersion === null) {
            $debianCodenames = array_map(
                static fn(DebianVersion $version): string => DebianVersion::getData($version)->codename,
                DebianVersion::cases(),
            );
            $rxDebian = '-(?<debianVersion>' . implode('|', array_map(
                static fn(string $codename): string => preg_quote($codename, '#'),
                $debianCodenames,
            )) . ')';
            $rxAlpine = '-alpine(?<alpineVersion>[\d\.]+)';
            $rxOSVersion = '#(' . $rxDebian . '|' . $rxAlpine . ')#';
        }
        if (!preg_match($rxOSVersion, $data['name'], $matches)) {
            return null;
        }
        if (!empty($matches['alpineVersion'])) {
            return new AlpinePHPImage(
                $phpVersion,
                $isRC,
                $lastUpdated,
                $matches['alpineVersion'],
                false,
            );
        } elseif (!empty($matches['debianVersion'])) {
            return new DebianPHPImage(
                $phpVersion,
                $isRC,
                $lastUpdated,
                DebianVersion::fromCodename($matches['debianVersion']),
                false,
            );
        }
    }
}

class DebianPHPImage extends PHPImage
{
    public function __construct(
        string $phpVersion,
        bool $isRC,
        DateTimeImmutable $lastUpdated,
        public readonly DebianVersion $version,
        bool $isDefaultForMajorMinor,
    ) {
        parent::__construct(
            $phpVersion,
            $isRC,
            $lastUpdated,
            DebianVersion::getData($version)->codename,
            $isDefaultForMajorMinor,
        );
    }
    public function __toString(): string
    {
        return $this->phpMajorMinorVersion . ($this->isRC ? '-rc' : '') . '-' . DebianVersion::getData($this->version)->codename;
    }
}


class AlpinePHPImage extends PHPImage
{
    public function __construct(
        string $phpVersion,
        bool $isRC,
        DateTimeImmutable $lastUpdated,
        /** Alpine version */
        public readonly string $version,
        bool $isDefaultForMajorMinor,
    ) {
        parent::__construct(
            $phpVersion,
            $isRC,
            $lastUpdated,
            "alpine{$version}",
            $isDefaultForMajorMinor,
        );
    }
    public function __toString(): string
    {
        return $this->phpMajorMinorVersion . ($this->isRC ? '-rc' : '') . '-alpine' . $this->version;
    }
}


class PHPImageList implements JsonSerializable
{
    /**
     * @var PHPImage[]
     */
    private $phpImages = [];

    public function __construct(
        public DateTimeImmutable $lastUpdated,
    ) {}

    public function add(PHPImage $phpImage): void
    {
        foreach ($this->phpImages as $index => $existingImage) {
            if (
                $existingImage->phpMajorMinorVersion === $phpImage->phpMajorMinorVersion
                && $existingImage->isRC === $phpImage->isRC
                && $existingImage->os === $phpImage->os
            ) {
                if (version_compare($phpImage->phpVersion, $existingImage->phpVersion) > 0) {
                    $this->phpImages[$index] = $phpImage;
                }
                return;
            }
        }
        $this->phpImages[] = $phpImage;
    }

    /**
     * @return PHPImage[]
     */
    public function getImages(): array
    {
        return $this->phpImages;
    }

    public function jsonSerialize(): array
    {
        $imagesByMajorMinorVersion = [];
        foreach ($this->phpImages as $phpImage) {
            $phpMajorMinorVersion = $phpImage->phpMajorMinorVersion;
            if (!isset($imagesByMajorMinorVersion[$phpMajorMinorVersion])) {
                $imagesByMajorMinorVersion[$phpMajorMinorVersion] = [
                    'rc' => [],
                    'ga' => [],
                ];
            }
            $channel = $phpImage->isRC ? 'rc' : 'ga';
            $imagesByMajorMinorVersion[$phpMajorMinorVersion][$channel][] = [
                'os' => $phpImage->os,
                'phpVersion' => $phpImage->phpVersion,
                'default' => $phpImage->isDefaultForMajorMinor,
            ];
        }
        $images = [];
        foreach ($imagesByMajorMinorVersion as $phpMajorMinorVersion => $list) {
            $group = [
                'isRC' => $list['ga'] === [],
                'maxPHPVersion' => null,
                'os' => [],
            ];
            $channel = $list['ga'] === [] ? 'rc' : 'ga';
            foreach ($list[$channel] as $item) {
                if ($group['maxPHPVersion'] === null || version_compare($item['phpVersion'], $group['maxPHPVersion']) > 0) {
                    $group['maxPHPVersion'] = $item['phpVersion'];
                }
                $serializedItem = [
                    'id' => $item['os'],
                    'phpVersion' => $item['phpVersion'],
                ];
                if ($item['default']) {
                    $serializedItem['default'] = true;
                }
                $group['os'][] = $serializedItem;
            }
            foreach ($group['os'] as &$osItem) {
                if ($osItem['phpVersion'] === $group['maxPHPVersion']) {
                    unset($osItem['phpVersion']);
                }
            }
            $images[$phpMajorMinorVersion] = $group;
        }

        return [
            'last-updated' => $this->lastUpdated->format(DateTime::ATOM),
            'images' => $images,
        ];
    }

    private static function createEmpty(): self
    {
        return new self(new DateTimeImmutable('@0'));
    }

    private static function fromJSON(string $json): self
    {
        $data = json_decode($json, true, JSON_THROW_ON_ERROR);
        $instance = new self(new DateTimeImmutable($data['last-updated']));
        foreach ($data['images'] as $versionInfo) {
            $isRC = $versionInfo['isRC'];
            $maxPHPVersion = $versionInfo['maxPHPVersion'];
            foreach ($versionInfo['os'] as $osInfo) {
                $phpVersion = $osInfo['phpVersion'] ?? $maxPHPVersion;
                $os = $osInfo['id'];
                if (str_starts_with($os, 'alpine')) {
                    $instance->add(new AlpinePHPImage(
                        $phpVersion,
                        $isRC,
                        $instance->lastUpdated,
                        substr($os, strlen('alpine')),
                        $osInfo['default'] ?? false,
                    ));
                } else {
                    $instance->add(new DebianPHPImage(
                        $phpVersion,
                        $isRC,
                        $instance->lastUpdated,
                        DebianVersion::fromCodename($os),
                        $osInfo['default'] ?? false,
                    ));
                }
            }
        }
        return $instance;
    }

    public static function loadFromDataFile(): self
    {
        if (!file_exists(DATA_FILE)) {
            return self::createEmpty();
        }
        $json = file_get_contents(DATA_FILE);
        return self::fromJSON($json);
    }

    public function saveToDataFile(): void
    {
        $data = $this->jsonSerialize();
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents(DATA_FILE, $json);
    }

    public function setDefaultDebianVersion(string $phpMajorMinorVersion, int $debianVersion): void
    {
        $found = false;
        foreach ($this->phpImages as $image) {
            if (
                !$image instanceof DebianPHPImage
                || $image->phpMajorMinorVersion !== $phpMajorMinorVersion
            ) {
                continue;
            }
            $versionInfo = DebianVersion::getData($image->version);
            if ($versionInfo->id === $debianVersion) {
                $found = true;
                $image->isDefaultForMajorMinor = true;
            } else {
                $image->isDefaultForMajorMinor = false;
            }
        }
        if (!$found) {
            throw new RuntimeException("Could not find Debian image for PHP {$phpMajorMinorVersion} with Debian version ID {$debianVersion}");
        }
    }

    public function setDefaultAlpineVersion(string $phpMajorMinorVersion, string $alpineVersion): void
    {
        $found = false;
        foreach ($this->phpImages as $image) {
            if (
                !$image instanceof AlpinePHPImage
                || $image->phpMajorMinorVersion !== $phpMajorMinorVersion
            ) {
                continue;
            }
            if ($image->version === $alpineVersion) {
                $found = true;
                $image->isDefaultForMajorMinor = true;
            } else {
                $image->isDefaultForMajorMinor = false;
            }
        }
        if (!$found) {
            throw new RuntimeException("Could not find Alpine image for PHP {$phpMajorMinorVersion} with Alpine version {$alpineVersion}");
        }
    }
}

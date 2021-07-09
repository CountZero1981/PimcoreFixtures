<?php
namespace FixtureBundle\Service;

use FixtureBundle\Alice\Providers\Assets;
use FixtureBundle\Alice\Persister\PimcorePersister;
use FixtureBundle\Alice\Processor\DocumentProperties;
use FixtureBundle\Alice\Processor\UserProcessor;
use FixtureBundle\Alice\Processor\WorkspaceProcessor;
use FixtureBundle\Alice\Providers\ObjectReference;
use Nelmio\Alice\Loader\NativeLoader;
use Pimcore\File;

class FixtureLoader
{

    const FIXTURE_FOLDER = PIMCORE_PRIVATE_VAR. '/bundles/FixtureBundle/fixtures';
    const IMAGES_FOLDER  = PIMCORE_PRIVATE_VAR . '/bundles/FixtureBundle/images';

    private static $objects = [];
    /**
     * @var bool
     */
    private $omitValidation;
    /**
     * @var bool
     */
    private $checkPathExists;
    /**
     * @var Assets
     */
    private $assetsProvider;
    /**
     * @var ObjectReference
     */
    private $objectReferenceProvider;

    /**
     * FixtureLoader constructor.
     * @param bool $checkPathExists
     * @param bool $omitValidation
     */
    public function __construct($checkPathExists, $omitValidation) {
        $this->omitValidation = $omitValidation;
        $this->checkPathExists = $checkPathExists;
    }

    /**
     * @required
     * @param Assets $assetsProvider
     */
    public function setAssetsProvider(Assets $assetsProvider)
    {
        $this->assetsProvider = $assetsProvider;
        $this->assetsProvider->setAssetsPath(self::IMAGES_FOLDER);
    }

    /**
     * @required
     * @param ObjectReference $objectReferenceProvider
     */
    public function setObjectReferenceProvider(ObjectReference $objectReferenceProvider)
    {
        $this->objectReferenceProvider = $objectReferenceProvider;
        $this->objectReferenceProvider->setObjects(self::$objects);
    }

    /**
     * @param array|null $specificFiles Array of files in fixtures folder
     * @return array
     */
    public static function getFixturesFiles($specificFiles = [])
    {
        self::createFolderDependencies([
            self::FIXTURE_FOLDER,
            self::IMAGES_FOLDER
        ]);

        if (is_array($specificFiles) && count($specificFiles) > 0) {
            $fixturesFiles = glob(self::FIXTURE_FOLDER . '/{' . implode(',', $specificFiles) . '}.{yml,php}', GLOB_BRACE);
        } else {
            $fixturesFiles = glob(self::FIXTURE_FOLDER . '/*.{yml,php}',GLOB_BRACE);
        }

        usort($fixturesFiles, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        return $fixturesFiles;
    }

    /**
     * @param string $fixtureFile
     */
    public function load($fixtureFile)
    {
        $processors = [
            new UserProcessor(),
            new WorkspaceProcessor(),
            new DocumentProperties()
        ];
        $persister = new PimcorePersister($this->checkPathExists, $this->omitValidation);
        $basename = basename($fixtureFile);
        $loader = new NativeLoader();
        self::$objects[ $basename ] = array_merge(self::$objects,  $loader->load($fixtureFile));
    }

    /**
     * Makes sure all folders are created so glob does not throw any error
     * @param array $folders
     */
    private static function createFolderDependencies($folders)
    {
        foreach ($folders as $folder) {
            if (!is_dir($folder)) {
                File::mkdir($folder);
            }
        }
    }
}
